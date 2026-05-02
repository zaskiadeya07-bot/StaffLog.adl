<<<<<<< HEAD
 @extends('layouts.admin-layout')

@section('title', 'Detail Rekap Kehadiran')

@section('content')
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800" id="employeeName">Detail Rekap Kehadiran</h1>
            <p class="text-slate-500 text-sm" id="employeeInfo">Periode: April 2024</p>
        </div>
        <div class="flex gap-2">
            <button id="exportPdfBtn" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition flex items-center gap-2">
                <i class="bi bi-file-pdf"></i> Export PDF
            </button>
            <a href="{{ route('admin.rekap-karyawan') }}" class="btn-secondary inline-flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="card mb-6">
        <div class="p-4">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex items-center gap-2">
                    <span class="font-semibold">Filter:</span>
                </div>
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-emerald-600 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Hadir</h6>
                    <h2 class="text-3xl font-bold" id="statHadir">0</h2>
                </div>
                <i class="bi bi-check-circle text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-amber-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Izin</h6>
                    <h2 class="text-3xl font-bold" id="statIzin">0</h2>
                </div>
                <i class="bi bi-pencil-square text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-sky-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Sakit</h6>
                    <h2 class="text-3xl font-bold" id="statSakit">0</h2>
                </div>
                <i class="bi bi-thermometer-half text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-slate-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Alpha</h6>
                    <h2 class="text-3xl font-bold" id="statAlpha">0</h2>
                </div>
                <i class="bi bi-x-circle text-3xl opacity-50"></i>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="rekapTable" class="min-w-full divide-y divide-slate-200">
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
                    <tbody id="rekapTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Absensi -->
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
=======
@extends('layouts.app')

@section('title', 'Landing Page')

@section('content')
<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-slate-800 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-end">
                <a href="{{ route('login') }}" class="bg-amber-400 text-slate-800 px-6 py-2 rounded-full font-semibold hover:bg-amber-500 transition flex items-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="grow">
        <div class="container mx-auto px-6 py-16">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-slate-800 mb-4 tracking-tight">
                        StaffLog.adl
                    </h1>
                    <p class="text-lg text-slate-600 mb-8 max-w-lg mx-auto lg:mx-0">
                        Sistem manajemen kehadiran dan perizinan karyawan secara online. 
                        Admin dapat merekap kehadiran dengan cepat, dan karyawan dapat 
                        melihat data kehadiran mereka kapan saja.
                    </p>
                </div>
                <div class="lg:w-1/2">
                    <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100 text-center">
                        <i class="bi bi-people-fill text-8xl text-slate-800 opacity-80"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="container mx-auto px-6 py-16">
            <h2 class="text-3xl font-bold text-center text-slate-800 mb-12">Fitur Unggulan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card card-hover p-6 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-geo-alt-fill text-3xl text-slate-700"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Check-In / Out</h3>
                </div>
                <div class="card card-hover p-6 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-calendar-check text-3xl text-slate-700"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Persetujuan Izin</h3>
                </div>
                <div class="card card-hover p-6 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-graph-up text-3xl text-slate-700"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Rekap Absensi</h3>
                </div>
                <div class="card card-hover p-6 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-people text-3xl text-slate-700"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Data Karyawan</h3>
                </div>
            </div>
        </div>

        <!-- Demo Section -->
        <div class="container mx-auto px-6 py-16">
            <div class="bg-slate-100 rounded-3xl p-10 text-center">
                <i class="bi bi-play-circle text-5xl text-slate-700 mb-4 inline-block"></i>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Cara Penggunaan Web StaffLog.adl</h3>
                <button id="demoVideoBtn" class="btn-primary inline-flex items-center gap-2">
                    <i class="bi bi-youtube"></i>
                    Lihat Demo Penuh di YouTube
                </button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-800 py-12 mt-auto">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
                <div class="text-center md:text-left">
                    <h3 class="text-xl font-bold text-white">StaffLog.adl</h3>
                    <p class="text-slate-400 text-sm">Sistem Manajemen Kehadiran & Izin Digital</p>
                </div>
                <div class="flex gap-6">
                    <button id="tentangBtn" class="text-slate-300 hover:text-amber-400 transition cursor-pointer">Tentang Kami</button>
                    <button id="bantuanBtn" class="text-slate-300 hover:text-amber-400 transition cursor-pointer">Bantuan</button>
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-amber-400 transition">Masuk ke Akun</a>
                </div>
            </div>
            <div class="border-t border-slate-700 pt-6 text-center">
                <p class="text-slate-500 text-sm">&copy; 2026 StaffLog.adl - Sistem Manajemen Kehadiran Karyawan.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Modal Tentang -->
