@extends('layouts.KaryawanLayout')

@section('title', 'Profil Saya')

@section('content')

@if (!$pengguna)
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <i class="bi bi-person-x text-6xl mb-4"></i>
        <p class="text-lg font-semibold">Data profil tidak ditemukan.</p>
        <p class="text-sm mt-1">Silakan login ulang.</p>
        <a href="{{ route('login') }}" class="mt-4 btn-primary">Login Ulang</a>
    </div>
@else

{{-- ── HEADER ──────────────────────────────────────────────────────────────── --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Profil Saya</h1>
    <p class="text-slate-500 text-sm">Informasi akun dan data kepegawaian Anda</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── KOLOM KIRI: KARTU IDENTITAS ──────────────────────────────────── --}}
    <div class="lg:col-span-1 flex flex-col gap-5">

        {{-- Avatar Card --}}
        <div class="card p-6 flex flex-col items-center text-center">
            {{-- Avatar Inisial --}}
            <div class="w-24 h-24 rounded-full bg-slate-800 flex items-center justify-center mb-4 shadow-lg">
                <span class="text-3xl font-bold text-white">
                    {{ strtoupper(substr($pengguna->nama_lengkap, 0, 1)) }}{{ strtoupper(substr(strrchr($pengguna->nama_lengkap, ' ') ?: ' ', 1, 1)) }}
                </span>
            </div>

            <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $pengguna->nama_lengkap }}</h2>
            <p class="text-slate-500 text-sm mb-3">{{ '@' . $pengguna->username }}</p>

            {{-- Badge Role --}}
            @if ($pengguna->role === 'admin')
                <span class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    <i class="bi bi-shield-check"></i> Admin
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    <i class="bi bi-person-badge"></i> Karyawan
                </span>
            @endif

            <div class="w-full border-t border-slate-100 mt-4 pt-4 space-y-2 text-left">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <i class="bi bi-building text-slate-400 w-4 text-center"></i>
                    <span>{{ $pengguna->devisi->nama_devisi ?? '-' }}</span>
                </div>
                @if ($pengguna->nomor_hp)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <i class="bi bi-telephone text-slate-400 w-4 text-center"></i>
                    <span>{{ $pengguna->nomor_hp }}</span>
                </div>
                @endif
                @if ($pengguna->tgl_mulai_kerja)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <i class="bi bi-calendar-event text-slate-400 w-4 text-center"></i>
                    <span>Mulai {{ \Carbon\Carbon::parse($pengguna->tgl_mulai_kerja)->translatedFormat('d F Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Masa Kerja Card --}}
        @if ($pengguna->tgl_mulai_kerja)
        @php
            $mulai    = \Carbon\Carbon::parse($pengguna->tgl_mulai_kerja);
            $sekarang = \Carbon\Carbon::now();
            $tahun    = (int) $mulai->diffInYears($sekarang);
            $bulan    = (int) $mulai->copy()->addYears($tahun)->diffInMonths($sekarang);
            $hari     = (int) $mulai->copy()->addYears($tahun)->addMonths($bulan)->diffInDays($sekarang);
        @endphp
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-slate-600 mb-3 flex items-center gap-2">
                <i class="bi bi-briefcase text-slate-400"></i> Masa Kerja
            </h3>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-2xl font-bold text-slate-800">{{ $tahun }}</p>
                    <p class="text-xs text-slate-500">Tahun</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-2xl font-bold text-slate-800">{{ $bulan }}</p>
                    <p class="text-xs text-slate-500">Bulan</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-2xl font-bold text-slate-800">{{ $hari }}</p>
                    <p class="text-xs text-slate-500">Hari</p>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ── KOLOM KANAN: DETAIL DATA ──────────────────────────────────────── --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Data Diri --}}
        <div class="card">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <i class="bi bi-person-lines-fill text-blue-500"></i>
                <h3 class="font-semibold text-slate-700">Data Diri</h3>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <p class="text-xs text-slate-400 mb-1">Nama Lengkap</p>
                    <p class="font-semibold text-slate-800">{{ $pengguna->nama_lengkap }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-400 mb-1">Username</p>
                    <p class="font-semibold text-slate-800">{{ $pengguna->username }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-400 mb-1">Divisi</p>
                    <p class="font-semibold text-slate-800">{{ $pengguna->devisi->nama_devisi ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-400 mb-1">Role</p>
                    <p class="font-semibold text-slate-800 capitalize">{{ $pengguna->role }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-400 mb-1">Nomor HP</p>
                    <p class="font-semibold text-slate-800">{{ $pengguna->nomor_hp ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-400 mb-1">Tanggal Mulai Kerja</p>
                    <p class="font-semibold text-slate-800">
                        {{ $pengguna->tgl_mulai_kerja
                            ? \Carbon\Carbon::parse($pengguna->tgl_mulai_kerja)->translatedFormat('d F Y')
                            : '-' }}
                    </p>
                </div>

            </div>
        </div>

        {{-- Keamanan Akun --}}
        <div class="card">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <i class="bi bi-shield-lock text-slate-500"></i>
                <h3 class="font-semibold text-slate-700">Ubah Password</h3>
            </div>
            <div class="p-5">

                {{-- Flash sukses --}}
                @if (session('password_success'))
                <div id="passSuccess" class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 mb-4 text-sm">
                    <i class="bi bi-check-circle-fill text-emerald-500"></i>
                    {{ session('password_success') }}
                    <button onclick="document.getElementById('passSuccess').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                @endif

                {{-- Flash error --}}
                @if (session('password_error'))
                <div id="passError" class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 mb-4 text-sm">
                    <i class="bi bi-exclamation-triangle-fill text-red-500"></i>
                    {{ session('password_error') }}
                    <button onclick="document.getElementById('passError').remove()" class="ml-auto text-red-400 hover:text-red-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                @endif

                <form action="{{ route('karyawan.password.update') }}" method="POST" id="formPassword">
                    @csrf
                    @method('PUT')

                    {{-- Password Lama --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            <i class="bi bi-lock text-slate-400 mr-1"></i> Password Saat Ini
                        </label>
                        <div class="relative">
                            <input type="password" name="password_lama" id="passwordLama"
                                class="w-full px-4 py-2.5 pr-10 border {{ $errors->has('password_lama') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                                placeholder="Masukkan password saat ini" autocomplete="current-password">
                            <button type="button" onclick="togglePass('passwordLama', 'eyeLama')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye" id="eyeLama"></i>
                            </button>
                        </div>
                        @if ($errors->has('password_lama'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('password_lama') }}</p>
                        @endif
                    </div>

                    {{-- Password Baru --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            <i class="bi bi-key text-slate-400 mr-1"></i> Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" name="password_baru" id="passwordBaru"
                                class="w-full px-4 py-2.5 pr-10 border {{ $errors->has('password_baru') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                                placeholder="Minimal 6 karakter" autocomplete="new-password">
                            <button type="button" onclick="togglePass('passwordBaru', 'eyeBaru')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye" id="eyeBaru"></i>
                            </button>
                        </div>
                        @if ($errors->has('password_baru'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('password_baru') }}</p>
                        @endif
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            <i class="bi bi-key-fill text-slate-400 mr-1"></i> Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" name="password_baru_confirmation" id="passwordKonfirmasi"
                                class="w-full px-4 py-2.5 pr-10 border {{ $errors->has('password_baru_confirmation') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                                placeholder="Ulangi password baru" autocomplete="new-password">
                            <button type="button" onclick="togglePass('passwordKonfirmasi', 'eyeKonfirmasi')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye" id="eyeKonfirmasi"></i>
                            </button>
                        </div>
                        @if ($errors->has('password_baru_confirmation'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('password_baru_confirmation') }}</p>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                        <i class="bi bi-floppy"></i> Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endif

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

@endsection

