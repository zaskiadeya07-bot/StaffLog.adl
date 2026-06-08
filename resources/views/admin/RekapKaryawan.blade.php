@extends('layouts.AdminLayout')

@section('title', 'Rekap Karyawan')

@section('content')
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Karyawan</h1>
            <p class="text-slate-500 text-sm">Kelola data karyawan yang terdaftar</p>
        </div>
        <a href="{{ route('admin.tambah-karyawan') }}" class="btn-primary inline-flex items-center gap-2">
            <i class="bi bi-person-plus"></i> Tambah Karyawan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg mb-4 flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Tab --}}
    <div class="flex gap-1 mb-4 bg-slate-100 p-1 rounded-lg w-fit">
        <a href="{{ route('admin.rekap-karyawan', ['filter' => 'aktif']) }}"
           class="px-4 py-2 text-sm font-medium rounded-md transition {{ $filter === 'aktif' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
            <i class="bi bi-person-check"></i> Aktif
        </a>
        <a href="{{ route('admin.rekap-karyawan', ['filter' => 'nonaktif']) }}"
           class="px-4 py-2 text-sm font-medium rounded-md transition {{ $filter === 'nonaktif' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
            <i class="bi bi-person-x"></i> Nonaktif
        </a>
        <a href="{{ route('admin.rekap-karyawan', ['filter' => 'semua']) }}"
           class="px-4 py-2 text-sm font-medium rounded-md transition {{ $filter === 'semua' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
            <i class="bi bi-people"></i> Semua
        </a>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Username</th>
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
                                @if($k->status === 'aktif')
                                    <span class="bg-emerald-100 text-emerald-700 text-xs font-medium px-2.5 py-1 rounded-full">Aktif</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1 rounded-full">Nonaktif</span>
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
                                       title="Edit Karyawan">
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
                                        @csrf
                                        @method('PUT')
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

<!-- Modal Konfirmasi Nonaktifkan -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
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
                @csrf
                @method('DELETE')
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
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = '{{ route('admin.hapus-karyawan', ':id') }}'.replace(':id', id);
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    $(document).ready(function() {
        const table = document.getElementById('rekapKaryawanTable');
        if (table) {
            $(table).DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: [0, 7] }
                ]
            });
        }
    });
</script>
@endpush
@endsection
