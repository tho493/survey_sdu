<?php

namespace App\Http\Controllers;

use App\Models\DotKhaoSat;
use App\Models\PhieuKhaoSat;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'dot_dang_hoat_dong' => DotKhaoSat::where('trangthai', 'active')->count(),
            'tong_phieu_thang' => PhieuKhaoSat::whereMonth('created_at', date('m'))->count(),
            'ty_le_hoan_thanh' => $this->getTyLeHoanThanhChung(),
            'dot_sap_ket_thuc' => DotKhaoSat::where('trangthai', 'active')
                ->whereBetween('denngay', [now(), now()->addDays(7)])
                ->count()
        ];

        $dotKhaoSatMoiNhat = DotKhaoSat::with(['mauKhaoSat.doiTuong'])
            ->where('trangthai', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $thongKeTheoDoiTuong = $this->getThongKeTheoDoiTuong();

        return view('dashboard.index', compact('stats', 'dotKhaoSatMoiNhat', 'thongKeTheoDoiTuong'));
    }

    private function getTyLeHoanThanhChung()
    {
        $result = DB::table('phieu_khaosat')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN trangthai = "completed" THEN 1 ELSE 0 END) as completed
            ')
            ->first();

        if ($result->total == 0)
            return 0;

        return round(($result->completed / $result->total) * 100, 2);
    }

    private function getThongKeTheoDoiTuong()
    {
        return DB::table('dot_khaosat as dk')
            ->join('mau_khaosat as mk', 'dk.mau_khaosat_id', '=', 'mk.id')
            ->join('doituong_khaosat as dt', 'mk.ma_doituong', '=', 'dt.ma_doituong')
            ->leftJoin('phieu_khaosat as pk', 'dk.id', '=', 'pk.dot_khaosat_id')
            ->select(
                'dt.ten_doituong',
                DB::raw('COUNT(DISTINCT dk.id) as so_dot'),
                DB::raw('COUNT(pk.id) as tong_phieu')
            )
            ->where('dk.trangthai', 'active')
            ->groupBy('dt.ma_doituong', 'dt.ten_doituong')
            ->get();
    }
}