<?php $__env->startSection('title', 'Rekap Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Data Karyawan</h1>
        <p class="text-slate-500 text-sm">Kelola data karyawan yang terdaftar</p>
    </div>
    
    <!-- Tombol Tambah -->
    <div class="mb-4">
        <a href="<?php echo e(route('admin.tambah-karyawan')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Karyawan
        </a>
    </div>
    
    <!-- Tabel Data Karyawan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">NO</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">NAMA</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">DIVISI</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ID KARYAWAN</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($index + 1); ?></td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800"><?php echo e($emp->nama_lengkap); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($emp->devisi->nama_devisi ?? '-'); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($emp->id_karyawan ?? '-'); ?></td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-3">
                            <!-- Tombol Edit (Pensil) -->
                            <a href="<?php echo e(url('/admin/edit-karyawan/' . $emp->id_pengguna)); ?>" class="text-blue-600 hover:text-blue-800" title="Edit Karyawan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <!-- Tombol Detail (Kalender) -->
                            <a href="<?php echo e(url('/admin/rekap-karyawan/' . $emp->id_pengguna)); ?>" class="text-green-600 hover:text-green-800" title="Detail Kehadiran">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data karyawan</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\StaffLog.adl\StaffLog.adl\resources\views/admin/rekap-karyawan.blade.php ENDPATH**/ ?>