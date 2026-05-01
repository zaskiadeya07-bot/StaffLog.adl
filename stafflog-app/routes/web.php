<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

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
        return view('karyawan.profile');
    })->name('profile');
    
    Route::get('/check-in', function () {
        return view('karyawan.check-in');
    })->name('check-in');
    
    Route::get('/check-out', function () {
        return view('karyawan.check-out');
    })->name('check-out');
});