@extends('layouts.AdminLayout')

@section('title', 'Ganti Kata Sandi')

@section('content')
<div>
    <x-page-header title="Ganti Kata Sandi" description="Perbarui kata sandi akun admin Anda" />

    <x-errors />

    @if (session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    <div class="card max-w-2xl">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="bi bi-shield-lock text-slate-500"></i>
            <h3 class="font-semibold text-slate-700">Ubah Kata Sandi</h3>
        </div>
        <div class="p-5">
            <form action="{{ route('admin.ganti-password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-lock text-slate-400 mr-1"></i> Kata Sandi Saat Ini
                    </label>
                    <input type="password" name="password_lama"
                        class="w-full px-4 py-2.5 border {{ $errors->has('password_lama') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                        placeholder="Masukkan kata sandi saat ini">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-key text-slate-400 mr-1"></i> Kata Sandi Baru
                    </label>
                    <input type="password" name="password_baru"
                        class="w-full px-4 py-2.5 border {{ $errors->has('password_baru') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                        placeholder="Minimal 6 karakter">
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-key-fill text-slate-400 mr-1"></i> Konfirmasi Kata Sandi Baru
                    </label>
                    <input type="password" name="password_baru_confirmation"
                        class="w-full px-4 py-2.5 border {{ $errors->has('password_baru_confirmation') ? 'border-red-400' : 'border-slate-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                        placeholder="Ulangi kata sandi baru">
                </div>

                <button type="submit"
                    class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2.5 px-6 rounded-xl text-sm transition flex items-center justify-center gap-2">
                    <i class="bi bi-floppy"></i> Simpan Kata Sandi Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
