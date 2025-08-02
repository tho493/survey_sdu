<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\MauKhaoSat;
use App\Models\DotKhaoSat;
use App\Models\DoiTuongKhaoSat;
use App\Observers\ActivityLogObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Không cần khai báo policies
    ];

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot()
    {
        $this->registerPolicies();

        User::observe(ActivityLogObserver::class);
        MauKhaoSat::observe(ActivityLogObserver::class);
        DotKhaoSat::observe(ActivityLogObserver::class);
        DoiTuongKhaoSat::observe(ActivityLogObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}