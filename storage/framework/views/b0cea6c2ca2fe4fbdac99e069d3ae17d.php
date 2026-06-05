<?php $__env->startSection('title', 'Detail Rekap Kehadiran'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800" id="employeeName">
                Detail Kehadiran: <?php echo e($karyawan->nama_lengkap ?? 'Karyawan'); ?>

            </h1>
            <p class="text-slate-500 text-sm" id="employeeInfo">
                <i class="bi bi-building"></i> 
                <?php
                    $divisiName = DB::table('devisi')->where('id_devisi', $karyawan->divisi)->first();
                ?>
                <?php echo e($divisiName->nama_devisi ?? '-'); ?> | Periode: <?php echo e($bulanNama ?? date('F')); ?> <?php echo e($tahun ?? date('Y')); ?>

            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.rekap-karyawan')); ?>" class="btn-secondary inline-flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="card mb-6">
        <div class="p-4">
            <form method="GET" action="<?php echo e(route('admin.detail-rekap-kehadiran', $karyawan->id_pengguna)); ?>" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Bulan</label>
                    <select name="bulan" class="input-field">
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(($bulan ?? date('m')) == $i ? 'selected' : ''); ?>>
                                <?php echo e(date('F', mktime(0, 0, 0, $i, 1))); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Tahun</label>
                    <select name="tahun" class="input-field">
                        <?php for($i = 2022; $i <= 2026; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(($tahun ?? date('Y')) == $i ? 'selected' : ''); ?>>
                                <?php echo e($i); ?>

                            </option>
                        <?php endfor; ?>
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
    
    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-emerald-600 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Hadir / Tepat Waktu</h6>
                    <h2 class="text-3xl font-bold"><?php echo e($statHadir ?? 0); ?></h2>
                </div>
                <i class="bi bi-check-circle text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-amber-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Terlambat</h6>
                    <h2 class="text-3xl font-bold"><?php echo e($statTerlambat ?? 0); ?></h2>
                </div>
                <i class="bi bi-clock-history text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-blue-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Izin / Sakit</h6>
                    <h2 class="text-3xl font-bold"><?php echo e($statIzin ?? 0); ?></h2>
                </div>
                <i class="bi bi-pencil-square text-3xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-slate-500 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-sm opacity-90 mb-1">Alpha</h6>
                    <h2 class="text-3xl font-bold"><?php echo e($statAlpha ?? 0); ?></h2>
                </div>
                <i class="bi bi-x-circle text-3xl opacity-50"></i>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Hari</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Keluar</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $presensi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?php echo e(\Carbon\Carbon::parse($p->tanggal)->format('d/m/Y')); ?>

                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?php echo e(\Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd')); ?>

                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">
                                <?php echo e($p->jam_masuk ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">
                                <?php echo e($p->jam_keluar ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <?php if($p->status_kehadiran == 'hadir'): ?>
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs">
                                        <i class="bi bi-check-circle"></i> Hadir
                                    </span>
                                <?php elseif($p->status_kehadiran == 'terlambat'): ?>
                                    <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs">
                                        <i class="bi bi-clock-history"></i> Terlambat
                                    </span>
                                <?php elseif($p->status_kehadiran == 'izin'): ?>
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">
                                        <i class="bi bi-pencil-square"></i> Izin
                                    </span>
                                <?php else: ?>
                                    <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-full text-xs">
                                        <i class="bi bi-x-circle"></i> Alpha
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?php echo e($p->menit_terlambat > 0 ? $p->menit_terlambat . ' menit' : '-'); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                <i class="bi bi-inbox text-4xl"></i>
                                <p class="mt-2">Belum ada data kehadiran untuk periode ini</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
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
                        <div class="flex items-center justify-center h-full text-slate-400">
                            <i class="bi bi-geo-alt-fill text-2xl"></i>
                            <span class="ml-2">Lokasi tidak tersedia</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Lokasi Check-Out</label>
                    <div id="detailMapPulang" class="h-48 bg-slate-100 rounded-xl overflow-hidden relative">
                        <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full">
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
            <button class="close-modal btn-secondary px-6">Tutup</button>
        </div>
    </div>
</div>

<script>
    let mapMasuk, mapPulang;
    
    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }
    
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', closeDetailModal);
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\StaffLog.adl\resources\views/admin/detail-rekap-kehadiran.blade.php ENDPATH**/ ?>