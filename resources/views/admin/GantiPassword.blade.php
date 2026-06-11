@extends('layouts.AdminLayout')

@section('title', 'Ganti Password')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Ganti Password</h1>
        <p class="text-slate-500 text-sm">Perbarui password akun admin Anda</p>
    </div>

    <div class="card max-w-2xl">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="bi bi-shield-lock text-slate-500"></i>
            <h3 class="font-semibold text-slate-700">Ubah Password</h3>
        </div>
        <div class="p-5">

            @if (session('success'))
            <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 mb-4 text-sm">
                <i class="bi bi-check-circle-fill text-emerald-500"></i>
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('admin.ganti-password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-lock text-slate-400 mr-1"></i> Password Saat Ini
                    </label>
                    <input type="password" name="password_lama"
                        class="w-full px-4 py-2.5 border {{ $errors->has('password_lama') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                        placeholder="Masukkan password saat ini">
                    @if ($errors->has('password_lama'))
                        <p class="text-xs text-red-500 mt-1">{{ $errors->first('password_lama') }}</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-key text-slate-400 mr-1"></i> Password Baru
                    </label>
                    <input type="password" name="password_baru"
                        class="w-full px-4 py-2.5 border {{ $errors->has('password_baru') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                        placeholder="Minimal 6 karakter">
                    @if ($errors->has('password_baru'))
                        <p class="text-xs text-red-500 mt-1">{{ $errors->first('password_baru') }}</p>
                    @endif
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-key-fill text-slate-400 mr-1"></i> Konfirmasi Password Baru
                    </label>
                    <input type="password" name="password_baru_confirmation"
                        class="w-full px-4 py-2.5 border {{ $errors->has('password_baru_confirmation') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                        placeholder="Ulangi password baru">
                    @if ($errors->has('password_baru_confirmation'))
                        <p class="text-xs text-red-500 mt-1">{{ $errors->first('password_baru_confirmation') }}</p>
                    @endif
                </div>

                <button type="submit"
                    class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2.5 px-6 rounded-xl text-sm transition flex items-center justify-center gap-2">
                    <i class="bi bi-floppy"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
