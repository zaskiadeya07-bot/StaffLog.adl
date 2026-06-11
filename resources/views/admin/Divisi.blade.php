@extends('layouts.AdminLayout')

@section('title', 'Divisi')

@section('content')
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Divisi</h1>
            <p class="text-slate-500 text-sm">Kelola divisi atau bagian dalam perusahaan</p>
        </div>
        <button onclick="openModal(null)" class="btn-primary inline-flex items-center gap-2">
            <i class="bi bi-plus-circle"></i> Tambah Divisi
        </button>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 mb-4 text-sm">
            <i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 mb-4 text-sm">
            <i class="bi bi-exclamation-triangle-fill text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="p-0">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 w-16">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600">Nama Divisi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($divisis as $d)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-sm text-slate-500">{{ $d->id_devisi }}</td>
                        <td class="px-5 py-3 text-sm font-medium text-slate-800">{{ $d->nama_devisi }}</td>
                        <td class="px-5 py-3 text-right">
                            <button onclick="openModal({{ $d->id_devisi }}, '{{ $d->nama_devisi }}')"
                                class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button onclick="hapusDivisi({{ $d->id_devisi }}, '{{ $d->nama_devisi }}')"
                                class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition ml-1" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-12 text-center text-slate-400">
                            <i class="bi bi-building text-5xl block mb-3"></i>
                            <p>Belum ada divisi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="divisiModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4">
        <div class="bg-slate-800 p-5 rounded-t-3xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white" id="modalTitle">Tambah Divisi</h3>
                <button onclick="tutupModal()" class="text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
        </div>
        <form id="divisiForm" method="POST">
            @csrf
            <div class="p-6">
                <input type="hidden" id="divisiId" name="divisi_id">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Divisi</label>
                    <input type="text" id="namaDivisi" name="nama_devisi"
                        class="input-field w-full" placeholder="Masukkan nama divisi"
                        maxlength="50" required autofocus>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="tutupModal()" class="btn-secondary px-5">Batal</button>
                <button type="submit" class="btn-primary px-5" id="btnSimpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id, nama) {
        const modal = document.getElementById('divisiModal');
        const form = document.getElementById('divisiForm');
        const title = document.getElementById('modalTitle');
        const btnSimpan = document.getElementById('btnSimpan');
        const idInput = document.getElementById('divisiId');
        const namaInput = document.getElementById('namaDivisi');

        if (id) {
            title.textContent = 'Edit Divisi';
            btnSimpan.innerHTML = '<i class="bi bi-save"></i> Simpan';
            idInput.value = id;
            namaInput.value = nama;
            form.action = '{{ route('admin.divisi.update', ':id') }}'.replace(':id', id);
            form.querySelector('input[name="_method"]')?.remove();
            form.insertAdjacentHTML('beforeend', '@method('PUT')');
        } else {
            title.textContent = 'Tambah Divisi';
            btnSimpan.innerHTML = '<i class="bi bi-plus-circle"></i> Tambah';
            idInput.value = '';
            namaInput.value = '';
            form.action = '{{ route('admin.divisi.store') }}';
            form.querySelector('input[name="_method"]')?.remove();
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => namaInput.focus(), 100);
    }

    function tutupModal() {
        document.getElementById('divisiModal').classList.add('hidden');
        document.getElementById('divisiModal').classList.remove('flex');
    }

    function hapusDivisi(id, nama) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Divisi?',
            text: 'Divisi "' + nama + '" akan dihapus permanent.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626'
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.divisi.destroy', ':id') }}'.replace(':id', id);
                form.innerHTML = '@csrf @method('DELETE')';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
