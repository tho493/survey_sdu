<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DotKhaoSat extends Model
{
    protected $table = 'dot_khaosat';

    protected $fillable = [
        'ten_dot',
        'mau_khaosat_id',
        'namhoc_id',
        'tungay',
        'denngay',
        'trangthai',
        'mota',
        'nguoi_tao_id'
    ];

    protected $dates = ['tungay', 'denngay'];

    public function mauKhaoSat()
    {
        return $this->belongsTo(MauKhaoSat::class, 'mau_khaosat_id');
    }

    public function namHoc()
    {
        return $this->belongsTo(NamHoc::class, 'namhoc_id');
    }

    public function phieuKhaoSat()
    {
        return $this->hasMany(PhieuKhaoSat::class, 'dot_khaosat_id');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id', 'tendangnhap');
    }

    public function isActive()
    {
        return $this->trangthai === 'active'
            && Carbon::now()->between($this->tungay, $this->denngay);
    }

    public function getTyLeHoanThanh()
    {
        $total = $this->phieuKhaoSat()->count();
        if ($total == 0)
            return 0;

        $completed = $this->phieuKhaoSat()->where('trangthai', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }
}