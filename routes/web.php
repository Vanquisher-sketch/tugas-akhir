<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// --- CONTROLLER UTAMA & AUTENTIKASI ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

// --- CONTROLLER DINAMIS ---
use App\Http\Controllers\TanahController;
use App\Http\Controllers\PeralatanController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\JalanController;
use App\Http\Controllers\RusakController;
use App\Http\Controllers\RuanganController; // 🌟 REVISI: Menggunakan RuanganController
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BmdController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\PegawaiController;

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
    Route::resource('user', UserController::class)->parameters(['user' => 'id']);

    // --- RUTE DINAMIS UNTUK SEMUA MODUL (PANDAWA) ---
    Route::prefix('{lokasi}')
        ->whereIn('lokasi', ['tawang', 'lengkongsari', 'cikalang', 'empang', 'kahuripan', 'tawangsari'])
        ->name('lokasi.')
        ->middleware('lokasi.access') // Pastikan middleware ini sudah ada ya!
        ->group(function () {

            // Route untuk export Excel (Global)
            Route::get('export/{menu}', [ExportController::class, 'export'])->name('export.excel');

            // 1. Route Ruangan (🌟 REVISI: Room diubah menjadi Ruangan)
            Route::get('ruangan/print', [RuanganController::class, 'print'])->name('ruangan.print');
            Route::resource('ruangan', RuanganController::class)->parameters([
                'ruangan' => 'kode_ruangan'
            ]);

            // 2. Route Inventaris
            Route::get('ruangan/{kode_ruangan}/inventaris/autocomplete', [InventarisController::class, 'autocomplete'])->name('inventaris.autocomplete');
            Route::get('ruangan/{kode_ruangan}/inventaris/print', [InventarisController::class, 'print'])->name('inventaris.print');
            Route::post('ruangan/{kode_ruangan}/inventaris/{inv_kode_barang}/move', [InventarisController::class, 'move'])->name('inventaris.move');
            
            // Detail & Cetak Label QR Code Satuan untuk Inventaris Ruangan
            Route::get('ruangan/{kode_ruangan}/inventaris/{kode_barang}/detail', [InventarisController::class, 'showDetail'])->name('inventaris.detail');

            // 🌟 REVISI: Resource Inventaris (Menyesuaikan parameter string eksplisit)
            Route::resource('ruangan.inventaris', InventarisController::class)->parameters([
                'ruangan' => 'kode_ruangan',
                'inventaris' => 'inv_kode_barang'
            ])->names('inventaris');

            // 3. KIB A (Tanah)
            Route::get('tanah/autocomplete', [TanahController::class, 'autocomplete'])->name('tanah.autocomplete');
            Route::get('tanah/print', [TanahController::class, 'print'])->name('tanah.print');
            Route::resource('tanah', TanahController::class)->parameters(['tanah' => 'kode_barang']);

            // 4. KIB B (Peralatan)
            Route::get('peralatan/autocomplete', [PeralatanController::class, 'autocomplete'])->name('peralatan.autocomplete');
            Route::get('peralatan/print', [PeralatanController::class, 'print'])->name('peralatan.print');
            
            // Detail & Cetak Label QR Code Satuan untuk Peralatan KIB B
            Route::get('peralatan/{kode_barang}/detail', [PeralatanController::class, 'showDetail'])->name('peralatan.detail');

            Route::resource('peralatan', PeralatanController::class)->parameters(['peralatan' => 'kode_barang']);

            // 5. KIB C (Gedung)
            Route::get('gedung/autocomplete', [GedungController::class, 'autocomplete'])->name('gedung.autocomplete');
            Route::get('gedung/print', [GedungController::class, 'print'])->name('gedung.print');
            Route::resource('gedung', GedungController::class)->parameters(['gedung' => 'kode_barang']);

            // 6. KIB D (Jalan)
            Route::get('jalan/autocomplete', [JalanController::class, 'autocomplete'])->name('jalan.autocomplete');
            Route::get('jalan/print', [JalanController::class, 'print'])->name('jalan.print');
            Route::resource('jalan', JalanController::class)->parameters(['jalan' => 'kode_barang']);

            // 7. Aset Rusak (🌟 REVISI: Menggunakan ID biasa, bukan no_id_pemda)
            Route::get('rusak/autocomplete', [RusakController::class, 'autocomplete'])->name('rusak.autocomplete');
            Route::get('rusak/print', [RusakController::class, 'print'])->name('rusak.print');
            Route::resource('rusak', RusakController::class)->parameters(['rusak' => 'id']);

            // 8. PENGGUNAAN BMD
            Route::get('bmd/print', [BmdController::class, 'print'])->name('bmd.print');
            Route::get('bmd/cari-pegawai', [BmdController::class, 'cariPegawaiByNip'])->name('bmd.cari-pegawai');
            Route::get('bmd/{id}/buka-pdf', [BmdController::class, 'bukaPdf'])->name('bmd.buka_pdf');
            Route::resource('bmd', BmdController::class)->parameters(['bmd' => 'id']);

            // 9. MONITORING PAJAK
            Route::get('pajak/print', [PajakController::class, 'print'])->name('pajak.print');
            Route::post('pajak/kirim-reminder', [PajakController::class, 'kirimReminderManual'])->name('pajak.kirim_reminder');
            Route::resource('pajak', PajakController::class)->only(['index', 'edit', 'update'])->parameters(['pajak' => 'id']);

            // 10. RUTE ARSIP
            Route::prefix('arsip')->name('arsip.')->group(function() {
                Route::get('/', [ArsipController::class, 'index'])->name('index'); 
                Route::get('/{kategori}', [ArsipController::class, 'show'])->name('show'); 
                Route::post('/{kategori}/{kode}/restore', [ArsipController::class, 'restore'])->name('restore'); 
                Route::delete('/{kategori}/{kode}/permanen', [ArsipController::class, 'forceDelete'])->name('permanen'); 
            });

            // 11. RUTE DATA PEGAWAI (🌟 REVISI: Penyelarasan NIP Pegawai)
            Route::resource('pegawai', PegawaiController::class)->except(['show'])->parameters(['pegawai' => 'nip']);
        });
});

// --- KHUSUS UNTUK VERCEL CRON ---
Route::get('/run-scheduler', function () {
    Artisan::call('schedule:run');
    return 'Scheduler dijalankan!';
});