<div id="tentangModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-slate-800 p-5 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="bi bi-building text-amber-400"></i> Tentang StaffLog.adl
            </h3>
            <button class="close-modal text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <div class="p-6">
            <p class="text-slate-700 mb-4">
                <strong>StaffLog.adl</strong> adalah platform manajemen kehadiran generasi terbaru yang dirancang untuk HRD dan tim manajemen.
            </p>
            <div class="bg-amber-50 p-4 rounded-xl border-l-4 border-amber-400 mb-4">
                <strong>Misi Kami</strong><br>
                Memberikan solusi absensi digital yang akurat, transparan, dan efisien.
            </div>
            <p class="text-sm text-slate-500">&copy; 2025 StaffLog.adl – Solusi cerdas untuk masa kerja yang lebih teratur.</p>
        </div>
    </div>
</div>

<!-- Modal Bantuan -->
<div id="bantuanModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-slate-800 p-5 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="bi bi-question-circle text-amber-400"></i> Pusat Bantuan
            </h3>
            <button class="close-modal text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <div class="p-6">
            <p class="font-semibold text-slate-800 mb-3">Butuh bantuan seputar StaffLog.adl?</p>
            <ul class="space-y-2 mb-4">
                <li class="flex gap-2"><i class="bi bi-fingerprint text-amber-500"></i> <span><strong>Cara Check-In/Out:</strong> Klik tombol "Absen" pada dashboard</span></li>
                <li class="flex gap-2"><i class="bi bi-calendar-plus text-amber-500"></i> <span><strong>Pengajuan Izin:</strong> Akses menu "Izin", isi form</span></li>
                <li class="flex gap-2"><i class="bi bi-envelope text-amber-500"></i> <span><strong>Email:</strong> support@stafflog.adl</span></li>
            </ul>
            <button class="close-modal-btn w-full btn-primary py-2">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Video Demo -->
<div id="videoModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-3xl w-full mx-4 overflow-hidden">
        <div class="bg-slate-800 p-4 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-youtube text-red-500"></i> Video Demo StaffLog.adl
            </h3>
            <button class="close-video text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <div class="p-4">
            <div class="relative pb-[56.25%] h-0 rounded-xl overflow-hidden">
                <iframe id="youtubeIframe" class="absolute top-0 left-0 w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <p class="text-sm text-slate-500 mt-3 text-center">Demo lengkap fitur StaffLog.adl</p>
        </div>
>>>>>>> 179a06fd51a26fc66dbcb945489b02d1dcd04480
    </div>
</div>

