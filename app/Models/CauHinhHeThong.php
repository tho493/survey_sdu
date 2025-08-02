<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CauHinhHeThong extends Model
{
    protected $table = 'cau_hinh_dich_vu';

    protected $fillable = [
        'ma_cauhinh',
        'giatri',
        'mota',
        'nhom_cauhinh'
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache khi cập nhật
        static::saved(function () {
            Cache::forget('system_configs');
        });
    }

    /**
     * Lấy giá trị cấu hình
     */
    public static function get($key, $default = null)
    {
        $configs = Cache::remember('system_configs', 3600, function () {
            return self::pluck('giatri', 'ma_cauhinh')->toArray();
        });

        return $configs[$key] ?? $default;
    }

    /**
     * Set giá trị cấu hình
     */
    public static function set($key, $value)
    {
        self::updateOrCreate(
            ['ma_cauhinh' => $key],
            ['giatri' => $value]
        );

        Cache::forget('system_configs');
    }

    /**
     * Lấy cấu hình theo nhóm
     */
    public static function getByGroup($group)
    {
        return self::where('nhom_cauhinh', $group)
            ->pluck('giatri', 'ma_cauhinh')
            ->toArray();
    }
}