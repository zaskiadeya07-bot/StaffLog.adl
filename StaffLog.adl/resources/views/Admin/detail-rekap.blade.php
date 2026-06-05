@extends('layouts.admin-layout')

@section('title', 'Detail Rekap Kehadiran')

@section('content')
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800" id="employeeName">{{ $employee->nama_lengkap }}</h1>
            <p class="text-slate-500 text-sm" id="employeeInfo">
                <i class="bi bi-building"></i> {{ $employee->devisi->nama_devisi ?? '-' }} | 
                ID: {{ $employee->id_karyawan ?? '-' }} |
                Periode: <span id="periodeText">-</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('/admin/rekap-karyawan') }}" class="btn-secondary inline-flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="card mb-6">
        <div class="p-4">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Bulan</label>
                    <select id="bulanSelect" class="input-field">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4" selected>April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Tahun</label>
                    <select id="tahunSelect" class="input-field">
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024" selected>2024</option>
                        <option value="2025">2025</option>
                    </select>
                </div>
                <div>
                    <button id="filterBtn" class="btn-primary py-2.5">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-600 text-white rounded-xl p-4 text-center">
            <h6 class="text-sm opacity-80">Hadir</h6>
            <h2 class="text-3xl font-bold" id="statHadir">0</h2>
        </div>
        <div class="bg-yellow-500 text-white rounded-xl p-4 text-center">
            <h6 class="text-sm opacity-80">Izin</h6>
            <h2 class="text-3xl font-bold" id="statIzin">0</h2>
        </div>
        <div class="bg-blue-500 text-white rounded-xl p-4 text-center">
            <h6 class="text-sm opacity-80">Sakit</h6>
            <h2 class="text-3xl font-bold" id="statSakit">0</h2>
        </div>
        <div class="bg-gray-500 text-white rounded-xl p-4 text-center">
            <h6 class="text-sm opacity-80">Alpha</h6>
            <h2 class="text-3xl font-bold" id="statAlpha">0</h2>
        </div>
    </div>
    
    <!-- Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Hari</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Pulang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rekapTableBody">
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-slate-800 p-5 rounded-t-3xl sticky top-0">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Detail Absensi</h3>
                <button class="close-modal text-slate-400 hover:text-white text-2xl">&times;</button>
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
                    <label class="block text-sm font-semibold mb-2">Lokasi Check-In</label>
                    <div id="detailMapMasuk" class="h-48 bg-slate-100 rounded-xl overflow-hidden relative">
                        <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full">
                            <i class="bi bi-clock"></i> <span id="detailTimeMasuk">--:--</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Lokasi Check-Out</label>
                    <div id="detailMapPulang" class="h-48 bg-slate-100 rounded-xl overflow-hidden relative">
                        <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full">
                            <i class="bi bi-clock"></i> <span id="detailTimePulang">--:--</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-2">Keterangan</label>
                <div class="bg-slate-50 rounded-xl p-3" id="detailKeterangan">-</div>
            </div>
        </div>
        <div class="p-5 border-t border-slate-100 flex justify-center">
            <button class="close-modal btn-secondary px-6">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    let mapMasuk, mapPulang;
    const employeeId = {{ $employee->id_pengguna }};
    
    function getNamaBulan(bulan) {
        const nama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return nama[parseInt(bulan) - 1];
    }
    
    function getStatusBadge(status) {
        const badges = {
            'Hadir': '<span class="badge-success"><i class="bi bi-check-circle"></i> Hadir</span>',
            'Izin': '<span class="badge-warning"><i class="bi bi-pencil-square"></i> Izin</span>',
            'Sakit': '<span class="badge-info"><i class="bi bi-thermometer-half"></i> Sakit</span>',
            'Alpha': '<span class="badge-secondary"><i class="bi bi-x-circle"></i> Alpha</span>'
        };
        return badges[status] || badges.Alpha;
    }
    
    async function loadData() {
        const bulan = document.getElementById('bulanSelect').value;
        const tahun = document.getElementById('tahunSelect').value;
        
        document.getElementById('periodeText').innerText = getNamaBulan(bulan) + ' ' + tahun;
        
        try {
            const response = await fetch('/admin/rekap-filter?id=' + employeeId + '&bulan=' + bulan + '&tahun=' + tahun);
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('statHadir').innerText = result.stats.hadir;
                document.getElementById('statIzin').innerText = result.stats.izin;
                document.getElementById('statSakit').innerText = result.stats.sakit;
                document.getElementById('statAlpha').innerText = result.stats.alpha;
                
                const tbody = document.getElementById('rekapTableBody');
                if (result.rows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8">Tidak ada data untuk periode ini</td></tr>';
                    return;
                }
                
                tbody.innerHTML = '';
                for (let i = 0; i < result.rows.length; i++) {
                    const item = result.rows[i];
                    tbody.innerHTML += `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm">${item.tanggal_formatted}</td>
                            <td class="px-4 py-3 text-sm">${item.hari}</td>
                            <td class="px-4 py-3 text-sm">${item.jamMasuk}</td>
                            <td class="px-4 py-3 text-sm">${item.jamPulang}</td>
                            <td class="px-4 py-3">${getStatusBadge(item.status)}</td>
                            <td class="px-4 py-3">
                                <button onclick="showDetail(${item.id})" class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                    `;
                }
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('rekapTableBody').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500">Gagal memuat数据</td></tr>';
        }
    }
    
    async function showDetail(id) {
        try {
            const response = await fetch('/admin/rekap-detail/' + id);
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                document.getElementById('detailTglHari').innerHTML = data.tanggal_formatted + ' (' + data.hari + ')';
                document.getElementById('detailStatusBadge').innerHTML = getStatusBadge(data.status);
                document.getElementById('detailKeterangan').innerHTML = data.keterangan;
                document.getElementById('detailTimeMasuk').innerHTML = data.jamMasuk;
                document.getElementById('detailTimePulang').innerHTML = data.jamPulang;
                
                setTimeout(() => {
                    if (mapMasuk) mapMasuk.remove();
                    if (mapPulang) mapPulang.remove();
                    
                    const mapMasukDiv = document.getElementById('detailMapMasuk');
                    const mapPulangDiv = document.getElementById('detailMapPulang');
                    
                    if (data.latMasuk && data.lngMasuk) {
                        mapMasukDiv.innerHTML = '';
                        mapMasuk = L.map('detailMapMasuk').setView([data.latMasuk, data.lngMasuk], 15);
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapMasuk);
                        L.marker([data.latMasuk, data.lngMasuk]).addTo(mapMasuk).bindPopup('<b>Check-In</b><br>Jam: ' + data.jamMasuk);
                    } else {
                        mapMasukDiv.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
                    }
                    
                    if (data.latPulang && data.lngPulang) {
                        mapPulangDiv.innerHTML = '';
                        mapPulang = L.map('detailMapPulang').setView([data.latPulang, data.lngPulang], 15);
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapPulang);
                        L.marker([data.latPulang, data.lngPulang]).addTo(mapPulang).bindPopup('<b>Check-Out</b><br>Jam: ' + data.jamPulang);
                    } else {
                        mapPulangDiv.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
                    }
                }, 100);
                
                document.getElementById('detailModal').classList.remove('hidden');
                document.getElementById('detailModal').classList.add('flex');
            }
        } catch (error) {
            alert('Gagal memuat detail');
        }
    }
    
    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }
    
    document.getElementById('filterBtn').addEventListener('click', loadData);
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', closeDetailModal);
    });
    
    loadData();
</script>
@endpush