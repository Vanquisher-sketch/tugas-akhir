<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllAsRead()
    {
        // auth()->user() akan memanggil data user yang sedang login.
        // Tanda '?->' memastikan sistem tidak error jika seandainya user belum login.
        auth()->user()?->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}