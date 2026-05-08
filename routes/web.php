<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('landing.index');
})->name('landing');

// Login
Route::get('/login',  [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// API Publik — pengaturan kantor (dipakai oleh view karyawan via fetch/axios)
Route::get('/api/setting', [MasterDataController::class, 'apiSetting'])->name('api.setting');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/rekap-karyawan', function () {
        return view('admin.rekap-karyawan');
    })->name('rekap-karyawan');

    Route::get('/tambah-karyawan', function () {
        return view('admin.tambah-karyawan');
    })->name('tambah-karyawan');

    Route::get('/edit-karyawan', function () {
        return view('admin.edit-karyawan');
    })->name('edit-karyawan');

    Route::get('/notifikasi', function () {
        return view('admin.notifikasi');
    })->name('notifikasi');

    Route::get('/detail-rekap-kehadiran', function () {
        return view('admin.detail-rekap-kehadiran');
    })->name('detail-rekap-kehadiran');

    Route::get('/pengaturan-kantor',  [MasterDataController::class, 'index'])->name('pengaturan-kantor');
    Route::post('/pengaturan-kantor', [MasterDataController::class, 'update'])->name('pengaturan-kantor.update');
});

// Karyawan Routes
Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', function () {
        return view('karyawan.dashboard');
    })->name('dashboard');

    Route::get('/rekap-absen', function () {
        return view('karyawan.rekap-absen');
    })->name('rekap-absen');

    Route::get('/izin-cuti', function () {
        return view('karyawan.izin-cuti');
    })->name('izin-cuti');

    Route::get('/profile', function () {
        $pengguna = \App\Models\Pengguna::with('devisi')
            ->find(session('pengguna_id'));
        return view('karyawan.profile', compact('pengguna'));
    })->name('profile');

    Route::put('/profile/password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'password_lama'              => ['required'],
            'password_baru'              => ['required', 'min:6', 'confirmed'],
        ], [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_lama.required'  => 'Password saat ini wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
        ]);

        $pengguna = \App\Models\Pengguna::find(session('pengguna_id'));

        if (!\Illuminate\Support\Facades\Hash::check($request->password_lama, $pengguna->password)) {
            return back()->with('password_error', 'Password saat ini tidak sesuai.');
        }

        $pengguna->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password_baru),
        ]);

        return back()->with('password_success', 'Password berhasil diubah!');
    })->name('password.update');

    Route::get('/check-in', function () {
        $setting = \App\Models\MasterData::first();
        return view('karyawan.check-in', compact('setting'));
    })->name('check-in');

    Route::get('/check-out', function () {
        $setting = \App\Models\MasterData::first();
        return view('karyawan.check-out', compact('setting'));
    })->name('check-out');
});
