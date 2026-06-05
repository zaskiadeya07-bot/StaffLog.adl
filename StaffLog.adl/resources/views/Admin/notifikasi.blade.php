@extends('layouts.admin-layout')

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
                    <tbody class="divide-y divide-slate-100" id="notifikasiTableBody"></tbody>
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
            <button class="close-modal btn-secondary px-6">Batal</button>
            <button id="savePerizinanBtn" class="btn-primary px-6">Simpan</button>
        </div>
    </div>
</div>

<script>
    let allNotifications = [
        { id: 1, tanggal: '2024-04-15', nama: 'Aulia Pramesti', divisi: 'Engineering', jenis: 'Izin', alasan: 'Acara keluarga', durasi: '1 hari', status: 'Menunggu', deskripsi: 'Izin karena ada acara keluarga', jamMasuk: '08:00', jamPulang: '17:00', latMasuk: -6.200000, lngMasuk: 106.816666, latPulang: -6.200000, lngPulang: 106.816666 },
        { id: 2, tanggal: '2024-04-16', nama: 'Bimo Santoso', divisi: 'Marketing', jenis: 'Sakit', alasan: 'Demam tinggi', durasi: '2 hari', status: 'Menunggu', deskripsi: 'Sakit demam', jamMasuk: '-', jamPulang: '-', latMasuk: null, lngMasuk: null, latPulang: null, lngPulang: null }
    ];
    
    let currentTab = 'semua';
    let currentNotification = null;
    
    function formatTanggal(tgl) {
        return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }
    
    function getJenisBadge(jenis) {
        if (jenis === 'Izin') {
            return '<span class="badge-warning"><i class="bi bi-pencil-square"></i> Izin</span>';
        }
        return '<span class="badge-info"><i class="bi bi-thermometer-half"></i> Sakit</span>';
    }
    
    function getStatusBadge() {
        return '<span class="badge-secondary"><i class="bi bi-clock-history"></i> Menunggu</span>';
    }
    
    function getFilteredData() {
        if (currentTab === 'semua') return allNotifications;
        return allNotifications.filter(item => item.jenis === (currentTab === 'izin' ? 'Izin' : 'Sakit'));
    }
    
    function renderTable() {
        const tbody = document.getElementById('notifikasiTableBody');
        tbody.innerHTML = '';
        const filtered = getFilteredData();
        document.getElementById('totalNotif').innerText = allNotifications.length;
        
        filtered.forEach((item, index) => {
            const row = `
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 text-sm text-slate-600">${index + 1}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">${formatTanggal(item.tanggal)}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">${item.nama}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">${item.divisi}</td>
                    <td class="px-4 py-3">${getJenisBadge(item.jenis)}</td>
                    <td class="px-4 py-3">${getStatusBadge()}</td>
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
        currentNotification = allNotifications.find(i => i.id === id);
        if (currentNotification) {
            const detailContent = `
                <div class="bg-slate-50 rounded-xl p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500">Nama Karyawan</p>
                            <p class="font-semibold">${currentNotification.nama}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Divisi</p>
                            <p>${currentNotification.divisi}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Tanggal Pengajuan</p>
                            <p>${formatTanggal(currentNotification.tanggal)}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Jenis</p>
                            <div>${getJenisBadge(currentNotification.jenis)}</div>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Durasi</p>
                            <p>${currentNotification.durasi}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Alasan</p>
                            <p>${currentNotification.alasan}</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Lokasi Check-In</label>
                        <div id="mapMasuk" class="h-48 bg-slate-100 rounded-xl overflow-hidden"></div>
                        <div class="text-xs text-slate-500 mt-1">Jam: ${currentNotification.jamMasuk !== '-' ? currentNotification.jamMasuk : '--:--'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Lokasi Check-Out</label>
                        <div id="mapPulang" class="h-48 bg-slate-100 rounded-xl overflow-hidden"></div>
                        <div class="text-xs text-slate-500 mt-1">Jam: ${currentNotification.jamPulang !== '-' ? currentNotification.jamPulang : '--:--'}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Status Kehadiran</label>
                        <select id="statusKehadiran" class="input-field">
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpha">Alpha</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Keterangan</label>
                        <textarea id="keterangan" rows="3" class="input-field" placeholder="Tambahkan keterangan...">Tepat Waktu</textarea>
                    </div>
                </div>
            `;
            document.getElementById('detailContent').innerHTML = detailContent;
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
            
            setTimeout(() => {
                if (currentNotification.latMasuk && currentNotification.lngMasuk) {
                    const mapMasuk = L.map('mapMasuk').setView([currentNotification.latMasuk, currentNotification.lngMasuk], 15);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapMasuk);
                    L.marker([currentNotification.latMasuk, currentNotification.lngMasuk]).addTo(mapMasuk);
                } else {
                    document.getElementById('mapMasuk').innerHTML = '<div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
                }
                
                if (currentNotification.latPulang && currentNotification.lngPulang) {
                    const mapPulang = L.map('mapPulang').setView([currentNotification.latPulang, currentNotification.lngPulang], 15);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapPulang);
                    L.marker([currentNotification.latPulang, currentNotification.lngPulang]).addTo(mapPulang);
                } else {
                    document.getElementById('mapPulang').innerHTML = '<div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
                }
            }, 100);
        }
    }
    
    function savePerizinan() {
        if (currentNotification) {
            allNotifications = allNotifications.filter(i => i.id !== currentNotification.id);
            renderTable();
            showToast(`Perizinan ${currentNotification.jenis} dari ${currentNotification.nama} telah diproses!`, 'success');
            closeDetailModal();
        }
    }
    
    function showToast(message, type = 'success') {
        const toastHtml = `
            <div class="fixed bottom-5 right-5 z-50">
                <div class="bg-${type === 'success' ? 'emerald-500' : 'red-500'} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    <span>${message}</span>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => {
            const toast = document.querySelector('.fixed.bottom-5.right-5');
            if (toast) toast.remove();
        }, 3000);
    }
    
    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
        currentNotification = null;
    }
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTab = this.dataset.tab;
            renderTable();
        });
    });
    
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', closeDetailModal);
    });
    
    document.getElementById('savePerizinanBtn')?.addEventListener('click', savePerizinan);
    
    renderTable();
</script>
@endsection