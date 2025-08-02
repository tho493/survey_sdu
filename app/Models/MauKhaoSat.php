<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MauKhaoSat extends Model
{

    protected $table = 'mau_khaosat';

    protected $fillable = [
        'ten_mau',
        'ma_doituong',
        'mota',
        'version',
        'trangthai',
        'nguoi_tao_id'
    ];

    public function doiTuong()
    {
        return $this->belongsTo(DoiTuongKhaoSat::class, 'ma_doituong', 'ma_doituong');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    public function nhomCauHoi()
    {
        return $this->hasMany(NhomCauHoi::class, 'mau_khaosat_id')->orderBy('thutu');
    }

    public function cauHoi()
    {
        return $this->hasMany(CauHoiKhaoSat::class, 'mau_khaosat_id')->orderBy('thutu');
    }

    public function dotKhaoSat()
    {
        return $this->hasMany(DotKhaoSat::class, 'mau_khaosat_id');
    }
}