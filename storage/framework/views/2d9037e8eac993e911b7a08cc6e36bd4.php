 

<?php $__env->startSection('title', 'Detail Rekap Kehadiran'); ?>

<?php $__env->startSection('content'); ?>
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
            <a href="<?php echo e(route('admin.rekap-karyawan')); ?>" class="btn-secondary inline-flex items-center gap-2">
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
    </div>
</div>

<script>
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
<?php $__env->stopSection(); ?>      . ini kan aku refresh dulu baru bisa pencet detail  baru muncul ... jangan ada yang dikurangkan kodenya, perbaiki saja yang salah
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\stafflog-app\resources\views/layouts/admin-layout.blade.php ENDPATH**/ ?>