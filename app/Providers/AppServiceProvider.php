<?php

namespace App\Providers;

use App\Http\Middleware\DricCpBrandingAuthenticatedInertiaRequests;
use App\Http\Middleware\DricCpBrandingInertiaRequests;
use Illuminate\Support\ServiceProvider;
use Statamic\Http\Middleware\CP\HandleAuthenticatedInertiaRequests;
use Statamic\Http\Middleware\CP\HandleInertiaRequests;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(HandleInertiaRequests::class, DricCpBrandingInertiaRequests::class);
        $this->app->bind(HandleAuthenticatedInertiaRequests::class, DricCpBrandingAuthenticatedInertiaRequests::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
