<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
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

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'view'])->name('dashboard');

    Route::prefix('/statistik')->group(function() {
        Route::get('/', [StatistikController::class, 'view'])->name('stat');
        Route::get('/detail/{uid}', [StatistikController::class, 'detail_statistik'])->name('stat.detail');
        Route::post('/kecamatan', [StatistikController::class, 'detail_kecamatan'])->name('stat.kec');

        Route::get('/testing/kecamatan', [StatistikController::class, 'testing'])->name('stat.test');
        Route::get('/testing/desa/{kec}', [StatistikController::class, 'testing_desa']);
    });

    Route::prefix('/tabel-statistik')->group(function() {
        Route::get('/', [StatistikController::class, 'view_tabel'])->name('tabel');
        Route::get('/detail/{uid}', [StatistikController::class, 'detail_tabel_statistik'])->name('tabel.detail');
        Route::post('/kecamatan', [StatistikController::class, 'detail_tabel_kecamatan'])->name('tabel.kec');

        Route::get('/testing/kecamatan', [StatistikController::class, 'testing_tabel'])->name('tabel.test');
        Route::get('/testing/desa/{kec}', [StatistikController::class, 'testing_desa_tabel']);
    });

    Route::prefix('/suara-masuk')->group(function() {
        Route::get('/', [VoteController::class, 'view'])->name('vote');
        Route::get('/detail/{uid}', [VoteController::class, 'detail_voting'])->name('vote.detail');
        Route::get('/cek-pwd/{pass}', [VoteController::class, 'check_password'])->name('vote.pwd');

        Route::post('/tambah-data', [VoteController::class, 'add_voting'])->name('vote.add');
        Route::post('/hapus-data', [VoteController::class, 'delete_vote'])->name('vote.delete');
    });

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
        Route::get('/detail-user/{uid}', [UserController::class, 'detail_user']);
        Route::get('/cek-email/{mail}', [UserController::class, 'check_email']);

        Route::post('/tambah-user', [UserController::class, 'add_user'])->name('users.add');
        Route::post('/password-user', [UserController::class, 'change_pwd_user'])->name('users.pwd');
        Route::post('/hapus-user', [UserController::class, 'delete_user'])->name('users.delete');
    });

    Route::prefix('profile')->group(function() {
        Route::get('/', [ProfileController::class, 'view'])->name('profile');
        Route::post('/update-data', [ProfileController::class, 'is_update'])->name('profile.update');
    });

    Route::get('/download-template/{data}', [DataController::class, 'template_download'])->name('template');
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
