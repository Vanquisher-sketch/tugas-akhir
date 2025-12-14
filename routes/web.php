<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// --- CONTROLLER UTAMA & AUTENTIKASI ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

// --- CONTROLLER DINAMIS (SATU UNTUK SEMUA) ---
use App\Http\Controllers\TanahController;
use App\Http\Controllers\PeralatanController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\JalanController;
use App\Http\Controllers\RusakController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BmdController;
use App\Http\Controllers\PajakController;

// --- CONTROLLER TEST WA (Tambahan Baru) ---
use App\Http\Controllers\TestWaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- RUTE PUBLIK & AUTENTIKASI ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'registerView'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');


// --- RUTE YANG MEMBUTUHKAN AUTENTIKASI ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Rute Notifikasi
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Rute Manajemen User (Admin)
    Route::resource('user', UserController::class);

    // --- RUTE DINAMIS UNTUK SEMUA MODUL ---
    Route::prefix('{lokasi}')
        ->whereIn('lokasi', ['tawang', 'lengkongsari', 'cikalang', 'empang', 'kahuripan', 'tawangsari'])
        ->name('lokasi.')
        ->middleware('lokasi.access')
        ->group(function () {

            // Route untuk export Excel (Global)
            Route::get('export/{menu}', [ExportController::class, 'export'])->name('export.excel');

            // 1. Route Ruangan
            Route::get('room/print', [RoomController::class, 'print'])->name('room.print');
            Route::resource('room', RoomController::class);

            // 2. Route Inventaris (Bersarang di Room)
            Route::get('room/{room}/inventaris/print', [InventarisController::class, 'print'])->name('inventaris.print');
            Route::post('room/{room}/inventaris/{inventari}/move', [InventarisController::class, 'move'])->name('inventaris.move');
            Route::resource('room/{room}/inventaris', InventarisController::class)->names('inventaris');

            // 3. KIB A (Tanah)
            Route::get('tanah/print', [TanahController::class, 'print'])->name('tanah.print');
            Route::resource('tanah', TanahController::class);

            // 4. KIB B (Peralatan)
            Route::get('peralatan/print', [PeralatanController::class, 'print'])->name('peralatan.print');
            Route::resource('peralatan', PeralatanController::class);

            // 5. KIB C (Gedung)
            Route::get('gedung/print', [GedungController::class, 'print'])->name('gedung.print');
            Route::resource('gedung', GedungController::class);

            // 6. KIB D (Jalan)
            Route::get('jalan/print', [JalanController::class, 'print'])->name('jalan.print');
            Route::resource('jalan', JalanController::class);

            // 7. Aset Rusak
            Route::get('rusak/print', [RusakController::class, 'print'])->name('rusak.print');
            Route::resource('rusak', RusakController::class);

            // 8. PENGGUNAAN BMD
            Route::get('bmd/print', [BmdController::class, 'print'])->name('bmd.print');
            Route::resource('bmd', BmdController::class);

            // 9. MONITORING PAJAK
            Route::get('pajak/print', [PajakController::class, 'print'])->name('pajak.print');
            
            // [BARU] Route khusus untuk tombol Kirim WA Reminder (Manual)
            // Nanti di form view pajak action-nya ke route ini
            Route::post('pajak/kirim-reminder', [PajakController::class, 'kirimReminderManual'])->name('pajak.kirim_reminder');
            
            Route::resource('pajak', PajakController::class)->only(['index', 'edit', 'update']);
        });
});


// --- KHUSUS UNTUK VERCEL CRON ---
Route::get('/run-scheduler', function () {
    Artisan::call('schedule:run');
    return 'Scheduler dijalankan!';
});


// --- AREA TESTING & DEVELOPMENT ---

// [BARU] Route Tes Koneksi WA (Hello World)
// Akses di browser: http://localhost:8000/tes-wa
// Route::get('/tes-wa', [TestWaController::class, 'kirimTes']);