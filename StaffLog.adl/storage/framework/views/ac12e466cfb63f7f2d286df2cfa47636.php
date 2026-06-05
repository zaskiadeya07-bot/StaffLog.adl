<?php $__env->startSection('title', 'Detail Rekap Kehadiran'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800" id="employeeName"><?php echo e($employee->nama_lengkap); ?></h1>
            <p class="text-slate-500 text-sm" id="employeeInfo">
                <i class="bi bi-building"></i> <?php echo e($employee->devisi->nama_devisi ?? '-'); ?> | 
                ID: <?php echo e($employee->id_karyawan ?? '-'); ?> |
                Periode: <span id="periodeText">-</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(url('/admin/rekap-karyawan')); ?>" class="btn-secondary inline-flex items-center gap-2">
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
    
    <!-- Statistik Cards (DIPERBAIKI) -->
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
    <div class="bg-white rounded-3xl max-w-lg w-full mx-4">
        <div class="bg-slate-800 p-5 rounded-t-3xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Detail Absensi</h3>
                <button class="close-modal text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
        </div>
        <div class="p-6">
            <p><strong>ID Absensi:</strong> <span id="detailId">-</span></p>
            <p><strong>Tanggal & Hari:</strong> <span id="detailTglHari">-</span></p>
            <p><strong>Jam Masuk:</strong> <span id="detailTimeMasuk">-</span></p>
            <p><strong>Jam Pulang:</strong> <span id="detailTimePulang">-</span></p>
            <p><strong>Status:</strong> <span id="detailStatus">-</span></p>
            <p><strong>Keterlambatan:</strong> <span id="detailTerlambat">-</span></p>
            <p><strong>Keterangan:</strong> <span id="detailKeterangan">-</span></p>
        </div>
        <div class="p-5 border-t text-center">
            <button class="close-modal btn-secondary px-6">Tutup</button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script>
    const employeeId = <?php echo e($employee->id_pengguna); ?>;
    
    function getNamaBulan(bulan) {
        const nama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return nama[parseInt(bulan) - 1];
    }
    
    function getStatusBadge(status) {
        const badges = {
            'Hadir': '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">✅ Hadir</span>',
            'Izin': '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">📝 Izin</span>',
            'Sakit': '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">🤒 Sakit</span>',
            'Alpha': '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">❌ Alpha</span>'
        };
        return badges[status] || status;
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
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-400">📋 Tidak ada data untuk periode ini</td></tr>';
                    return;
                }
                
                tbody.innerHTML = '';
                for (let i = 0; i < result.rows.length; i++) {
                    const item = result.rows[i];
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 border-b">${item.tanggal_formatted}</td>
                            <td class="px-4 py-3 border-b">${item.hari}</td>
                            <td class="px-4 py-3 border-b">${item.jamMasuk}</td>
                            <td class="px-4 py-3 border-b">${item.jamPulang}</td>
                            <td class="px-4 py-3 border-b">${getStatusBadge(item.status)}</td>
                            <td class="px-4 py-3 border-b">
                                <button onclick="showDetail(${item.id})" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                    👁️ Detail
                                </button>
                            </td>
                        </tr>
                    `;
                }
            } else {
                alert('Gagal memuat data');
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('rekapTableBody').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500">⚠️ Gagal memuat data</td></tr>';
        }
    }
    
    async function showDetail(id) {
        try {
            const response = await fetch('/admin/rekap-detail/' + id);
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('detailId').innerHTML = id;
                document.getElementById('detailTglHari').innerHTML = result.data.tanggal_formatted + ' (' + result.data.hari + ')';
                document.getElementById('detailTimeMasuk').innerHTML = result.data.jamMasuk;
                document.getElementById('detailTimePulang').innerHTML = result.data.jamPulang;
                document.getElementById('detailStatus').innerHTML = result.data.status;
                document.getElementById('detailTerlambat').innerHTML = result.data.menit_terlambat ? result.data.menit_terlambat + ' menit' : 'Tepat waktu';
                document.getElementById('detailKeterangan').innerHTML = result.data.keterangan || 'Tidak ada keterangan';
                
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
    
    const closeButtons = document.querySelectorAll('.close-modal');
    for (let i = 0; i < closeButtons.length; i++) {
        closeButtons[i].addEventListener('click', closeDetailModal);
    }
    
    // Load data saat halaman pertama kali dibuka
    loadData();
</script>
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\StaffLog.adl\StaffLog.adl\resources\views/admin/detail-rekap-backend.blade.php ENDPATH**/ ?>