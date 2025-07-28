<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Không cần khai báo policies
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}