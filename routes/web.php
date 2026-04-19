<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.rekap-karyawan');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/rekap-karyawan', function () {
        return view('admin.rekap-karyawan');
    })->name('rekap-karyawan');

    Route::get('/tambah-karyawan', function () {
        return view('admin.tambah-karyawan');
    })->name('tambah-karyawan');
});

Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/profil', function () {
        return view('karyawan.profil');
    })->name('profil');

    Route::get('/check-in', function () {
        return view('karyawan.check-in');
    })->name('check-in');

    Route::get('/check-out', function () {
        return view('karyawan.check-out');
    })->name('check-out');

    Route::get('/riwayat-presensi', function () {
        return view('karyawan.riwayat-presensi');
    })->name('riwayat-presensi');
});
