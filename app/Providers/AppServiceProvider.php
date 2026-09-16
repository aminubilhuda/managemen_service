<?php

namespace App\Providers;

use App\Models\Perusahaan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        View::composer('*', function ($view) {
            static $appPerusahaan = false;
            if ($appPerusahaan === false) {
                try {
                    $appPerusahaan = Perusahaan::first();
                } catch (\Throwable $e) {
                    $appPerusahaan = null;
                }
            }
            $view->with('appPerusahaan', $appPerusahaan);
        });
    }
}