<script>
<<<<<<< HEAD
    let currentEmployee = null;
    let mapMasuk, mapPulang;
    
    function getNamaBulan(bulan) {
        const nama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return nama[parseInt(bulan) - 1];
    }
    
    function formatTanggal(tgl) {
        return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
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
    
    function getAbsensiData(employeeId, bulan, tahun) {
        const allData = {
            1: {
                nama: 'Aulia Pramesti',
                divisi: 'Engineering',
                absensi: [
                    { tgl: `${tahun}-${String(bulan).padStart(2,'0')}-01`, hari: 'Senin', jamMasuk: '08:00', jamPulang: '17:00', status: 'Hadir', keterangan: '-', latMasuk: -6.200000, lngMasuk: 106.816666, latPulang: -6.200000, lngPulang: 106.816666 },
                    { tgl: `${tahun}-${String(bulan).padStart(2,'0')}-02`, hari: 'Selasa', jamMasuk: '08:15', jamPulang: '17:00', status: 'Hadir', keterangan: '-', latMasuk: -6.200000, lngMasuk: 106.816666, latPulang: -6.200000, lngPulang: 106.816666 },
                    { tgl: `${tahun}-${String(bulan).padStart(2,'0')}-03`, hari: 'Rabu', jamMasuk: '08:00', jamPulang: '17:00', status: 'Hadir', keterangan: '-', latMasuk: -6.200000, lngMasuk: 106.816666, latPulang: -6.200000, lngPulang: 106.816666 },
                    { tgl: `${tahun}-${String(bulan).padStart(2,'0')}-04`, hari: 'Kamis', jamMasuk: '-', jamPulang: '-', status: 'Izin', keterangan: 'Acara keluarga', latMasuk: null, lngMasuk: null, latPulang: null, lngPulang: null },
                    { tgl: `${tahun}-${String(bulan).padStart(2,'0')}-05`, hari: 'Jumat', jamMasuk: '08:30', jamPulang: '17:00', status: 'Hadir', keterangan: '-', latMasuk: -6.200000, lngMasuk: 106.816666, latPulang: -6.200000, lngPulang: 106.816666 }
                ]
            },
            2: {
                nama: 'Bimo Santoso',
                divisi: 'Marketing',
                absensi: [
                    { tgl: `${tahun}-${String(bulan).padStart(2,'0')}-01`, hari: 'Senin', jamMasuk: '08:00', jamPulang: '17:00', status: 'Hadir', keterangan: '-', latMasuk: -6.210000, lngMasuk: 106.820000, latPulang: -6.210000, lngPulang: 106.820000 }
                ]
            }
        };
        return allData[employeeId] || allData[1];
    }
    
    function updateStats(data) {
        let hadir = 0, izin = 0, sakit = 0, alpha = 0;
        data.forEach(item => {
            if (item.status === 'Hadir') hadir++;
            else if (item.status === 'Izin') izin++;
            else if (item.status === 'Sakit') sakit++;
            else alpha++;
        });
        document.getElementById('statHadir').innerText = hadir;
        document.getElementById('statIzin').innerText = izin;
        document.getElementById('statSakit').innerText = sakit;
        document.getElementById('statAlpha').innerText = alpha;
    }
    
    function renderTable(data) {
        const tbody = document.getElementById('rekapTableBody');
        tbody.innerHTML = '';
        updateStats(data);
        
        data.forEach(item => {
            const row = `
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 text-sm text-slate-600">${formatTanggal(item.tgl)}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">${item.hari}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">${item.jamMasuk}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">${item.jamPulang}</td>
                    <td class="px-4 py-3">${getStatusBadge(item.status)}</td>
                    <td class="px-4 py-3">
                        <button onclick="showDetail('${item.tgl}', '${item.hari}', '${item.jamMasuk}', '${item.jamPulang}', '${item.status}', '${item.keterangan}', ${item.latMasuk || null}, ${item.lngMasuk || null}, ${item.latPulang || null}, ${item.lngPulang || null})" class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }
    
    function showDetail(tgl, hari, jamMasuk, jamPulang, status, keterangan, latMasuk, lngMasuk, latPulang, lngPulang) {
        document.getElementById('detailTglHari').innerHTML = `${formatTanggal(tgl)} (${hari})`;
        document.getElementById('detailStatusBadge').innerHTML = getStatusBadge(status);
        document.getElementById('detailKeterangan').innerHTML = keterangan !== '-' ? keterangan : 'Tidak ada keterangan';
        document.getElementById('detailTimeMasuk').innerHTML = jamMasuk !== '-' ? jamMasuk : '--:--';
        document.getElementById('detailTimePulang').innerHTML = jamPulang !== '-' ? jamPulang : '--:--';
        
        setTimeout(() => {
            if (mapMasuk) mapMasuk.remove();
            if (mapPulang) mapPulang.remove();
            
            const mapMasukDiv = document.getElementById('detailMapMasuk');
            const mapPulangDiv = document.getElementById('detailMapPulang');
            
            if (latMasuk && lngMasuk) {
                mapMasukDiv.innerHTML = '';
                mapMasuk = L.map('detailMapMasuk').setView([latMasuk, lngMasuk], 15);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapMasuk);
                L.marker([latMasuk, lngMasuk]).addTo(mapMasuk).bindPopup(`<b>Check-In</b><br>Jam: ${jamMasuk}`);
            } else {
                mapMasukDiv.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
            }
            
            if (latPulang && lngPulang) {
                mapPulangDiv.innerHTML = '';
                mapPulang = L.map('detailMapPulang').setView([latPulang, lngPulang], 15);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapPulang);
                L.marker([latPulang, lngPulang]).addTo(mapPulang).bindPopup(`<b>Check-Out</b><br>Jam: ${jamPulang}`);
            } else {
                mapPulangDiv.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400"><i class="bi bi-geo-alt-fill text-2xl"></i><span class="ml-2">Lokasi tidak tersedia</span></div>';
            }
        }, 100);
        
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex');
    }
    
    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }
    
    function loadData() {
        const urlParams = new URLSearchParams(window.location.search);
        const employeeId = urlParams.get('id') || '1';
        const bulan = document.getElementById('bulanSelect').value;
        const tahun = document.getElementById('tahunSelect').value;
        
        const dataAbsen = getAbsensiData(parseInt(employeeId), bulan, tahun);
        currentEmployee = dataAbsen;
        document.getElementById('employeeName').innerHTML = `${dataAbsen.nama}`;
        document.getElementById('employeeInfo').innerHTML = `<i class="bi bi-building"></i> ${dataAbsen.divisi} | Periode: ${getNamaBulan(bulan)} ${tahun}`;
        renderTable(dataAbsen.absensi);
    }
    
    function filterData() {
        const urlParams = new URLSearchParams(window.location.search);
        const employeeId = urlParams.get('id') || '1';
        const bulan = document.getElementById('bulanSelect').value;
        const tahun = document.getElementById('tahunSelect').value;
        
        const dataAbsen = getAbsensiData(parseInt(employeeId), bulan, tahun);
        currentEmployee = dataAbsen;
        renderTable(dataAbsen.absensi);
        document.getElementById('employeeInfo').innerHTML = `<i class="bi bi-building"></i> ${dataAbsen.divisi} | Periode: ${getNamaBulan(bulan)} ${tahun}`;
    }
    
    function exportToPDF() {
        alert('Fitur export PDF akan segera hadir');
    }
    
    document.getElementById('filterBtn')?.addEventListener('click', filterData);
    document.getElementById('exportPdfBtn')?.addEventListener('click', exportToPDF);
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', closeDetailModal);
    });
    
    loadData();
</script>
@endsection      . ini kan aku refresh dulu baru bisa pencet detail  baru muncul ... jangan ada yang dikurangkan kodenya, perbaiki saja yang salah
=======
    // Modal functionality
    const tentangModal = document.getElementById('tentangModal');
    const bantuanModal = document.getElementById('bantuanModal');
    const videoModal = document.getElementById('videoModal');
    const youtubeIframe = document.getElementById('youtubeIframe');
    
    function openModal(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    document.getElementById('tentangBtn').addEventListener('click', () => openModal(tentangModal));
    document.getElementById('bantuanBtn').addEventListener('click', () => openModal(bantuanModal));
    document.getElementById('demoVideoBtn').addEventListener('click', () => {
        openModal(videoModal);
        youtubeIframe.src = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1';
    });
    
    document.querySelectorAll('.close-modal, .close-modal-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(tentangModal);
            closeModal(bantuanModal);
        });
    });
    
    document.querySelector('.close-video').addEventListener('click', () => {
        closeModal(videoModal);
        youtubeIframe.src = '';
    });
    
    window.addEventListener('click', (e) => {
        if (e.target === tentangModal) closeModal(tentangModal);
        if (e.target === bantuanModal) closeModal(bantuanModal);
        if (e.target === videoModal) {
            closeModal(videoModal);
            youtubeIframe.src = '';
        }
    });
</script>
@endsection
>>>>>>> 179a06fd51a26fc66dbcb945489b02d1dcd04480
