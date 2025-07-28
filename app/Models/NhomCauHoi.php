<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhomCauHoi extends Model
{
    protected $table = 'nhom_cauhoi';

    protected $fillable = [
        'mau_khaosat_id',
        'ten_nhom',
        'mota',
        'thutu',
        'hienthi_tennhom'
    ];

    protected $casts = [
        'hienthi_tennhom' => 'boolean'
    ];

    /**
     * Quan hệ với mẫu khảo sát
     */
    public function mauKhaoSat()
    {
        return $this->belongsTo(MauKhaoSat::class, 'mau_khaosat_id');
    }

    /**
     * Quan hệ với câu hỏi
     */
    public function cauHoi()
    {
        return $this->hasMany(CauHoiKhaoSat::class, 'nhom_cauhoi_id')
            ->orderBy('thutu');
    }

    /**
     * Scope sắp xếp theo thứ tự
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('thutu');
    }
}