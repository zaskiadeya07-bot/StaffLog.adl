<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\Admin\TambahKaryawan; 
use App\Http\Controllers\Admin\PengaturanKantorController;
use App\Http\Controllers\Admin\DetailRekapKehadiranController;
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\Admin\RekapKaryawanController;
use App\Http\Controllers\Admin\GantiPasswordController;
use App\Http\Controllers\Admin\DivisiController;
use App\Http\Controllers\Karyawan\AbsenMasuk;
use App\Http\Controllers\Karyawan\AbsenKeluar;
use App\Http\Controllers\Karyawan\DashboardController;
use App\Http\Controllers\Karyawan\IzinCutiController;
use App\Http\Controllers\Karyawan\ProfileController;
use App\Http\Controllers\Karyawan\RekapAbsenController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('landing.Index');
})->name('landing');

// Login
Route::get('/login',  [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// API Publik — pengaturan kantor (dipakai oleh view karyawan via fetch/axios)
Route::get('/api/setting', [MasterDataController::class, 'apiSetting'])->name('api.setting');

// =========================================================================
// ADMIN ROUTES
// =========================================================================
Route::prefix('admin')->name('admin.')->middleware(['session.check', 'role.check:admin'])->group(function () {

    // Rekap Karyawan
    Route::get('/rekap-karyawan', [RekapKaryawanController::class, 'index'])->name('rekap-karyawan');
    
    // Tambah Karyawan
    Route::get('/tambah-karyawan', [TambahKaryawan::class, 'index'])->name('tambah-karyawan');
    Route::post('/tambah-karyawan', [TambahKaryawan::class, 'store'])->name('tambah-karyawan.store');
    
    // Edit Karyawan
    Route::get('/edit-karyawan/{id}', [TambahKaryawan::class, 'edit'])->name('edit-karyawan');
    Route::put('/edit-karyawan/{id}', [TambahKaryawan::class, 'update'])->name('edit-karyawan.update');
    
    // Hapus (Nonaktifkan) & Aktifkan Karyawan
    Route::delete('/hapus-karyawan/{id}', [TambahKaryawan::class, 'destroy'])->name('hapus-karyawan');
    Route::put('/aktifkan-karyawan/{id}', [TambahKaryawan::class, 'activate'])->name('aktifkan-karyawan');
    
    // NOTIFIKASI PERIZINAN (sudah diperbaiki)
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
    Route::get('/notifikasi/data', [NotifikasiController::class, 'getData'])->name('notifikasi.data');
    Route::put('/notifikasi/{id}', [NotifikasiController::class, 'updateStatus'])->name('notifikasi.update');
    
    // Detail Rekap Kehadiran
    Route::get('/detail-rekap-kehadiran/{id}', [DetailRekapKehadiranController::class, 'index'])->name('detail-rekap-kehadiran');
    Route::get('/detail-rekap-kehadiran/{id}/export-pdf', [DetailRekapKehadiranController::class, 'exportPdf'])->name('detail-rekap-kehadiran.export-pdf');
    Route::put('/detail-rekap-kehadiran/update-status/{id}', [DetailRekapKehadiranController::class, 'updateStatus'])->name('detail-rekap-kehadiran.update-status');
    
    // Pengaturan Kantor   
    Route::get('/pengaturan-kantor', [PengaturanKantorController::class, 'index'])->name('pengaturan-kantor');
    Route::post('/pengaturan-kantor', [PengaturanKantorController::class, 'update'])->name('pengaturan-kantor.update');

    // Ganti Password
    Route::get('/ganti-password', [GantiPasswordController::class, 'index'])->name('ganti-password');
    Route::put('/ganti-password', [GantiPasswordController::class, 'update'])->name('ganti-password.update');

    // Divisi
    Route::get('/divisi', [DivisiController::class, 'index'])->name('divisi');
    Route::post('/divisi', [DivisiController::class, 'store'])->name('divisi.store');
    Route::put('/divisi/{id}', [DivisiController::class, 'update'])->name('divisi.update');
    Route::delete('/divisi/{id}', [DivisiController::class, 'destroy'])->name('divisi.destroy');
});

// =========================================================================
// KARYAWAN ROUTES
// =========================================================================
Route::prefix('karyawan')->name('karyawan.')->middleware(['session.check', 'role.check:karyawan'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rekap Absen
    Route::get('/rekap-absen', [RekapAbsenController::class, 'index'])->name('rekap-absen');

    Route::get('/rekap/data', [RekapAbsenController::class, 'data'])->name('rekap.data');

    // IZIN CUTI (sudah diperbaiki)
    Route::get('/izin-cuti', [IzinCutiController::class, 'index'])->name('izin-cuti');
    Route::post('/izin-cuti/store', [IzinCutiController::class, 'store'])->name('izin-cuti.store');
    Route::get('/izin-cuti/data', [IzinCutiController::class, 'getData'])->name('izin-cuti.data');
    Route::put('/izin-cuti/cancel/{id}', [IzinCutiController::class, 'cancel'])->name('izin-cuti.cancel');

    // Profile & Ganti Password
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

    // CHECK IN
    Route::get('/check-in', [AbsenMasuk::class, 'index'])->name('check-in');
    Route::get('/checkin/status', [AbsenMasuk::class, 'status'])->name('checkin.status');
    Route::post('/checkin/store', [AbsenMasuk::class, 'store'])->name('checkin.store');

    // CHECK OUT
    Route::get('/check-out', [AbsenKeluar::class, 'index'])->name('check-out');
    Route::get('/checkout/status', [AbsenKeluar::class, 'status'])->name('checkout.status');
    Route::post('/checkout/store', [AbsenKeluar::class, 'store'])->name('checkout.store');
});
