<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DotKhaoSat extends Model
{
    protected $table = 'dot_khaosat';
    public $incrementing = false;
    protected $keyType = 'string';

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

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

    /**
     * Kiểm tra xem đợt khảo sát có phải là bản nháp không.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->trangthai === 'draft';
    }

    /**
     * Kiểm tra xem đợt khảo sát có đang trong thời gian hoạt động hay không.
     *
     * @return bool
     */
    public function isInActivePeriod(): bool
    {
        return Carbon::now()->between(
            $this->tungay->startOfDay(),
            $this->denngay->endOfDay()
        );
    }

    /**
     * Kiểm tra xem đợt khảo sát đã bị đóng (dù còn hạn hay không).
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->trangthai === 'closed';
    }

    /**
     * Kiểm tra xem đợt khảo sát đã hết hạn (quá ngày kết thúc).
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return Carbon::now()->gt($this->denngay->endOfDay());
    }

    /**
     * Kiểm tra xem đợt khảo sát chưa tới ngày bắt đầu.
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return Carbon::now()->lt($this->tungay->startOfDay());
    }
}