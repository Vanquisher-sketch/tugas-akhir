<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Path untuk 'auth' yang sudah kita perbaiki sebelumnya.
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,

            // --- INI JUGA DIPERBAIKI ---
            // Path untuk 'guest' diubah agar sesuai dengan Laravel versi baru.
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            
            // Middleware hak akses lokasi kita.
            'lokasi.access' => \App\Http\Middleware\CheckLokasiAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

