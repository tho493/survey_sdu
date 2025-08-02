<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DotKhaoSat;
use App\Models\PhieuKhaoSat;
use App\Models\DoiTuongKhaoSat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KhaoSatExport;

class BaoCaoController extends Controller
{
    public function index()
    {
        $dotKhaoSats = DotKhaoSat::with(['mauKhaoSat.doiTuong'])
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

        // Thống kê theo đối tượng
        $thongKeDoiTuong = $this->getThongKeDoiTuong();

        return view('admin.bao-cao.index', compact(
            'dotKhaoSats',
            'tongQuan',
            'thongKeThang',
            'thongKeDoiTuong'
        ));
    }

    public function dotKhaoSat(DotKhaoSat $dotKhaoSat)
    {
        $dotKhaoSat->load(['mauKhaoSat.cauHoi.phuongAnTraLoi', 'mauKhaoSat.doiTuong']);

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

        return view('admin.bao-cao.dot-khao-sat', compact('dotKhaoSat', 'tongQuan', 'thongKeCauHoi', 'thongKeTheoNgay'));
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

    private function getThongKeDoiTuong()
    {
        return DB::table('phieu_khaosat as pk')
            ->join('dot_khaosat as dk', 'pk.dot_khaosat_id', '=', 'dk.id')
            ->join('mau_khaosat as mk', 'dk.mau_khaosat_id', '=', 'mk.id')
            ->join('doituong_khaosat as dt', 'mk.ma_doituong', '=', 'dt.ma_doituong')
            ->select(
                'dt.ten_doituong',
                DB::raw('COUNT(*) as tong_phieu'),
                DB::raw('SUM(CASE WHEN pk.trangthai = "completed" THEN 1 ELSE 0 END) as phieu_hoanthanh')
            )
            ->groupBy('dt.ma_doituong', 'dt.ten_doituong')
            ->get();
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
        // Implement export functionality
        return Excel::download(new KhaoSatExport($dotKhaoSat), 'admin.bao-cao-khao-sat-' . $dotKhaoSat->id . '.xlsx');
    }
}