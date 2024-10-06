<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\ProfileController;
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

// Route::get('/data-wilayah', function () {
//     return Inertia::render('Wilayah');
// })->middleware(['auth', 'verified'])->name('wilayah');

Route::get('/daftar-pemilih-tetap', function () {
    return Inertia::render('Pemilih');
})->middleware(['auth', 'verified'])->name('dpt');

Route::middleware('auth')->group(function () {
    Route::get('/data-wilayah', [DataController::class, 'wilayah'])->name('wilayah');
    Route::get('/data-wilayah/kecamatan', [DataController::class, 'list_kecamatan'])->name('wilayah.listkec');

    Route::post('/data-wilayah/kecamatan', [DataController::class, 'add_kecamatan'])->name('wilayah.kec');
    Route::post('/data-wilayah/hapus-kecamatan', [DataController::class, 'delete_kecamatan'])->name('wilayah.delkec');
    // Route::post('/data-wilayah/hapus-kecamatan-terpilih', [DataController::class, 'delete_kecamatan_multi'])->name('wilayah.delkec_multi');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
