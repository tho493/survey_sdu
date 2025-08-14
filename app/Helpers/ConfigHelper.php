<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!function_exists('db_env')) {
    /**
     * Lấy giá trị của một biến môi trường, ưu tiên từ database,
     * sau đó đến file .env, cuối cùng là giá trị mặc định.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function db_env($key, $default = null)
    {
        // 1. Tải tất cả config từ DB (có cache)
        $dbConfigs = Cache::rememberForever('db_env_configs', function () {
            if (Schema::hasTable('cau_hinh_dich_vu')) { // Thay bằng tên bảng của bạn
                return DB::table('cau_hinh_dich_vu')->pluck('giatri', 'ma_cauhinh')->all();
            }
            return [];
        });

        // 2. Trả về giá trị từ DB nếu tồn tại
        if (array_key_exists($key, $dbConfigs) && $dbConfigs[$key] !== null) {
            return $dbConfigs[$key];
        }

        // 3. Nếu không có, trả về giá trị từ hàm env() gốc
        return env($key, $default);
    }
}