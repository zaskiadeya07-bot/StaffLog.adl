@extends('layouts.karyawan-layout')

@section('title', 'Dashboard')

@section('content')
<div>
    <!-- Welcome Banner -->
    <div class="bg-slate-800 rounded-2xl p-6 mb-6 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold mb-1">Halo, <span id="welcomeName">Karyawan</span>!</h1>
                <p class="text-slate-300 text-sm">Semangat bekerja hari ini. Jangan lupa untuk absen tepat waktu.</p>
            </div>
            <i class="bi bi-graph-up text-5xl opacity-40"></i>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card p-5 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i class="bi bi-lightning-charge text-amber-500"></i> Fitur Absen
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ url('/karyawan/check-in') }}" class="bg-emerald-50 p-5 rounded-xl text-center hover:bg-emerald-100 transition border-2 border-emerald-200 block no-underline">
                <i class="bi bi-box-arrow-in-right text-3xl text-emerald-600"></i>
                <p class="font-semibold text-slate-700 mt-2">Check In</p>
                <small class="text-slate-500 text-xs">Absen Masuk</small>
            </a>
            <a href="{{ url('/karyawan/check-out') }}" class="bg-red-50 p-5 rounded-xl text-center hover:bg-red-100 transition border-2 border-red-200 block no-underline">
                <i class="bi bi-box-arrow-right text-3xl text-red-600"></i>
                <p class="font-semibold text-slate-700 mt-2">Check Out</p>
                <small class="text-slate-500 text-xs">Absen Pulang</small>
            </a>
            <a href="{{ url('/karyawan/izin-cuti') }}" class="bg-amber-50 p-5 rounded-xl text-center hover:bg-amber-100 transition border-2 border-amber-200 block no-underline">
                <i class="bi bi-file-text text-3xl text-amber-600"></i>
                <p class="font-semibold text-slate-700 mt-2">Buat Izin</p>
                <small class="text-slate-500 text-xs">Izin / Sakit</small>
            </a>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="card p-5">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i class="bi bi-pie-chart text-blue-500"></i> Total Absensi Bulan Ini
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-emerald-50 rounded-xl p-4 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="bi bi-check-circle text-emerald-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-emerald-600" id="statHadir">18</p>
                <p class="text-xs text-slate-500">Hadir</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="bi bi-clock text-amber-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-amber-600" id="statTerlambat">2</p>
                <p class="text-xs text-slate-500">Terlambat</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="bi bi-file-text text-blue-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-blue-600" id="statIzin">2</p>
                <p class="text-xs text-slate-500">Izin</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="bi bi-thermometer-half text-red-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-red-600" id="statSakit">1</p>
                <p class="text-xs text-slate-500">Sakit</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var storedName = localStorage.getItem('userName');
        var userName = (storedName !== null) ? storedName : 'Karyawan';
        
        var welcomeNameElement = document.getElementById('welcomeName');
        if (welcomeNameElement !== null) {
            welcomeNameElement.innerText = userName;
        }
    });
</script>
@endsection