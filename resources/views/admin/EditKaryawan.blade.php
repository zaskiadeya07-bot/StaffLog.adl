@extends('layouts.AdminLayout')

@section('title', 'Edit Karyawan')

@section('content')
<div>
    <x-page-header title="Edit Karyawan" description="Perbaharui data karyawan yang sudah terdaftar" />

    <x-errors />

    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    <div class="card">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.edit-karyawan.update', $karyawan->id_pengguna) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_lengkap"
                            class="input-field w-full @error('nama_lengkap') border-red-500 @enderror"
                            value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}"
                            minlength="3" maxlength="100" pattern="[\pL\s]+" required>
                        @error('nama_lengkap')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ID Karyawan (Otomatis) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            ID Karyawan
                        </label>
                        <input type="text" 
                               class="input-field w-full bg-slate-50 text-slate-400" 
                               value="{{ $karyawan->id_karyawan }}" disabled readonly>
                        <input type="hidden" name="id_karyawan" value="{{ $karyawan->id_karyawan }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nama Pengguna <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username"
                            class="input-field w-full @error('username') border-red-500 @enderror"
                            value="{{ old('username', $karyawan->username) }}"
                            minlength="5" maxlength="30" pattern="\S+" required>
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Alamat</label>
                        <textarea name="alamat" rows="3" maxlength="255"
                            class="input-field w-full">{{ old('alamat', $karyawan->alamat) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nomor HP <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="nomor_hp"
                            class="input-field w-full @error('nomor_hp') border-red-500 @enderror"
                            value="{{ old('nomor_hp', $karyawan->nomor_hp) }}"
                            pattern="[0-9]{10,15}" inputmode="numeric" required>
                        @error('nomor_hp')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Mulai Kerja (Otomatis) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Tanggal Mulai Kerja
                        </label>
                        <input type="date" 
                               class="input-field w-full bg-slate-50 text-slate-400" 
                               value="{{ old('tgl_mulai_kerja', $karyawan->tgl_mulai_kerja ? \Carbon\Carbon::parse($karyawan->tgl_mulai_kerja)->format('Y-m-d') : '') }}" disabled readonly>
                        <input type="hidden" name="tgl_mulai_kerja" value="{{ old('tgl_mulai_kerja', $karyawan->tgl_mulai_kerja ? \Carbon\Carbon::parse($karyawan->tgl_mulai_kerja)->format('Y-m-d') : '') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <select name="divisi" class="input-field w-full @error('divisi') border-red-500 @enderror" required>
                            <option value="" disabled>Pilih divisi</option>
                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->id }}" {{ old('divisi', $karyawan->divisi) == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->nama_devisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('divisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="pwEdit"
                                class="input-field w-full pr-10"
                                placeholder="Kosongkan jika tidak ingin mengubah" minlength="8">
                            <button type="button" onclick="togglePass('pwEdit', 'eyeEdit')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye" id="eyeEdit"></i>
                            </button>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Minimal 8 karakter. Isi hanya jika ingin mengubah kata sandi.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Konfirmasi Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="pwEditKonfirmasi"
                                class="input-field w-full pr-10"
                                placeholder="Kosongkan jika tidak ingin mengubah" minlength="8">
                            <button type="button" onclick="togglePass('pwEditKonfirmasi', 'eyeEditKonfirmasi')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye" id="eyeEditKonfirmasi"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="flex gap-3 mt-8 pt-4 border-t border-slate-100">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
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
