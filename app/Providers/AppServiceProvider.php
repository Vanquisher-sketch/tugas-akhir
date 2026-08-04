<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL; // <-- 1. Tambahkan import URL ini
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
        // REVISI: Mendaftarkan NotificationComposer
        // Kode ini akan memberitahu Laravel untuk menjalankan composer ini
        // setiap kali view 'layouts.navbar' akan dimuat.
        Paginator::useBootstrap();
        View::composer('layouts.navbar', \App\Http\View\Composers\NotificationComposer::class);

        if ($this->app->environment('production')) {
            // 2. Sekarang panggil URL tanpa tanda backslash (\)
            URL::forceScheme('https'); 
        }
    }
}