@extends('layouts.karyawan-layout')

@section('title', 'Rekap Kehadiran')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800" id="employeeName">Rekap Kehadiran Saya</h1>
        <p class="text-slate-500 text-sm" id="employeeInfo"></p>
    </div>
    
    <!-- Info Card -->
    <div class="bg-slate-800 rounded-xl p-5 mb-5 text-white">
        <div class="flex items-center gap-3">
            <i class="bi bi-info-circle-fill text-2xl"></i>
            <div>
                <h4 class="font-bold">Informasi</h4>
                <p class="text-sm opacity-90">Halaman ini menampilkan riwayat kehadiran Anda. Klik tombol "Detail" untuk melihat informasi lengkap absensi anda</p>
            </div>
        </div>
    </div>
    
    <!-- Note Card -->
    <div class="bg-amber-50 border-l-4 border-amber-400 rounded-xl p-3 mb-5 flex items-center gap-2">
        <i class="bi bi-shield-lock-fill text-amber-600"></i>
        <span class="text-sm text-amber-700">Anda hanya dapat melihat data kehadiran. Untuk koreksi absensi, silakan hubungi HRD atau Administrator.</span>
    </div>
    
    <!-- Filter -->
    <div class="card mb-5">
        <div class="p-4">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Bulan</label>
                    <select id="bulanSelect" class="input-field">
                        @php $blnSekarang = date('m'); @endphp
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $blnSekarang == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Tahun</label>
                    <select id="tahunSelect" class="input-field">
                        @php $thnSekarang = date('Y'); @endphp
                        @for($i = 2022; $i <= $thnSekarang; $i++)
                            <option value="{{ $i }}" {{ $thnSekarang == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
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
    
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="bg-emerald-600 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div><h6 class="text-sm opacity-90 mb-1">Hadir</h6><h2 class="text-3xl font-bold" id="statHadir">0</h2></div>
                <i class="bi bi-check-circle text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-amber-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div><h6 class="text-sm opacity-90 mb-1">Izin</h6><h2 class="text-3xl font-bold" id="statIzin">0</h2></div>
                <i class="bi bi-pencil-square text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-sky-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div><h6 class="text-sm opacity-90 mb-1">Sakit</h6><h2 class="text-3xl font-bold" id="statSakit">0</h2></div>
                <i class="bi bi-thermometer-half text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-slate-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div><h6 class="text-sm opacity-90 mb-1">Terlambat</h6><h2 class="text-3xl font-bold" id="statTerlambat">0</h2></div>
                <i class="bi bi-clock text-3xl opacity-50"></i>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="rekapTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Hari</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Pulang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Aksi</th>
                        </tr></thead>
                    <tbody id="rekapTableBody"></tbody>
                </table>
            </div>
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
                    <div><p class="text-xs text-slate-500">Tanggal & Hari</p><p class="font-semibold" id="detailTglHari">-</p></div>
                    <div><p class="text-xs text-slate-500">Status Kehadiran</p><div id="detailStatusBadge">-</div></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-semibold mb-2">Lokasi Check-In</label><div id="detailMapMasuk" class="h-48 bg-slate-100 rounded-xl overflow-hidden relative"><div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full"><i class="bi bi-clock"></i> <span id="detailTimeMasuk">--:--</span></div></div></div>
                <div><label class="block text-sm font-semibold mb-2">Lokasi Check-Out</label><div id="detailMapPulang" class="h-48 bg-slate-100 rounded-xl overflow-hidden relative"><div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full"><i class="bi bi-clock"></i> <span id="detailTimePulang">--:--</span></div></div></div>
            </div>
            <div><label class="block text-sm font-semibold mb-2">Keterangan</label><div class="bg-slate-50 rounded-xl p-3" id="detailKeterangan">-</div></div>
        </div>
        <div class="p-5 border-t border-slate-100 flex justify-center"><button class="close-modal btn-secondary px-6">Tutup</button></div>
    </div>
</div>

<script>
    let mapMasuk, mapPulang;
    
    function getNamaBulan(bulan) {
        const nama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return nama[parseInt(bulan) - 1];
    }
    
    function formatTanggal(tgl) {
        return new Date(tgl + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }
    
    function getHari(tgl) {
        const nama = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return nama[new Date(tgl + 'T00:00:00').getDay()];
    }
    
    function getStatusBadge(status) {
        const badges = {
            'hadir': '<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-check-circle"></i> Hadir</span>',
            'terlambat': '<span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-clock"></i> Terlambat</span>',
            'izin': '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs"><i class="bi bi-pencil-square"></i> Izin</span>',
            'alpha': '<span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-full text-xs"><i class="bi bi-x-circle"></i> Alpha</span>'
        };
        return badges[status] || badges['alpha'];
    }
    
    function updateStats(data) {
        let hadir = 0, terlambat = 0, izin = 0, sakit = 0;
        data.forEach(item => {
            if (item.status === 'hadir') hadir++;
            else if (item.status === 'terlambat') terlambat++;
            else if (item.status === 'izin') izin++;
            else if (item.status === 'sakit') sakit++;
        });
        document.getElementById('statHadir').innerText = hadir;
        document.getElementById('statIzin').innerText = izin;
        document.getElementById('statSakit').innerText = sakit;
        document.getElementById('statTerlambat').innerText = terlambat;
    }
    
    function renderTable(data) {
        const tbody = document.getElementById('rekapTableBody');
        tbody.innerHTML = '';
        updateStats(data);
        data.forEach(item => {
            const row = `<tr class="hover:bg-slate-50 transition">
                <td class="px-4 py-3 text-sm text-slate-600">${formatTanggal(item.tgl)}</td>
                <td class="px-4 py-3 text-sm text-slate-600">${item.hari}</td>
                <td class="px-4 py-3 text-sm text-slate-600">${item.jamMasuk}</td>
                <td class="px-4 py-3 text-sm text-slate-600">${item.jamPulang}</td>
                <td class="px-4 py-3">${getStatusBadge(item.status)}</td>
                <td class="px-4 py-3"><button onclick='showDetail(${JSON.stringify(item)})' class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition"><i class="bi bi-eye"></i> Detail</button></td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }
    
    function showDetail(item) {
        document.getElementById('detailTglHari').innerHTML = `${formatTanggal(item.tgl)} (${item.hari})`;
        document.getElementById('detailStatusBadge').innerHTML = getStatusBadge(item.status);
        document.getElementById('detailKeterangan').innerHTML = item.keterangan !== '-' ? item.keterangan : 'Tidak ada keterangan';
        document.getElementById('detailTimeMasuk').innerHTML = item.jamMasuk !== '-' ? item.jamMasuk : '--:--';
        document.getElementById('detailTimePulang').innerHTML = item.jamPulang !== '-' ? item.jamPulang : '--:--';
        
        setTimeout(() => {
            if (mapMasuk) mapMasuk.remove();
            if (mapPulang) mapPulang.remove();
            
            const mapMasukDiv = document.getElementById('detailMapMasuk');
            const mapPulangDiv = document.getElementById('detailMapPulang');
            
            if (item.latMasuk && item.lngMasuk) {
                mapMasukDiv.innerHTML = '<div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full z-[999]"><i class="bi bi-clock"></i> ' + item.jamMasuk + '</div>';
                mapMasuk = L.map('detailMapMasuk').setView([item.latMasuk, item.lngMasuk], 15);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapMasuk);
                L.marker([item.latMasuk, item.lngMasuk]).addTo(mapMasuk);
            } else {
                mapMasukDiv.innerHTML = '<div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full z-[999]"><i class="bi bi-clock"></i> --:--</div><div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
            }
            
            if (item.latPulang && item.lngPulang) {
                mapPulangDiv.innerHTML = '<div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full z-[999]"><i class="bi bi-clock"></i> ' + item.jamPulang + '</div>';
                mapPulang = L.map('detailMapPulang').setView([item.latPulang, item.lngPulang], 15);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapPulang);
                L.marker([item.latPulang, item.lngPulang]).addTo(mapPulang);
            } else {
                mapPulangDiv.innerHTML = '<div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full z-[999]"><i class="bi bi-clock"></i> --:--</div><div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
            }
        }, 100);
        
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex');
    }
    
    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }
    
    async function loadMyData() {
        const bulan = document.getElementById('bulanSelect').value;
        const tahun = document.getElementById('tahunSelect').value;
        try {
            const res = await fetch('/karyawan/rekap/data?bulan=' + bulan + '&tahun=' + tahun);
            const data = await res.json();
            const mapped = data.map(item => ({
                tgl: item.tanggal,
                hari: getHari(item.tanggal),
                jamMasuk: item.check_in || '-',
                jamPulang: item.check_out || '-',
                status: item.status || 'alpha',
                keterangan: item.catatan_keterlambatan || '-',
                latMasuk: item.check_in_lat,
                lngMasuk: item.check_in_lng,
                latPulang: item.check_out_lat,
                lngPulang: item.check_out_lng
            }));
            document.getElementById('employeeName').innerHTML = 'Rekap Kehadiran {{ session('pengguna_nama') }}';
            document.getElementById('employeeInfo').innerHTML = '<i class="bi bi-building"></i> Periode: ' + getNamaBulan(bulan) + ' ' + tahun;
            renderTable(mapped);
        } catch (e) {
            console.error('Gagal memuat data rekap:', e);
        }
    }
    
    async function filterData() {
        const bulan = document.getElementById('bulanSelect').value;
        const tahun = document.getElementById('tahunSelect').value;
        try {
            const res = await fetch('/karyawan/rekap/data?bulan=' + bulan + '&tahun=' + tahun);
            const data = await res.json();
            const mapped = data.map(item => ({
                tgl: item.tanggal,
                hari: getHari(item.tanggal),
                jamMasuk: item.check_in || '-',
                jamPulang: item.check_out || '-',
                status: item.status || 'alpha',
                keterangan: item.catatan_keterlambatan || '-',
                latMasuk: item.check_in_lat,
                lngMasuk: item.check_in_lng,
                latPulang: item.check_out_lat,
                lngPulang: item.check_out_lng
            }));
            document.getElementById('employeeInfo').innerHTML = '<i class="bi bi-building"></i> Periode: ' + getNamaBulan(bulan) + ' ' + tahun;
            renderTable(mapped);
        } catch (e) {
            console.error('Gagal memuat data:', e);
        }
    }
    
    document.getElementById('filterBtn')?.addEventListener('click', filterData);
    document.querySelectorAll('.close-modal').forEach(btn => btn.addEventListener('click', closeDetailModal));
    
    loadMyData();
</script>
@endsection