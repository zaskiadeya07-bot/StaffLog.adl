@extends('layouts.AdminLayout')

@section('title', 'Edit Karyawan')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Karyawan</h1>
        <p class="text-slate-500 text-sm">Perbaharui data karyawan yang sudah terdaftar</p>
    </div>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="card">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.edit-karyawan.update', $karyawan->id_pengguna) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama_lengkap"
                               class="input-field w-full @error('nama_lengkap') border-red-500 @enderror"
                               value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}"
                               minlength="3" maxlength="100" pattern="[\pL\s]+"
                               title="Hanya huruf dan spasi" required>
                        @error('nama_lengkap')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ID Karyawan (read-only) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            ID Karyawan
                        </label>
                        <input type="text"
                               class="input-field w-full bg-slate-50 text-slate-500"
                               value="{{ $karyawan->id_karyawan }}"
                               disabled readonly>
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="username"
                               class="input-field w-full @error('username') border-red-500 @enderror"
                               value="{{ old('username', $karyawan->username) }}"
                               minlength="5" maxlength="30" pattern="\S+"
                               title="Tanpa spasi" required>
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
                                  rows="3" maxlength="255"
                                  class="input-field w-full">{{ old('alamat', $karyawan->alamat) }}</textarea>
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nomor HP <span class="text-red-500">*</span>
                        </label>
                        <input type="tel"
                               name="nomor_hp"
                               class="input-field w-full @error('nomor_hp') border-red-500 @enderror"
                               value="{{ old('nomor_hp', $karyawan->nomor_hp) }}"
                               pattern="[0-9]{10,15}" inputmode="numeric"
                               title="10-15 digit angka" required>
                        @error('nomor_hp')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Mulai Kerja --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Tanggal Mulai Kerja <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tgl_mulai_kerja"
                               class="input-field w-full @error('tgl_mulai_kerja') border-red-500 @enderror"
                               value="{{ old('tgl_mulai_kerja', $karyawan->tgl_mulai_kerja) }}"
                               max="{{ date('Y-m-d') }}" required>
                        @error('tgl_mulai_kerja')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Divisi --}}
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

                    {{-- Password Baru (Opsional) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Password Baru
                        </label>
                        <input type="password"
                               name="password"
                               class="input-field w-full"
                               placeholder="Kosongkan jika tidak ingin mengubah"
                               minlength="8"
                               autocomplete="new-password">
                        <p class="text-xs text-slate-400 mt-1">Minimal 8 karakter. Isi hanya jika ingin mengubah password.</p>
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password"
                               name="password_confirmation"
                               class="input-field w-full"
                               placeholder="Konfirmasi password baru"
                               minlength="8"
                               autocomplete="new-password">
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
