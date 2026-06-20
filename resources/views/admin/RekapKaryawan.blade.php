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

    {{-- Filter --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <div class="flex gap-1 bg-slate-100 p-1 rounded-lg">
            @foreach(['aktif' => 'Person-check', 'nonaktif' => 'Person-x', 'semua' => 'People'] as $f => $icon)
            <a href="{{ route('admin.rekap-karyawan', array_merge(request()->query(), ['filter' => $f, 'page' => null])) }}"
               class="px-4 py-2 text-sm font-medium rounded-md transition {{ $filter === $f ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                <i class="bi bi-{{ lcfirst(substr($icon, 1)) }}"></i> {{ ucfirst($f) }}
            </a>
            @endforeach
        </div>
        <select onchange="filterByDivisi(this.value)"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400">
            <option value="">Semua Divisi</option>
            @foreach($divisis as $d)
                <option value="{{ $d->id_devisi }}" {{ $divisiId == $d->id_devisi ? 'selected' : '' }}>{{ $d->nama_devisi }}</option>
            @endforeach
        </select>
    </div>

    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                @if($karyawan->count() > 0)
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
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $karyawan->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">{{ $k->id_karyawan ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $k->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $k->username }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $k->divisi_nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $k->nomor_hp ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <x-badge type="{{ $k->status }}">{{ ucfirst($k->status) }}</x-badge>
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
                                    @if($k->status === 'aktif')
                                    <button data-id="{{ $k->id_pengguna }}" data-name="{{ $k->nama_lengkap }}"
                                            onclick="showDeleteModal(this.dataset.id, this.dataset.name)"
                                            class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition"
                                            title="Nonaktifkan Karyawan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @else
                                    <form action="{{ route('admin.aktifkan-karyawan', $k->id_pengguna) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit"
                                                class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition"
                                                title="Aktifkan Kembali">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $karyawan->links('pagination::tailwind') }}
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 text-slate-500">
                    <i class="bi bi-inbox text-5xl mb-3"></i>
                    <p class="text-lg">Belum ada data karyawan</p>
                    <a href="{{ route('admin.tambah-karyawan') }}" class="text-blue-500 hover:underline mt-2">
                        Tambah karyawan sekarang
                    </a>
                </div>
                @endif
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
            <p class="text-slate-400 text-sm mt-2">Karyawan yang dinonaktifkan tidak bisa absen sampai diaktifkan kembali. Riwayat tetap tersimpan.</p>
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
                Batal
            </button>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                    <i class="bi bi-person-x"></i> Nonaktifkan
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

    function filterByDivisi(divisiId) {
        const params = new URLSearchParams(window.location.search);
        params.set('divisi', divisiId);
        params.set('filter', '{{ $filter }}');
        window.location.search = params.toString();
    }

    $(document).ready(function() {
        const table = document.getElementById('rekapKaryawanTable');
        if (table) {
            $(table).DataTable({
                paging: false,
                info: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                columnDefs: [{ orderable: false, targets: [0, 7] }]
            });
        }
    });
</script>
@endpush
@endsection
