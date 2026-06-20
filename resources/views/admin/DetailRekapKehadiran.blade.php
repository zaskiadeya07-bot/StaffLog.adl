@extends('layouts.AdminLayout')

@section('title', 'Detail Rekap Kehadiran')

@section('content')
<div>
    <x-page-header
        title="Detail Kehadiran: {{ $karyawan->nama_lengkap ?? 'Karyawan' }}"
        description='<i class="bi bi-building"></i> {{ $karyawan->devisi->nama_devisi ?? "-" }} | Periode: {{ $bulanNama ?? date("F") }} {{ $tahun ?? date("Y") }}'
    >
        <x-slot:actionSlot>
            <div class="flex gap-2">
                <a href="{{ route('admin.detail-rekap-kehadiran.export-pdf', ['id' => $karyawan->id_pengguna, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="btn-primary inline-flex items-center gap-2" style="background:#2563eb;">
                    <i class="bi bi-filetype-pdf"></i> Ekspor PDF
                </a>
                <a href="{{ route('admin.rekap-karyawan') }}" class="btn-secondary inline-flex items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </x-slot:actionSlot>
    </x-page-header>

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('admin.detail-rekap-kehadiran', $karyawan->id_pengguna) }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Bulan</label>
                    <select name="bulan" class="input-field">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ ($bulan ?? date('m')) == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Tahun</label>
                    <select name="tahun" class="input-field">
                        @for($i = date('Y') - 5; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ ($tahun ?? date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2.5">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        @php
            $stats = [
                ['color' => 'emerald', 'icon' => 'bi-check-circle', 'value' => $statHadir ?? 0, 'label' => 'Hadir / Tepat Waktu'],
                ['color' => 'amber', 'icon' => 'bi-clock-history', 'value' => $statTerlambat ?? 0, 'label' => 'Terlambat'],
                ['color' => 'blue', 'icon' => 'bi-pencil-square', 'value' => $statIzin ?? 0, 'label' => 'Izin / Sakit'],
                ['color' => 'slate', 'icon' => 'bi-x-circle', 'value' => $statAlpha ?? 0, 'label' => 'Alpha'],
            ];
        @endphp
        @foreach($stats as $s)
            <x-stat-card color="{{ $s['color'] }}" icon="{{ $s['icon'] }}" value="{{ $s['value'] }}" label="{{ $s['label'] }}" />
        @endforeach
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="detailKehadiranTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Hari</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Keluar</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Keterlambatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($presensi as $p)
                        @php $status = $p->status ?? 'alpha'; @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">{{ $p->check_in ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">{{ $p->check_out ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <x-badge type="{{ $status }}">
                                    <i class="bi {{ $status == 'hadir' ? 'bi-check-circle' : ($status == 'terlambat' ? 'bi-clock-history' : ($status == 'izin' ? 'bi-pencil-square' : 'bi-x-circle')) }}"></i>
                                    {{ ucfirst($status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ ($p->menit_terlambat ?? 0) > 0 ? ($p->menit_terlambat ?? 0) . ' menit' : '-' }}</td>
                            <td class="px-4 py-3">
                                <button onclick="openEditModal({{ $p->id_presensi }}, '{{ $p->status }}', {{ $p->id_pengguna }})"
                                    class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" title="Ubah Status">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-slate-800 p-5 rounded-t-3xl sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Detail Absensi</h3>
                <button class="close-modal text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
            </div>
        </div>
        <div class="p-6">
            <div class="bg-slate-50 rounded-xl p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-500">Tanggal & Hari</p>
                        <p class="font-semibold" id="detailTglHari">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Status Kehadiran</p>
                        <div id="detailStatusBadge">-</div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Lokasi Absen Masuk</label>
                    <div id="detailMapMasuk" class="h-48 bg-slate-100 rounded-xl overflow-hidden relative">
                        <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full z-10">
                            <i class="bi bi-clock"></i> <span id="detailTimeMasuk">--:--</span>
                        </div>
                        <div class="flex items-center justify-center h-full text-slate-400">
                            <i class="bi bi-geo-alt-fill text-2xl"></i>
                            <span class="ml-2">Lokasi tidak tersedia</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Lokasi Absen Pulang</label>
                    <div id="detailMapPulang" class="h-48 bg-slate-100 rounded-xl overflow-hidden relative">
                        <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full z-10">
                            <i class="bi bi-clock"></i> <span id="detailTimePulang">--:--</span>
                        </div>
                        <div class="flex items-center justify-center h-full text-slate-400">
                            <i class="bi bi-geo-alt-fill text-2xl"></i>
                            <span class="ml-2">Lokasi tidak tersedia</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Catatan</label>
                <div class="bg-slate-50 rounded-xl p-3" id="detailKeterangan">-</div>
            </div>
        </div>
        <div class="p-5 border-t border-slate-100 flex justify-center">
            <button class="close-modal btn-secondary px-6" type="button">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal Edit Status --}}
<div id="editStatusModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4">
        <div class="bg-slate-800 p-5 rounded-t-3xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Ubah Status Kehadiran</h3>
                <button class="close-edit-modal text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
            </div>
        </div>
        <div class="p-6">
            <form id="editStatusForm">
                <input type="hidden" id="editPresensiId">
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Status</label>
                    <select id="editStatusSelect" class="input-field w-full">
                        @foreach(['hadir', 'terlambat', 'izin', 'alpha'] as $s)
                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Keterangan</label>
                    <textarea id="editKeterangan" class="input-field w-full" rows="2" placeholder="Opsional"></textarea>
                </div>
            </form>
        </div>
        <div class="p-5 border-t border-slate-100 flex justify-end gap-3">
            <button class="close-edit-modal btn-secondary px-6" type="button">Batal</button>
            <button onclick="saveEditStatus()" class="btn-primary px-6" type="button">Simpan</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let mapMasuk, mapPulang;

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }

    function openEditModal(id, status) {
        document.getElementById('editPresensiId').value = id;
        document.getElementById('editStatusSelect').value = status;
        document.getElementById('editKeterangan').value = '';
        document.getElementById('editStatusModal').classList.remove('hidden');
        document.getElementById('editStatusModal').classList.add('flex');
    }

    function saveEditStatus() {
        const id = document.getElementById('editPresensiId').value;
        const status = document.getElementById('editStatusSelect').value;
        const keterangan = document.getElementById('editKeterangan').value;

        fetch('{{ route('admin.detail-rekap-kehadiran.update-status', ':id') }}'.replace(':id', id), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status, keterangan })
        })
        .then(res => res.json())
        .then(result => {
            Swal.fire({
                icon: result.success ? 'success' : 'error',
                title: result.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            if (result.success) {
                document.getElementById('editStatusModal').classList.add('hidden');
                location.reload();
            }
        });
    }

    document.querySelectorAll('.close-modal').forEach(b => b.addEventListener('click', closeDetailModal));
    document.querySelectorAll('.close-edit-modal').forEach(b => b.addEventListener('click', () => {
        document.getElementById('editStatusModal').classList.add('hidden');
        document.getElementById('editStatusModal').classList.remove('flex');
    }));

    $(document).ready(function() {
        $('#detailKehadiranTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                emptyTable: 'Belum ada data kehadiran untuk periode ini'
            },
            order: [[0, 'desc']],
            columnDefs: [{ orderable: false, targets: [6] }],
            drawCallback: function() {
                $('.dataTables_empty').addClass('px-4 py-12 text-center text-slate-500');
            }
        });
    });
</script>
@endpush
@endsection
