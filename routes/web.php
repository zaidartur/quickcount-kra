<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/statistik', function () {
    return Inertia::render('Statistik');
})->middleware(['auth', 'verified'])->name('statistik');

Route::get('/setting', function () {
    return Inertia::render('Setting');
})->middleware(['auth', 'verified'])->name('setting');

Route::middleware('auth')->group(function () {
    Route::prefix('/data-wilayah')->group(function() {
        Route::get('/', [DataController::class, 'wilayah'])->name('wilayah');
        Route::get('/kecamatan', [DataController::class, 'list_kecamatan'])->name('wilayah.listkec');

        Route::post('/kecamatan', [DataController::class, 'add_kecamatan'])->name('wilayah.kec');
        Route::post('/desa', [DataController::class, 'add_desa'])->name('wilayah.desa');
        Route::post('/hapus-kecamatan', [DataController::class, 'delete_kecamatan'])->name('wilayah.delkec');
        Route::post('/hapus-desa', [DataController::class, 'delete_desa'])->name('wilayah.deldesa');
    });

    Route::prefix('daftar-pemilih-tetap')->group(function() {
        Route::get('/', [DataController::class, 'dpt'])->name('dpt');

        Route::post('/import-dpt', [DataController::class, 'import_dpt'])->name('dpt.import');
        Route::post('/simpan-dpt', [DataController::class, 'add_dpt'])->name('dpt.add');
        Route::post('/hapus-dpt', [DataController::class, 'delete_dpt'])->name('dpt.drop');
    });

    Route::prefix('setting')->group(function() {
        Route::get('/', [SettingController::class, 'view'])->name('setting');

        Route::post('/update-config', [SettingController::class, 'update_config'])->name('config.update');
        Route::post('/tambah-paslon', [SettingController::class, 'add_paslon'])->name('paslon.add');
        Route::post('/hapus-paslon', [SettingController::class, 'delete_paslon'])->name('paslon.drop');
    });

    Route::prefix('data-user')->group(function() {
        Route::get('/', [UserController::class, 'view'])->name('users');
    });

    Route::get('/download-template/{data}', [DataController::class, 'template_download'])->name('template');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
