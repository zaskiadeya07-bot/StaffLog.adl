@extends('layouts.KaryawanLayout')

@section('title', 'Rekap Absen')

@section('content')
<div>
    <div class="bg-slate-800 rounded-2xl p-6 mb-6 text-white">
        <h1 class="text-2xl font-bold">Rekap Absen</h1>
        <p class="text-slate-400 mt-1">Riwayat kehadiran anda</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
        <i class="bi bi-calendar-check text-5xl text-slate-300 mb-4 block"></i>
        <p class="text-slate-600">Fitur rekap absen sedang dalam pengembangan.</p>
        <p class="text-slate-400 text-sm mt-2">Sementara ini, kamu bisa lihat kehadiran harian di halaman utama.</p>
        <a href="{{ route('karyawan.dashboard') }}" class="btn-primary inline-flex items-center gap-2 mt-4">
            <i class="bi bi-speedometer2"></i> Ke Halaman Utama
        </a>
    </div>
</div>
@endsection
