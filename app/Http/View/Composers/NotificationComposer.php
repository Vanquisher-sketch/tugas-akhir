<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Variabel ini akan secara otomatis tersedia di view yang ditargetkan
        if (Auth::check()) {
            $view->with('notifications', Auth::user()->unreadNotifications);
        } else {
            $view->with('notifications', collect()); // Kirim koleksi kosong jika user belum login
        }
    }
}
