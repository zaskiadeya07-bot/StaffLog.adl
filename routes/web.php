<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\Admin\tambah_karyawan; 
use App\Http\Controllers\Admin\PengaturanKantorController;
use App\Http\Controllers\Admin\DetailRekapKehadiranController;
use App\Http\Controllers\Karyawan\absen_masuk;
use App\Http\Controllers\Karyawan\absen_keluar;
use Illuminate\Support\Facades\DB;

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

     // Rekap Karyawan
    Route::get('/rekap-karyawan', function () {
        $karyawan = DB::table('pengguna')
            ->where('role', 'karyawan')
            ->leftJoin('devisi', 'pengguna.divisi', '=', 'devisi.id_devisi')
            ->select('pengguna.*', 'devisi.nama_devisi as divisi_nama')
            ->orderBy('pengguna.nama_lengkap', 'asc')
            ->get();
        return view('admin.rekap-karyawan', compact('karyawan'));
    })->name('rekap-karyawan');
    
    // Tambah Karyawan
    Route::get('/tambah-karyawan', [tambah_karyawan::class, 'index'])->name('tambah-karyawan');
    Route::post('/tambah-karyawan', [tambah_karyawan::class, 'store'])->name('tambah-karyawan.store');
    
    // Edit Karyawan
    Route::get('/edit-karyawan/{id}', [tambah_karyawan::class, 'edit'])->name('edit-karyawan');
    Route::put('/edit-karyawan/{id}', [tambah_karyawan::class, 'update'])->name('edit-karyawan.update');
    
    // Hapus Karyawan
    Route::delete('/hapus-karyawan/{id}', [tambah_karyawan::class, 'destroy'])->name('hapus-karyawan');
    
    // Notifikasi
    Route::get('/notifikasi', function () {
        return view('admin.notifikasi');
    })->name('notifikasi');
    
    // Detail Rekap Kehadiran
    Route::get('/detail-rekap-kehadiran/{id}', [DetailRekapKehadiranController::class, 'index'])
    ->name('detail-rekap-kehadiran');
    
     // Pengaturan Kantor   
    Route::get('/pengaturan-kantor', [PengaturanKantorController::class, 'index'])
        ->name('pengaturan-kantor');
    Route::post('/pengaturan-kantor', [PengaturanKantorController::class, 'update'])
        ->name('pengaturan-kantor.update');
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

    Route::get('/check-in', [absen_masuk::class, 'index'])->name('check-in');
    Route::get('/checkin/status', [absen_masuk::class, 'status'])->name('checkin.status');
    Route::post('/checkin/store', [absen_masuk::class, 'store'])->name('checkin.store');

    Route::get('/check-out', [absen_keluar::class, 'index'])->name('check-out');
    Route::get('/checkout/status', [absen_keluar::class, 'status'])->name('checkout.status');
    Route::post('/checkout/store', [absen_keluar::class, 'store'])->name('checkout.store');
});