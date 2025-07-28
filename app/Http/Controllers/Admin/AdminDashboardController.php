<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DotKhaoSat;
use App\Models\PhieuKhaoSat;
use App\Models\MauKhaoSat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $stats = [
            'total_users' => User::count(),
            'active_surveys' => DotKhaoSat::where('trangthai', 'active')->count(),
            'total_responses' => PhieuKhaoSat::whereMonth('created_at', date('m'))->count(),
            'total_templates' => MauKhaoSat::where('trangthai', 'active')->count(),
        ];

        // Biểu đồ phản hồi 7 ngày gần nhất
        $responseChart = $this->getResponseChart();

        // Hoạt động gần đây
        $recentActivities = $this->getRecentActivities();

        // Thống kê theo đối tượng
        $objectStats = $this->getObjectStats();

        // Người dùng hoạt động
        $activeUsers = User::where('last_login', '>=', Carbon::now()->subDays(7))
            ->orderBy('last_login', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'responseChart',
            'recentActivities',
            'objectStats',
            'activeUsers'
        ));
    }

    private function getResponseChart()
    {
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        $data = PhieuKhaoSat::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('d/m')),
            'data' => $dates->map(fn($date) => $data->get($date, 0))
        ];
    }

    private function getRecentActivities()
    {
        return DB::table('lichsu_thaydoi')
            ->join('taikhoan', 'lichsu_thaydoi.nguoi_thuchien_id', '=', 'taikhoan.id')
            ->select(
                'lichsu_thaydoi.*',
                'taikhoan.hoten as nguoi_thuchien'
            )
            ->orderBy('lichsu_thaydoi.thoigian', 'desc')
            ->take(10)
            ->get();
    }

    private function getObjectStats()
    {
        return DB::table('dot_khaosat as dk')
            ->join('mau_khaosat as mk', 'dk.mau_khaosat_id', '=', 'mk.id')
            ->join('doituong_khaosat as dt', 'mk.ma_doituong', '=', 'dt.ma_doituong')
            ->leftJoin('phieu_khaosat as pk', 'dk.id', '=', 'pk.dot_khaosat_id')
            ->select(
                'dt.ten_doituong',
                'dt.loai_doituong',
                DB::raw('COUNT(DISTINCT dk.id) as total_surveys'),
                DB::raw('COUNT(pk.id) as total_responses')
            )
            ->groupBy('dt.ma_doituong', 'dt.ten_doituong', 'dt.loai_doituong')
            ->get();
    }
}