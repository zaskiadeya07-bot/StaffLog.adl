@extends('layouts.admin-layout')

@section('title', 'Tambah Karyawan')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Tambah Karyawan</h1>
        <p class="text-slate-500 text-sm">Form pendaftaran karyawan baru</p>
    </div>
    
    {{-- Tampilkan error jika ada --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tampilkan pesan sukses jika ada --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tampilkan pesan error jika ada --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="card">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.tambah-karyawan.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_lengkap" 
                               class="input-field w-full @error('nama_lengkap') border-red-500 @enderror" 
                               placeholder="Masukkan nama lengkap" 
                               value="{{ old('nama_lengkap') }}" 
                               required>
                        @error('nama_lengkap')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ID Karyawan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            ID Karyawan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="id_karyawan" 
                               class="input-field w-full @error('id_karyawan') border-red-500 @enderror" 
                               placeholder="EMP-001" 
                               value="{{ old('id_karyawan') }}" 
                               required>
                        <p class="text-xs text-slate-400 mt-1">Contoh: EMP-001, KRY-001, dll</p>
                        @error('id_karyawan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="username" 
                               class="input-field w-full @error('username') border-red-500 @enderror" 
                               placeholder="Masukkan username untuk login" 
                               value="{{ old('username') }}" 
                               required>
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Alamat
                        </label>
                        <textarea name="alamat" 
                                  rows="2" 
                                  class="input-field w-full" 
                                  placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nomor HP
                        </label>
                        <input type="tel" 
                               name="nomor_hp" 
                               class="input-field w-full" 
                               placeholder="08123456789" 
                               value="{{ old('nomor_hp') }}">
                    </div>

                    {{-- Tanggal Mulai Kerja --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Tanggal Mulai Kerja
                        </label>
                        <input type="date" 
                               name="tgl_mulai_kerja" 
                               class="input-field w-full" 
                               value="{{ old('tgl_mulai_kerja') }}">
                    </div>

                    {{-- Divisi --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <select name="divisi" 
                                class="input-field w-full @error('divisi') border-red-500 @enderror" 
                                required>
                            <option value="" disabled selected>Pilih divisi</option>
                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->id }}" {{ old('divisi') == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->nama_devisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('divisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               class="input-field w-full @error('password') border-red-500 @enderror" 
                               placeholder="Buat password akun" 
                               autocomplete="new-password" 
                               required>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="input-field w-full" 
                               placeholder="Konfirmasi password" 
                               autocomplete="new-password" 
                               required>
                    </div>
                </div>
                
                <div class="flex gap-3 mt-8 pt-4 border-t border-slate-100">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i> Tambah Karyawan
                    </button>
                    <button type="reset" class="btn-secondary">
                        <i class="bi bi-arrow-repeat"></i> Reset Form
                    </button>
                    <a href="{{ route('admin.rekap-karyawan') }}" class="btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection