<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.public', 'pages.contact', 'welcome'], function ($view): void {
            $view->with('footerSettings', SiteSetting::footerData());
        });

        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('demo-request', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}
