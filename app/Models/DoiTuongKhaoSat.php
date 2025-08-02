<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoiTuongKhaoSat extends Model
{
    protected $table = 'doituong_khaosat';

    protected $fillable = [
        'ma_doituong',
        'ten_doituong',
        'mota',
        'trangthai'
    ];

    public function mauKhaoSat()
    {
        return $this->hasMany(MauKhaoSat::class, 'ma_doituong', 'ma_doituong');
    }
}