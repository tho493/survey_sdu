<?php

namespace App\Http\Controllers;

use App\Models\DotKhaoSat;
use App\Models\PhieuKhaoSat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KhaoSatExport;

class BaoCaoController extends Controller
{
    public function index(Request $request)
    {
        $dotKhaoSats = DotKhaoSat::with(['mauKhaoSat.doiTuong'])
            ->where('trangthai', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bao-cao.index', compact('dotKhaoSats'));
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

        return view('bao-cao.dot-khao-sat', compact('dotKhaoSat', 'tongQuan', 'thongKeCauHoi'));
    }

    private function thongKeCauHoi($dotKhaoSatId, $cauHoi)
    {
        $result = [];

        if (in_array($cauHoi->loai_cauhoi, ['single_choice', 'multiple_choice', 'likert'])) {
            // Thống kê cho câu hỏi lựa chọn
            $data = DB::table('phieu_khaosat_chitiet as pkc')
                ->join('phieu_khaosat as pk', 'pkc.phieu_khaosat_id', '=', 'pk.id')
                ->join('phuongan_traloi as pt', 'pkc.phuongan_id', '=', 'pt.id')
                ->where('pk.dot_khaosat_id', $dotKhaoSatId)
                ->where('pkc.cauhoi_id', $cauHoi->id)
                ->groupBy('pt.id', 'pt.noidung')
                ->select(
                    'pt.noidung',
                    DB::raw('COUNT(*) as so_luong'),
                    DB::raw('ROUND(COUNT(*) * 100.0 / (
                        SELECT COUNT(DISTINCT phieu_khaosat_id) 
                        FROM phieu_khaosat_chitiet 
                        WHERE cauhoi_id = ' . $cauHoi->id . '
                    ), 2) as ty_le')
                )
                ->get();

            $result['type'] = 'chart';
            $result['data'] = $data;
        } elseif ($cauHoi->loai_cauhoi == 'text') {
            // Lấy một số câu trả lời mẫu
            $data = DB::table('phieu_khaosat_chitiet as pkc')
                ->join('phieu_khaosat as pk', 'pkc.phieu_khaosat_id', '=', 'pk.id')
                ->where('pk.dot_khaosat_id', $dotKhaoSatId)
                ->where('pkc.cauhoi_id', $cauHoi->id)
                ->whereNotNull('pkc.giatri_text')
                ->select('pkc.giatri_text')
                ->limit(10)
                ->get();

            $result['type'] = 'text';
            $result['data'] = $data;
        }

        return $result;
    }

    private function getThoiGianTraLoiTrungBinh($dotKhaoSat)
    {
        $avg = $dotKhaoSat->phieuKhaoSat()
            ->where('trangthai', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, thoigian_batdau, thoigian_hoanthanh)) as avg_time')
            ->first();

        return $avg->avg_time ? round($avg->avg_time) . ' phút' : 'N/A';
    }

    public function export(DotKhaoSat $dotKhaoSat)
    {
        return Excel::download(
            new KhaoSatExport($dotKhaoSat),
            'khao-sat-' . $dotKhaoSat->id . '.xlsx'
        );
    }
}