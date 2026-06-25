@extends('layouts.AdminLayout')

@section('title', 'Rekap Karyawan')

@section('content')
<div>
    <x-page-header title="Data Karyawan" description="Kelola data karyawan yang terdaftar"
        actionUrl="{{ route('admin.tambah-karyawan') }}"
        actionIcon="bi-person-plus" actionLabel="Tambah Karyawan" />

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="rekapKaryawanTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">ID Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nama Lengkap</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nama Pengguna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nomor HP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($karyawan as $index => $k)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">{{ $k->id_karyawan ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $k->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $k->username }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if($k->divisi_nama)
                                    {{ $k->divisi_nama }}
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $k->nomor_hp ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($k->status === 'nonaktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <i class="bi bi-dash-circle"></i> Nonaktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        <i class="bi bi-check-circle"></i> Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.detail-rekap-kehadiran', $k->id_pengguna) }}"
                                       class="bg-emerald-50 text-emerald-600 p-2 rounded-lg hover:bg-emerald-100 transition"
                                       title="Lihat Kehadiran">
                                        <i class="bi bi-calendar-check"></i>
                                    </a>
                                    <a href="{{ route('admin.edit-karyawan', $k->id_pengguna) }}"
                                       class="bg-amber-50 text-amber-600 p-2 rounded-lg hover:bg-amber-100 transition"
                                       title="Ubah Karyawan">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($k->status === 'nonaktif')
                                        <button data-id="{{ $k->id_pengguna }}" data-name="{{ $k->nama_lengkap }}" 
                                                onclick="showAktifkanModal(this.dataset.id, this.dataset.name)" 
                                                class="bg-emerald-50 text-emerald-600 p-2 rounded-lg hover:bg-emerald-100 transition"
                                                title="Aktifkan Karyawan">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    @else
                                        <button data-id="{{ $k->id_pengguna }}" data-name="{{ $k->nama_lengkap }}" 
                                                onclick="showDeleteModal(this.dataset.id, this.dataset.name)" 
                                                class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition"
                                                title="Nonaktifkan Karyawan">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Nonaktifkan --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-xl">
        <div class="bg-amber-600 text-white p-4 rounded-t-2xl">
            <h3 class="font-bold text-lg flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                Konfirmasi Nonaktifkan
            </h3>
        </div>
        <div class="p-6">
            <p class="text-slate-700">Apakah Anda yakin ingin menonaktifkan karyawan <strong id="deleteName"></strong>?</p>
            <p class="text-slate-400 text-sm mt-2">Karyawan yang dinonaktifkan tidak bisa login sampai diaktifkan kembali.</p>
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
                Batal
            </button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                    <i class="bi bi-slash-circle"></i> Nonaktifkan
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Aktifkan --}}
<div id="aktifkanModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-xl">
        <div class="bg-emerald-600 text-white p-4 rounded-t-2xl">
            <h3 class="font-bold text-lg flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> 
                Konfirmasi Aktifkan
            </h3>
        </div>
        <div class="p-6">
            <p class="text-slate-700">Aktifkan kembali karyawan <strong id="aktifkanName"></strong>?</p>
            <p class="text-slate-400 text-sm mt-2">Karyawan yang diaktifkan dapat login kembali.</p>
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeAktifkanModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
                Batal
            </button>
            <form id="aktifkanForm" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    <i class="bi bi-check-lg"></i> Aktifkan
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showDeleteModal(id, name) {
        document.getElementById('deleteName').innerText = name;
        document.getElementById('deleteForm').action = '{{ route('admin.hapus-karyawan', ':id') }}'.replace(':id', id);
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    function showAktifkanModal(id, name) {
        document.getElementById('aktifkanName').innerText = name;
        document.getElementById('aktifkanForm').action = '{{ route('admin.aktifkan-karyawan', ':id') }}'.replace(':id', id);
        document.getElementById('aktifkanModal').classList.remove('hidden');
        document.getElementById('aktifkanModal').classList.add('flex');
    }

    function closeAktifkanModal() {
        document.getElementById('aktifkanModal').classList.add('hidden');
        document.getElementById('aktifkanModal').classList.remove('flex');
    }

    $(document).ready(function() {
        $('#rekapKaryawanTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                emptyTable: 'Belum ada data karyawan'
            },
            dom: '<"flex flex-wrap items-center justify-between gap-3 p-3"<"dataTables_length"l><"dataTables_filter"f>>t<"flex flex-wrap items-center justify-between gap-3 p-3"<"dataTables_info"i><"dataTables_paginate"p>>',
            order: [[2, 'asc']],
            columnDefs: [{ orderable: false, targets: [0, 7] }],
            drawCallback: function() {
                $('.dataTables_empty').addClass('px-4 py-12 text-center text-slate-500');
            }
        });
    });
</script>
@endpush
@endsection