@extends('layouts.AdminLayout')

@section('title', 'Notifikasi Perizinan')

@section('content')
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Notifikasi Perizinan</h1>
            <p class="text-slate-500 text-sm">Daftar pengajuan izin dan sakit dari karyawan</p>
        </div>
        <div>
            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm" id="totalNotif">0</span>
            <span class="text-slate-500 ml-1 text-sm">Total Notifikasi</span>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="border-b border-slate-200 mb-5">
        <nav class="flex gap-1">
            <button class="tab-btn active px-5 py-2.5 text-sm font-medium rounded-t-lg" data-tab="semua">
                <i class="bi bi-envelope"></i> Semua
            </button>
            <button class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg" data-tab="izin">
                <i class="bi bi-pencil-square"></i> Izin
            </button>
            <button class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg" data-tab="sakit">
                <i class="bi bi-thermometer-half"></i> Sakit
            </button>
        </nav>
    </div>
    
    <!-- Tab Content -->
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
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
        </div>
    </div>
</div>

<!-- Modal Detail Perizinan -->
<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-slate-800 p-5 rounded-t-3xl sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Detail Perizinan</h3>
                <button class="close-modal text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
        </div>
        <div class="p-6" id="detailContent"></div>
        <div class="p-5 border-t border-slate-100 flex justify-center gap-3">
            <button class="close-modal btn-secondary px-6">Tutup</button>
            <button id="approveBtn" class="btn-primary px-6 bg-emerald-600 hover:bg-emerald-700">Setujui</button>
            <button id="rejectBtn" class="btn-danger px-6 bg-red-600 hover:bg-red-700 text-white rounded-xl px-6 py-2">Tolak</button>
        </div>
    </div>
</div>

<script>
let currentTab = 'semua';
let currentPerizinanId = null;
let allData = [];

function formatTanggal(tgl) {
    return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}

function getJenisBadge(jenis) {
    if (jenis === 'Izin') {
        return '<span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-pencil-square"></i> Izin</span>';
    } else if (jenis === 'Sakit') {
        return '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-thermometer-half"></i> Sakit</span>';
    }
    return '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">' + jenis + '</span>';
}

function getStatusBadge(status) {
    if (status === 'Menunggu') {
        return '<span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-clock-history"></i> Menunggu</span>';
    } else if (status === 'Disetujui') {
        return '<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-check-circle"></i> Disetujui</span>';
    }
    return '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-x-circle"></i> Ditolak</span>';
}

function loadData() {
    let url = '/admin/notifikasi/data';
    if (currentTab !== 'semua') {
        url += '?jenis=' + currentTab;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            allData = data;
            renderTable();
            document.getElementById('totalNotif').innerText = allData.length;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function renderTable() {
    const tbody = document.getElementById('notifikasiTableBody');
    tbody.innerHTML = '';
    
    if (allData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-500">Belum ada data perizinan</td></tr>';
        return;
    }
    
    allData.forEach((item, index) => {
        const row = `
            <tr class="hover:bg-slate-50 transition">
                <td class="px-4 py-3 text-sm text-slate-600">${index + 1}</td>
                <td class="px-4 py-3 text-sm text-slate-600">${formatTanggal(item.tanggal)}</td>
                <td class="px-4 py-3 font-medium text-slate-800">${item.nama}</td>
                <td class="px-4 py-3 text-sm text-slate-600">${item.divisi}</td>
                <td class="px-4 py-3">${getJenisBadge(item.jenis)}</td>
                <td class="px-4 py-3">${getStatusBadge(item.status)}</td>
                <td class="px-4 py-3">
                    <button onclick="showDetail(${item.id})" class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function showDetail(id) {
    const item = allData.find(i => i.id === id);
    if (!item) return;
    
    currentPerizinanId = item.id;
    
    const detailContent = `
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500">Nama Karyawan</p>
                    <p class="font-semibold">${item.nama}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Divisi</p>
                    <p>${item.divisi}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Tanggal Pengajuan</p>
                    <p>${formatTanggal(item.tanggal)}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Jenis</p>
                    <div>${getJenisBadge(item.jenis)}</div>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Periode</p>
                    <p>${formatTanggal(item.tgl_mulai)} - ${formatTanggal(item.tgl_selesai)}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Durasi</p>
                    <p>${item.durasi}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500">Alasan / Keterangan</p>
                    <p class="bg-slate-50 p-3 rounded-xl text-sm">${item.keterangan || '-'}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500">Catatan Admin</p>
                    <textarea id="catatanAdmin" class="input-field w-full p-2 border rounded-lg" rows="3" placeholder="Tambahkan catatan...">${item.catatan_admin || ''}</textarea>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = detailContent;
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
}

function updateStatus(status) {
    if (!currentPerizinanId) return;
    
    const catatan = document.getElementById('catatanAdmin')?.value || '';
    
    fetch(`/admin/notifikasi/${currentPerizinanId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: status,
            catatan: catatan
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast(result.message);
            closeDetailModal();
            loadData();
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 z-50 bg-${type === 'success' ? 'emerald-500' : 'red-500'} text-white px-5 py-3 rounded-xl shadow-lg`;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
    currentPerizinanId = null;
}

// Event Listeners
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentTab = this.dataset.tab;
        loadData();
    });
});

document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', closeDetailModal);
});

document.getElementById('approveBtn')?.addEventListener('click', () => updateStatus('Disetujui'));
document.getElementById('rejectBtn')?.addEventListener('click', () => updateStatus('Ditolak'));

// Load data pertama kali
loadData();
</script>
@endsection
