@extends('layouts.AdminLayout')

@section('title', 'Rekap Karyawan')

@section('content')
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
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
    
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="rekapKaryawanTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">ID Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nama Lengkap</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Username</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nomor HP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($karyawan as $index => $k)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">{{ $k->id_karyawan ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $k->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $k->username }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{-- PERBAIKAN DI SINI --}}
                                @if($k->divisi_nama)
                                    {{ $k->divisi_nama }}
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $k->nomor_hp ?? '-' }}</td>
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
                                    <button data-id="{{ $k->id_pengguna }}" data-name="{{ $k->nama_lengkap }}" 
                                            onclick="showDeleteModal(this.dataset.id, this.dataset.name)" 
                                            class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition"
                                            title="Hapus Karyawan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                <i class="bi bi-inbox text-4xl"></i>
                                <p class="mt-2">Belum ada data karyawan</p>
                                <a href="{{ route('admin.tambah-karyawan') }}" class="text-blue-500 hover:underline mt-2 inline-block">
                                    Tambah karyawan sekarang
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-xl">
        <div class="bg-red-600 text-white p-4 rounded-t-2xl">
            <h3 class="font-bold text-lg flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                Konfirmasi Hapus
            </h3>
        </div>
        <div class="p-6">
            <p class="text-slate-700">Apakah Anda yakin ingin menghapus karyawan <strong id="deleteName"></strong>?</p>
            <p class="text-slate-400 text-sm mt-2">Data yang dihapus tidak dapat dikembalikan.</p>
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
                Batal
            </button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="bi bi-trash"></i> Hapus
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
        $('#rekapKaryawanTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ]
        });
    });
</script>
@endpush
@endsection
