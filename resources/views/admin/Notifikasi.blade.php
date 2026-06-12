@extends('layouts.AdminLayout')

@section('title', 'Notifikasi Perizinan')

@section('content')
<div>
    <x-page-header title="Notifikasi Perizinan" description="Daftar pengajuan izin, cuti, dan sakit yang menunggu validasi">
        <x-slot:actionSlot>
            <div>
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm" id="totalNotif">0</span>
                <span class="text-slate-500 ml-1 text-sm">Total Notifikasi</span>
            </div>
        </x-slot:actionSlot>
    </x-page-header>

    {{-- Tabs --}}
    <div class="border-b border-slate-200 mb-5">
        <nav class="flex gap-1">
            @foreach([['semua', 'bi-envelope', 'Semua'], ['cuti_tahunan', 'bi-calendar-check', 'Cuti Tahunan'], ['izin', 'bi-pencil-square', 'Izin'], ['sakit', 'bi-thermometer-half', 'Cuti Sakit']] as [$tab, $icon, $label])
            <button class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg {{ $loop->first ? 'active' : '' }}" data-tab="{{ $tab }}">
                <i class="{{ $icon }}"></i> {{ $label }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- Content --}}
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="notifikasiTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Nama Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="notifikasiTableBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div id="notifikasiEmpty" class="hidden py-8 text-center text-slate-500">Belum ada data perizinan</div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-slate-800 p-5 rounded-t-3xl sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Detail Perizinan</h3>
                <button class="close-modal text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
            </div>
        </div>
        <div class="p-6" id="detailContent"></div>
        <div class="p-5 border-t border-slate-100 flex justify-center gap-3">
            <button class="close-modal btn-secondary px-6" type="button">Tutup</button>
            <button id="approveBtn" class="btn-primary px-6 bg-emerald-600 hover:bg-emerald-700">Setujui</button>
            <button id="rejectBtn" class="bg-red-600 hover:bg-red-700 text-white rounded-xl px-6 py-2 text-sm font-semibold">Tolak</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTab = 'semua';
let currentPerizinanId = null;
let allData = [];
let currentPage = 1;
let lastPage = 1;
const PER_PAGE = 10;

function formatTanggal(tgl) {
    return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}

const BADGE_HTML = {
    'Izin': '<span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-pencil-square"></i> Izin</span>',
    'Cuti Sakit': '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-thermometer-half"></i> Cuti Sakit</span>',
    'Cuti Tahunan': '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-calendar-check"></i> Cuti Tahunan</span>',
};

const STATUS_BADGE = {
    'Menunggu': '<span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-clock-history"></i> Menunggu</span>',
    'Disetujui': '<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-check-circle"></i> Disetujui</span>',
    'Ditolak': '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-x-circle"></i> Ditolak</span>',
};

function loadData() {
    let url = '{{ route('admin.notifikasi.data') }}?page=' + currentPage + '&per_page=' + PER_PAGE;
    if (currentTab !== 'semua') url += '&jenis=' + currentTab;

    fetch(url)
        .then(r => r.json())
        .then(res => {
            allData = res.data;
            lastPage = res.last_page;
            document.getElementById('totalNotif').innerText = res.total;
            renderTable();
            renderPagination();
        });
}

function renderTable() {
    const tbody = document.getElementById('notifikasiTableBody');
    const table = document.getElementById('notifikasiTable');
    const empty = document.getElementById('notifikasiEmpty');
    tbody.innerHTML = '';

    if (allData.length === 0) {
        table.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
    }

    table.classList.remove('hidden');
    empty.classList.add('hidden');

    allData.forEach((item, index) => {
        tbody.insertAdjacentHTML('beforeend', `
            <tr class="hover:bg-slate-50 transition">
                <td class="px-4 py-3 text-sm text-slate-600">${(currentPage - 1) * PER_PAGE + index + 1}</td>
                <td class="px-4 py-3 text-sm text-slate-600">${formatTanggal(item.tanggal)}</td>
                <td class="px-4 py-3 font-medium text-slate-800">${item.nama}</td>
                <td class="px-4 py-3 text-sm text-slate-600">${item.divisi}</td>
                <td class="px-4 py-3">${BADGE_HTML[item.jenis] || ('<span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-full text-xs">' + item.jenis + '</span>')}</td>
                <td class="px-4 py-3">${STATUS_BADGE[item.status] || ('<span class="bg-slate-100 text-slate-500 px-2 py-1 rounded-full text-xs">' + item.status + '</span>')}</td>
                <td class="px-4 py-3">
                    <button onclick="showDetail(${item.id})" class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </button>
                </td>
            </tr>
        `);
    });
}

