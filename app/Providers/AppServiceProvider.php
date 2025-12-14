<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // <-- Pastikan Anda mengimpor class View

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
        View::composer('layouts.navbar', \App\Http\View\Composers\NotificationComposer::class);

        if ($this->app->environment('production')) {
        \URL::forceScheme('https');
        }
    }
}

