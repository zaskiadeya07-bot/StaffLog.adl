@extends('layouts.AdminLayout')

@section('title', 'Tambah Karyawan')

@section('content')
<div>
    <x-page-header title="Tambah Karyawan" description="Form pendaftaran karyawan baru" />

    <x-errors />

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    <div class="card">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.tambah-karyawan.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_lengkap"
                            class="input-field w-full @error('nama_lengkap') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap') }}"
                            minlength="3" maxlength="100" pattern="[\pL\s]+" required>
                        @error('nama_lengkap')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">ID Karyawan</label>
                        <input type="text" class="input-field w-full bg-slate-50 text-slate-400"
                            value="Otomatis (EMP-xxx)" disabled readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nama Pengguna <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username"
                            class="input-field w-full @error('username') border-red-500 @enderror"
                            placeholder="Masukkan nama pengguna untuk login" value="{{ old('username') }}"
                            minlength="5" maxlength="30" pattern="\S+" required>
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Alamat</label>
                        <textarea name="alamat" rows="2" maxlength="255"
                            class="input-field w-full" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Nomor HP <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="nomor_hp"
                            class="input-field w-full @error('nomor_hp') border-red-500 @enderror"
                            placeholder="08123456789" value="{{ old('nomor_hp') }}"
                            pattern="[0-9]{10,15}" inputmode="numeric" required>
                        @error('nomor_hp')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Tanggal Mulai Kerja <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tgl_mulai_kerja"
                            class="input-field w-full @error('tgl_mulai_kerja') border-red-500 @enderror"
                            value="{{ old('tgl_mulai_kerja', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                        @error('tgl_mulai_kerja')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <select name="divisi" class="input-field w-full @error('divisi') border-red-500 @enderror" required>
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

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Kata Sandi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="pwTambah"
                                class="input-field w-full pr-10 @error('password') border-red-500 @enderror"
                                placeholder="Buat kata sandi akun" minlength="8" required>
                            <button type="button" onclick="togglePass('pwTambah', 'eyeTambah')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye" id="eyeTambah"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">
                            Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="pwTambahKonfirmasi"
                                class="input-field w-full pr-10"
                                placeholder="Konfirmasi kata sandi" minlength="8" required>
                            <button type="button" onclick="togglePass('pwTambahKonfirmasi', 'eyeTambahKonfirmasi')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye" id="eyeTambahKonfirmasi"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-4 border-t border-slate-100">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i> Tambah Karyawan
                    </button>
                    <button type="reset" class="btn-secondary">
                        <i class="bi bi-arrow-repeat"></i> Reset Formulir
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
