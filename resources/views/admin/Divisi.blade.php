@extends('layouts.AdminLayout')

@section('title', 'Data Divisi')

@section('content')
<div>
    <x-page-header
        title="Data Divisi"
        description="Kelola daftar divisi perusahaan"
        actionUrl="#"
        actionIcon="bi-plus-circle"
        actionLabel="Tambah Divisi"
        :actionSlot="null"
    />

    <x-errors />

    @if (session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="divisiTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nama Divisi</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($divisis as $i => $d)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-700">{{ $d->nama_devisi }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openModal({{ $d->id_devisi }}, '{{ $d->nama_devisi }}')"
                                    class="bg-amber-50 text-amber-600 p-2 rounded-lg hover:bg-amber-100 transition" title="Ubah Divisi">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button onclick="hapusDivisi({{ $d->id_devisi }}, '{{ $d->nama_devisi }}')"
                                    class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition ml-1" title="Hapus Divisi">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">
                                <div class="flex flex-col items-center justify-center py-12 text-slate-500">
                                    <i class="bi bi-inbox text-5xl mb-3"></i>
                                    <p class="text-lg">Belum ada divisi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div id="modalDivisi" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 shadow-2xl">
        <div class="bg-slate-800 p-5 rounded-t-3xl">
            <div class="flex justify-between items-center">
                <h3 id="modalTitle" class="text-xl font-bold text-white">Tambah Divisi</h3>
                <button onclick="tutupModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
            </div>
        </div>
        <form id="formDivisi" method="POST">
            @csrf
            <div class="p-6">
                <input type="hidden" id="divisiId" name="id">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Divisi</label>
                <input type="text" id="namaDivisiInput" name="nama_devisi"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                    placeholder="Masukkan nama divisi" required maxlength="50">
            </div>
            <div class="p-5 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="tutupModal()" class="btn-secondary px-5">Batal</button>
                <button type="submit" id="btnSimpan" class="btn-primary px-5">
                    <i class="bi bi-plus-circle"></i> Tambah
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id = null, nama = '') {
        const title = document.getElementById('modalTitle');
        const btn = document.getElementById('btnSimpan');
        const idInput = document.getElementById('divisiId');
        const namaInput = document.getElementById('namaDivisiInput');
        const form = document.getElementById('formDivisi');

        if (id) {
            title.textContent = 'Ubah Divisi';
            btn.innerHTML = '<i class="bi bi-save"></i> Simpan';
            idInput.value = id;
            namaInput.value = nama;
            form.action = '{{ route('admin.divisi.update', ':id') }}'.replace(':id', id);
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
        } else {
            title.textContent = 'Tambah Divisi';
            btn.innerHTML = '<i class="bi bi-plus-circle"></i> Tambah';
            idInput.value = '';
            namaInput.value = '';
            form.action = '{{ route('admin.divisi.store') }}';
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        }
        document.getElementById('modalDivisi').classList.remove('hidden');
        document.getElementById('modalDivisi').classList.add('flex');
        setTimeout(() => namaInput.focus(), 100);
    }

    function tutupModal() {
        document.getElementById('modalDivisi').classList.add('hidden');
        document.getElementById('modalDivisi').classList.remove('flex');
    }

    function hapusDivisi(id, nama) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Divisi?',
            text: 'Divisi "' + nama + '" akan dihapus permanen.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.divisi.destroy', ':id') }}'.replace(':id', id);
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    document.getElementById('divisiTable')?.querySelector('tbody')?.addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        e.stopPropagation();
    });
</script>
@endpush
@endsection