function renderPagination() {
    let pagContainer = document.getElementById('paginationContainer');
    if (!pagContainer) {
        pagContainer = document.createElement('div');
        pagContainer.id = 'paginationContainer';
        pagContainer.className = 'flex items-center justify-between p-4 border-t border-slate-100';
        document.querySelector('.card .p-0').appendChild(pagContainer);
    }

    let html = '<div class="text-sm text-slate-500">Halaman ' + currentPage + ' dari ' + lastPage + '</div><div class="flex gap-1">';
    const btn = (p, label, extra) => `<button onclick="goToPage(${p})" class="px-3 py-1.5 text-sm rounded-lg border ${p === currentPage ? 'bg-blue-600 text-white border-blue-600' : (extra || 'bg-white text-slate-600 hover:bg-slate-50')}">${label}</button>`;

    html += btn(1, '&laquo;', currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '');
    html += btn(currentPage - 1, '&lsaquo;', currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '');
    for (let p = Math.max(1, currentPage - 2); p <= Math.min(lastPage, currentPage + 2); p++) html += btn(p, p);
    html += btn(currentPage + 1, '&rsaquo;', currentPage === lastPage ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '');
    html += btn(lastPage, '&raquo;', currentPage === lastPage ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '');
    html += '</div>';

    pagContainer.innerHTML = html;
}

function goToPage(page) {
    if (page < 1 || page > lastPage) return;
    currentPage = page;
    loadData();
}

function showDetail(id) {
    const item = allData.find(i => i.id === id);
    if (!item) return;

    currentPerizinanId = item.id;
    const isProcessed = item.status_original !== 'pending';

    let lampiranHtml = '';
    if (item.file_surat) {
        const fileName = item.file_surat.split('/').pop();
        lampiranHtml = `
            <div class="md:col-span-2">
                <p class="text-xs text-slate-500">Lampiran</p>
                <div class="mt-1 flex items-center gap-2">
                    <a href="{{ asset('storage') }}/${item.file_surat}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-2 rounded-lg">
                        <i class="bi bi-file-earmark-text"></i> <span class="text-sm">${fileName}</span> <i class="bi bi-box-arrow-up-right ml-1"></i>
                    </a>
                    <a href="{{ asset('storage') }}/${item.file_surat}" download="${fileName}" class="text-slate-500 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100" title="Unduh">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>`;
    }

    document.getElementById('detailContent').innerHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><p class="text-xs text-slate-500">Nama Karyawan</p><p class="font-semibold">${item.nama}</p></div>
                <div><p class="text-xs text-slate-500">Divisi</p><p>${item.divisi}</p></div>
                <div><p class="text-xs text-slate-500">Tanggal Pengajuan</p><p>${formatTanggal(item.tanggal)}</p></div>
                <div><p class="text-xs text-slate-500">Jenis</p><div>${BADGE_HTML[item.jenis] || item.jenis}</div></div>
                <div><p class="text-xs text-slate-500">Periode</p><p>${formatTanggal(item.tgl_mulai)} - ${formatTanggal(item.tgl_selesai)}</p></div>
                <div><p class="text-xs text-slate-500">Durasi</p><p>${item.durasi}</p></div>
                <div class="md:col-span-2"><p class="text-xs text-slate-500">Alasan / Keterangan</p><p class="bg-slate-50 p-3 rounded-xl text-sm">${item.keterangan || '-'}</p></div>
                ${lampiranHtml}
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500">Catatan Admin</p>
                    <textarea id="catatanAdmin" class="input-field w-full p-2 border rounded-lg" rows="3" placeholder="Tambahkan catatan..." ${isProcessed ? 'disabled' : ''}>${item.catatan_admin || ''}</textarea>
                </div>
            </div>
        </div>`;

    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
    document.getElementById('approveBtn').classList.toggle('hidden', isProcessed);
    document.getElementById('rejectBtn').classList.toggle('hidden', isProcessed);
}

function updateStatus(status) {
    if (!currentPerizinanId) return;
    const catatan = document.getElementById('catatanAdmin')?.value || '';

    fetch('{{ route('admin.notifikasi.update', ':id') }}'.replace(':id', currentPerizinanId), {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ status, catatan })
    })
    .then(r => r.json())
    .then(result => {
        Swal.fire({ icon: result.success ? 'success' : 'error', title: result.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        if (result.success) { closeDetailModal(); loadData(); }
    });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
    currentPerizinanId = null;
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentTab = this.dataset.tab;
        currentPage = 1;
        loadData();
    });
});

document.querySelectorAll('.close-modal').forEach(b => b.addEventListener('click', closeDetailModal));
document.getElementById('approveBtn')?.addEventListener('click', () => updateStatus('Disetujui'));
document.getElementById('rejectBtn')?.addEventListener('click', () => updateStatus('Ditolak'));

loadData();
</script>
@endpush
@endsection
