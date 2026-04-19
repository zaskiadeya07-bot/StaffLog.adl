@extends('layouts.dashboard', [
    'role' => 'admin',
    'userName' => 'Raka Pratama',
    'pageTitle' => 'Tambah Karyawan',
])

@section('content')
    <section class="space-y-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Tambah Karyawan</h1>
            <p class="mt-1 text-sm text-slate-500">Form pendaftaran karyawan baru (dummy frontend).</p>
        </div>

        <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="nama_lengkap" class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                    <input id="nama_lengkap" name="nama_lengkap" type="text" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label for="id_karyawan" class="mb-2 block text-sm font-semibold text-slate-700">Nomor ID Karyawan</label>
                    <input id="id_karyawan" name="id_karyawan" type="text" placeholder="EMP-001" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>

                <div class="md:col-span-2">
                    <label for="alamat" class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200"></textarea>
                </div>

                <div>
                    <label for="nomor_hp" class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP</label>
                    <input id="nomor_hp" name="nomor_hp" type="tel" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label for="tanggal_mulai" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Mulai Kerja</label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label for="divisi" class="mb-2 block text-sm font-semibold text-slate-700">Divisi</label>
                    <select id="divisi" name="divisi" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                        <option value="">Pilih divisi</option>
                        <option>Engineering</option>
                        <option>HR</option>
                        <option>Finance</option>
                        <option>Marketing</option>
                        <option>Operations</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required minlength="8" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200">
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Tambah Karyawan</button>
                <button type="reset" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Reset Form</button>
            </div>
        </form>
    </section>
@endsection
