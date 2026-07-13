<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menandai semua notifikasi yang belum dibaca sebagai sudah dibaca (Mark All as Read).
     * Bekerja otomatis menyesuaikan Primary Key 'user_id' dari Auth::user().
     */
    public function markAllAsRead()
    {
        // Pastikan ada user yang login dan memiliki notifikasi belum dibaca
        if (Auth::check() && Auth::user()->unreadNotifications->count() > 0) {
            Auth::user()->unreadNotifications->markAsRead();
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}