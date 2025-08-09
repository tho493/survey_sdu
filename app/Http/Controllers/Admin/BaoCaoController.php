<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DotKhaoSat;
use App\Models\PhieuKhaoSat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KhaoSatExport;
use Illuminate\Support\Str;
use PDF;

class BaoCaoController extends Controller
{
    public function index()
    {
        $dotKhaoSats = DotKhaoSat::with(['mauKhaoSat'])
            ->withCount([
                'phieuKhaoSat as phieu_hoan_thanh' => function ($query) {
                    $query->where('trangthai', 'completed');
                }
            ])
            ->where('trangthai', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Thống kê tổng quan
        $tongQuan = [
            'tong_dot' => DotKhaoSat::count(),
            'dot_active' => DotKhaoSat::where('trangthai', 'active')->count(),
            'tong_phieu' => PhieuKhaoSat::count(),
            'phieu_hoanthanh' => PhieuKhaoSat::where('trangthai', 'completed')->count(),
        ];

        // Thống kê theo tháng (12 tháng gần nhất)
        $thongKeThang = $this->getThongKeThang();

        // Thống kê theo mẫu khảo sát
        $thongKeMauKhaoSat = DB::table('dot_khaosat as dk')
            ->join('mau_khaosat as mk', 'dk.mau_khaosat_id', '=', 'mk.id')
            ->leftJoin('phieu_khaosat as pk', function ($join) {
                $join->on('dk.id', '=', 'pk.dot_khaosat_id')
                    ->where('pk.trangthai', '=', 'completed');
            })
            ->where('dk.trangthai', '!=', 'draft')
            ->groupBy('dk.mau_khaosat_id', 'mk.ten_mau')
            ->select(
                'mk.ten_mau',
                DB::raw('COUNT(DISTINCT dk.id) as so_dot'),
                DB::raw('COUNT(pk.id) as phieu_hoanthanh')
            )
            ->get()
            ->map(function ($item) {
                return [
                    'ten_mau' => $item->ten_mau ?? 'N/A',
                    'phieu_hoanthanh' => $item->phieu_hoanthanh,
                ];
            });

        return view('admin.bao-cao.index', compact(
            'dotKhaoSats',
            'tongQuan',
            'thongKeThang',
            'thongKeMauKhaoSat'
        ));
    }

    public function dotKhaoSat(DotKhaoSat $dotKhaoSat)
    {
        $dotKhaoSat->load(['mauKhaoSat.cauHoi.phuongAnTraLoi']);

        // Thống kê tổng quan
        $tongQuan = [
            'tong_phieu' => $dotKhaoSat->phieuKhaoSat()->count(),
            'phieu_hoan_thanh' => $dotKhaoSat->phieuKhaoSat()->where('trangthai', 'completed')->count(),
            'ty_le' => $dotKhaoSat->getTyLeHoanThanh(),
            'thoi_gian_tb' => $this->getThoiGianTraLoiTrungBinh($dotKhaoSat)
        ];

        // Thống kê từng câu hỏi
        $thongKeCauHoi = [];
        foreach ($dotKhaoSat->mauKhaoSat->cauHoi as $cauHoi) {
            $thongKeCauHoi[$cauHoi->id] = $this->thongKeCauHoi($dotKhaoSat->id, $cauHoi);
        }

        // Thống kê theo ngày
        $thongKeTheoNgay = $this->getThongKeTheoNgay($dotKhaoSat);

        // Danh sách phiếu khảo sát (có thể phân trang nếu nhiều)
        $danhSachPhieu = $dotKhaoSat->phieuKhaoSat()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bao-cao.dot-khao-sat', compact(
            'dotKhaoSat',
            'tongQuan',
            'thongKeCauHoi',
            'thongKeTheoNgay',
            'danhSachPhieu'
        ));
    }

    private function thongKeCauHoi($dotKhaoSatId, $cauHoi)
    {
        $result = [
            'type' => 'text',
            'data' => []
        ];

        if (in_array($cauHoi->loai_cauhoi, ['single_choice', 'multiple_choice', 'likert', 'rating'])) {
            // Thống kê cho câu hỏi lựa chọn
            $data = DB::table('phieu_khaosat_chitiet as pkc')
                ->join('phieu_khaosat as pk', 'pkc.phieu_khaosat_id', '=', 'pk.id')
                ->leftJoin('phuongan_traloi as pt', 'pkc.phuongan_id', '=', 'pt.id')
                ->where('pk.dot_khaosat_id', $dotKhaoSatId)
                ->where('pkc.cauhoi_id', $cauHoi->id)
                ->where('pk.trangthai', 'completed')
                ->groupBy('pkc.phuongan_id', 'pt.noidung')
                ->select(
                    'pt.noidung',
                    'pkc.phuongan_id',
                    DB::raw('COUNT(*) as so_luong')
                )
                ->orderBy('pkc.phuongan_id')
                ->get();

            // Tính tổng để tính phần trăm
            $total = $data->sum('so_luong');

            // Thêm phần trăm
            $data = $data->map(function ($item) use ($total) {
                $item->ty_le = $total > 0 ? round(($item->so_luong / $total) * 100, 2) : 0;
                return $item;
            });

            $result['type'] = 'chart';
            $result['data'] = $data;
            $result['total'] = $total;

        } elseif ($cauHoi->loai_cauhoi == 'text') {
            // Lấy một số câu trả lời mẫu cho câu hỏi text
            $data = DB::table('phieu_khaosat_chitiet as pkc')
                ->join('phieu_khaosat as pk', 'pkc.phieu_khaosat_id', '=', 'pk.id')
                ->where('pk.dot_khaosat_id', $dotKhaoSatId)
                ->where('pkc.cauhoi_id', $cauHoi->id)
                ->where('pk.trangthai', 'completed')
                ->whereNotNull('pkc.giatri_text')
                ->where('pkc.giatri_text', '!=', '')
                ->select('pkc.giatri_text')
                ->limit(20)
                ->get();

            $result['type'] = 'text';
            $result['data'] = $data;
            $result['total'] = DB::table('phieu_khaosat_chitiet as pkc')
                ->join('phieu_khaosat as pk', 'pkc.phieu_khaosat_id', '=', 'pk.id')
                ->where('pk.dot_khaosat_id', $dotKhaoSatId)
                ->where('pkc.cauhoi_id', $cauHoi->id)
                ->where('pk.trangthai', 'completed')
                ->whereNotNull('pkc.giatri_text')
                ->where('pkc.giatri_text', '!=', '')
                ->count();
        }

        return $result;
    }

    private function getThoiGianTraLoiTrungBinh($dotKhaoSat)
    {
        $avg = $dotKhaoSat->phieuKhaoSat()
            ->where('trangthai', 'completed')
            ->whereNotNull('thoigian_hoanthanh')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, thoigian_batdau, thoigian_hoanthanh)) as avg_time')
            ->first();

        return $avg && $avg->avg_time ? round($avg->avg_time) . ' phút' : 'N/A';
    }

    private function getThongKeThang()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = PhieuKhaoSat::whereYear('thoigian_batdau', $date->year)
                ->whereMonth('thoigian_batdau', $date->month)
                ->where('trangthai', 'completed')
                ->count();

            $data[] = [
                'thang' => $date->format('m/Y'),
                'so_luong' => $count
            ];
        }

        return $data;
    }

    private function getThongKeTheoNgay($dotKhaoSat)
    {
        return DB::table('phieu_khaosat')
            ->where('dot_khaosat_id', $dotKhaoSat->id)
            ->where('trangthai', 'completed')
            ->selectRaw('DATE(thoigian_hoanthanh) as ngay, COUNT(*) as so_luong')
            ->groupBy('ngay')
            ->orderBy('ngay')
            ->get();
    }

    public function export(Request $request, DotKhaoSat $dotKhaoSat)
    {
        $format = $request->input('format', 'excel');
        $fileName = 'bao-cao-' . Str::slug($dotKhaoSat->ten_dot) . '-' . date('Ymd');

        if ($format == 'excel') {
            // Sử dụng lớp KhaoSatExport đã tạo
            return Excel::download(new KhaoSatExport($dotKhaoSat), $fileName . '.xlsx');
        }

        if ($format == 'pdf') {
            // Lấy dữ liệu cần thiết cho view PDF
            $tongQuan = [
                'tong_phieu' => $dotKhaoSat->phieuKhaoSat()->count(),
                'phieu_hoan_thanh' => $dotKhaoSat->phieuKhaoSat()->where('trangthai', 'completed')->count(),
                'ty_le' => $dotKhaoSat->getTyLeHoanThanh(),
                'thoi_gian_tb' => $this->getThoiGianTraLoiTrungBinh($dotKhaoSat)
            ];
            $thongKeCauHoi = [];
            foreach ($dotKhaoSat->mauKhaoSat->cauHoi as $cauHoi) {
                $thongKeCauHoi[$cauHoi->id] = $this->thongKeTungCauHoi($dotKhaoSat->id, $cauHoi);
            }

            // Tải view PDF với dữ liệu
            $pdf = PDF::loadView('admin.bao-cao.pdf', compact('dotKhaoSat', 'tongQuan', 'thongKeCauHoi'));

            // Có thể set khổ giấy và hướng
            $pdf->setPaper('a4', 'portrait');

            // Tải file về
            return $pdf->download($fileName . '.pdf');
        }

        return back()->with('error', 'Định dạng xuất không hợp lệ.');
    }

    private function thongKeTungCauHoi($dotKhaoSatId, $cauHoi)
    {
        // Lấy danh sách ID của các phiếu đã hoàn thành để đảm bảo chỉ thống kê trên dữ liệu hợp lệ
        $completedSurveyIds = DB::table('phieu_khaosat')
            ->where('dot_khaosat_id', $dotKhaoSatId)
            ->where('trangthai', 'completed')
            ->pluck('id');

        // Nếu không có phiếu nào hoàn thành, trả về kết quả rỗng
        if ($completedSurveyIds->isEmpty()) {
            return ['type' => 'empty', 'data' => collect(), 'total' => 0];
        }

        // Tạo câu query cơ sở
        $baseQuery = DB::table('phieu_khaosat_chitiet')
            ->where('phieu_khaosat_chitiet.cauhoi_id', $cauHoi->id)
            ->whereIn('phieu_khaosat_id', $completedSurveyIds);

        // Xử lý tùy theo loại câu hỏi
        switch ($cauHoi->loai_cauhoi) {

            case 'single_choice':
            case 'multiple_choice':
            case 'likert':
            case 'rating':

                // Lấy dữ liệu đếm số lượt chọn cho mỗi phương án
                $data = (clone $baseQuery)
                    ->leftJoin('phuongan_traloi as pa', 'phieu_khaosat_chitiet.phuongan_id', '=', 'pa.id')
                    ->groupBy('phieu_khaosat_chitiet.phuongan_id', 'pa.noidung')
                    ->select(
                        'pa.noidung',
                        DB::raw('COUNT(phieu_khaosat_chitiet.id) as so_luong')
                    )
                    ->orderBy('so_luong', 'desc')
                    ->get();

                // Tính tổng số lượt trả lời cho câu hỏi này
                $totalResponses = $data->sum('so_luong');

                // Thêm tỷ lệ phần trăm vào mỗi phương án
                $data = $data->map(function ($item) use ($totalResponses) {
                    $item->ty_le = $totalResponses > 0 ? round(($item->so_luong / $totalResponses) * 100, 2) : 0;
                    // Đảm bảo 'noidung' không bị null (trường hợp dữ liệu rác)
                    $item->noidung = $item->noidung ?? 'Không xác định';
                    return $item;
                });

                return [
                    'type' => 'chart', // Dữ liệu phù hợp để vẽ biểu đồ
                    'data' => $data,
                    'total' => $totalResponses,
                ];

            case 'text':
                // Lấy các câu trả lời dạng văn bản
                $data = (clone $baseQuery)
                    ->whereNotNull('giatri_text')
                    ->where('giatri_text', '!=', '')
                    ->select('giatri_text')
                    ->limit(20) // Giới hạn 20 câu trả lời mẫu để hiển thị
                    ->pluck('giatri_text'); // Chỉ lấy cột giatri_text

                return [
                    'type' => 'text', // Dữ liệu dạng danh sách văn bản
                    'data' => $data,
                    'total' => (clone $baseQuery)->whereNotNull('giatri_text')->where('giatri_text', '!=', '')->count(),
                ];

            case 'number':
                // Thống kê cho câu hỏi dạng số (min, max, avg)
                $stats = (clone $baseQuery)
                    ->whereNotNull('giatri_number')
                    ->selectRaw('
                        COUNT(id) as total,
                        MIN(giatri_number) as min,
                        MAX(giatri_number) as max,
                        AVG(giatri_number) as avg,
                        STDDEV(giatri_number) as stddev
                    ')->first();

                return [
                    'type' => 'number_stats',
                    'data' => $stats,
                    'total' => $stats->total ?? 0,
                ];

            default:
                // Các loại khác (date, etc.) trả về dạng danh sách
                $data = (clone $baseQuery)->limit(20)->get();
                return [
                    'type' => 'list',
                    'data' => $data,
                    'total' => (clone $baseQuery)->count(),
                ];
        }
    }
}