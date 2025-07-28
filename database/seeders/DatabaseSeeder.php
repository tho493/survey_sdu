<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DoiTuongKhaoSat;
use App\Models\NamHoc;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Tạo tài khoản admin nếu database trống
        User::firstOrCreate([
            'tendangnhap' => 'tho493',
            'matkhau' => Hash::make('tho493'),
            'hoten' => 'Administrator',
            'email' => 'tho493@admin.com'
        ]);

        // Tạo đối tượng khảo sát
        $doiTuongs = [
            ['ma_doituong' => 'SV', 'ten_doituong' => 'Sinh viên', 'loai_doituong' => 'sinhvien'],
            ['ma_doituong' => 'GV', 'ten_doituong' => 'Giảng viên', 'loai_doituong' => 'giangvien'],
            ['ma_doituong' => 'NV', 'ten_doituong' => 'Nhân viên', 'loai_doituong' => 'nhanvien'],
            ['ma_doituong' => 'DN', 'ten_doituong' => 'Doanh nghiệp', 'loai_doituong' => 'doanhnghiep'],
            ['ma_doituong' => 'SVTN', 'ten_doituong' => 'Sinh viên tốt nghiệp', 'loai_doituong' => 'sinhvien']
        ];

        foreach ($doiTuongs as $dt) {
            DoiTuongKhaoSat::create($dt);
        }

        // Tạo năm học
        NamHoc::create(['namhoc' => '2023-2024']);
        NamHoc::create(['namhoc' => '2024-2025']);
        NamHoc::create(['namhoc' => '2025-2026']);
    }
}