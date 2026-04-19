@extends('layouts.dashboard', [
    'role' => 'karyawan',
    'userName' => 'Dina Andini',
    'pageTitle' => 'Profil Karyawan',
])

@php
    $profile = [
        'name' => 'Dina Andini',
        'employee_id' => 'EMP-017',
        'division' => 'Engineering',
        'phone' => '0812-3456-7890',
        'address' => 'Jl. Cendana Raya No. 21, Jakarta Selatan',
        'start_date' => '2024-01-15',
    ];

    $initials = collect(explode(' ', $profile['name']))
        ->take(2)
        ->map(fn (string $namePart) => mb_strtoupper(mb_substr($namePart, 0, 1)))
        ->implode('');
@endphp

@section('content')
    <section class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-600 text-2xl font-black text-white">
                    {{ $initials }}
                </div>

                <div class="flex-1">
                    <h1 class="text-3xl font-extrabold text-slate-900">{{ $profile['name'] }}</h1>
                    <p class="mt-1 text-sm text-slate-500">ID {{ $profile['employee_id'] }} • {{ $profile['division'] }}</p>
                </div>

                <button id="edit-profile-toggle" type="button" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Edit Profil</button>
            </div>

            <div class="mt-6 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nomor ID</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $profile['employee_id'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Divisi</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $profile['division'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nomor HP</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $profile['phone'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-4 sm:col-span-2 lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Alamat</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $profile['address'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal Mulai Kerja</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ \Carbon\Carbon::parse($profile['start_date'])->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </div>

        <div id="edit-profile-section" class="hidden rounded-3xl border border-blue-100 bg-blue-50/50 p-6">
            <h2 class="text-xl font-extrabold text-slate-900">Edit Profil</h2>
            <p class="mt-1 text-sm text-slate-600">Perbarui data profil Anda di bawah ini.</p>

            <form class="mt-5 grid gap-5 md:grid-cols-2">
                <div>
                    <label for="edit_nama" class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                    <input id="edit_nama" type="text" required value="{{ $profile['name'] }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label for="edit_id" class="mb-2 block text-sm font-semibold text-slate-700">Nomor ID Karyawan</label>
                    <input id="edit_id" type="text" required value="{{ $profile['employee_id'] }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>
                <div class="md:col-span-2">
                    <label for="edit_alamat" class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
                    <textarea id="edit_alamat" rows="3" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">{{ $profile['address'] }}</textarea>
                </div>
                <div>
                    <label for="edit_hp" class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP</label>
                    <input id="edit_hp" type="tel" required value="{{ $profile['phone'] }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label for="edit_tanggal" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Mulai Kerja</label>
                    <input id="edit_tanggal" type="date" required value="{{ $profile['start_date'] }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label for="edit_divisi" class="mb-2 block text-sm font-semibold text-slate-700">Divisi</label>
                    <select id="edit_divisi" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                        <option {{ $profile['division'] === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                        <option {{ $profile['division'] === 'HR' ? 'selected' : '' }}>HR</option>
                        <option {{ $profile['division'] === 'Finance' ? 'selected' : '' }}>Finance</option>
                        <option {{ $profile['division'] === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                        <option {{ $profile['division'] === 'Operations' ? 'selected' : '' }}>Operations</option>
                    </select>
                </div>
                <div>
                    <label for="edit_password" class="mb-2 block text-sm font-semibold text-slate-700">Password Baru (opsional)</label>
                    <input id="edit_password" type="password" minlength="8" placeholder="Isi jika ingin mengganti password" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const button = document.getElementById('edit-profile-toggle');
            const section = document.getElementById('edit-profile-section');

            button.addEventListener('click', function () {
                section.classList.toggle('hidden');
                button.textContent = section.classList.contains('hidden') ? 'Edit Profil' : 'Tutup Edit Profil';
            });
        })();
    </script>
@endpush
