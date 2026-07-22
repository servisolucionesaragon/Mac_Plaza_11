<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            try {
                $config = \App\Models\Configuracion::actual();
                $view->with('config', $config);
            } catch (\Throwable $e) {
                // tabla no migrada aún u otro error — no romper la app
            }
        });

        if (Schema::hasTable('configuracion')) {
            try {
                $tz = \App\Models\Configuracion::actual()->timezone;
                if ($tz) {
                    config(['app.timezone' => $tz]);
                    date_default_timezone_set($tz);
                }
            } catch (\Throwable $e) {
                // no romper boot
            }
        }
    }
}
