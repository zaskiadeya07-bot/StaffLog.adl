<?php $__env->startSection('title', 'Rekap Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Karyawan</h1>
            <p class="text-slate-500 text-sm">Kelola data karyawan yang terdaftar</p>
        </div>
        <a href="<?php echo e(route('admin.tambah-karyawan')); ?>" class="btn-primary inline-flex items-center gap-2">
            <i class="bi bi-person-plus"></i> Tambah Karyawan
        </a>
    </div>
    
    <?php if(session('success')): ?>
        <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg mb-4 flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">ID Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nama Lengkap</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Username</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nomor HP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $karyawan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($index + 1); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono"><?php echo e($k->id_karyawan ?? '-'); ?></td>
                            <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($k->nama_lengkap); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($k->username); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                
                                <?php if($k->divisi_nama): ?>
                                    <?php echo e($k->divisi_nama); ?>

                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($k->nomor_hp ?? '-'); ?></td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="<?php echo e(route('admin.detail-rekap-kehadiran', $k->id_pengguna)); ?>" 
                                       class="bg-emerald-50 text-emerald-600 p-2 rounded-lg hover:bg-emerald-100 transition"
                                       title="Lihat Kehadiran">
                                        <i class="bi bi-calendar-check"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.edit-karyawan', $k->id_pengguna)); ?>" 
                                       class="bg-amber-50 text-amber-600 p-2 rounded-lg hover:bg-amber-100 transition"
                                       title="Edit Karyawan">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button onclick="showDeleteModal(<?php echo e($k->id_pengguna); ?>, '<?php echo e($k->nama_lengkap); ?>')" 
                                            class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition"
                                            title="Hapus Karyawan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                <i class="bi bi-inbox text-4xl"></i>
                                <p class="mt-2">Belum ada data karyawan</p>
                                <a href="<?php echo e(route('admin.tambah-karyawan')); ?>" class="text-blue-500 hover:underline mt-2 inline-block">
                                    Tambah karyawan sekarang
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-xl">
        <div class="bg-red-600 text-white p-4 rounded-t-2xl">
            <h3 class="font-bold text-lg flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                Konfirmasi Hapus
            </h3>
        </div>
        <div class="p-6">
            <p class="text-slate-700">Apakah Anda yakin ingin menghapus karyawan <strong id="deleteName"></strong>?</p>
            <p class="text-slate-400 text-sm mt-2">Data yang dihapus tidak dapat dikembalikan.</p>
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
                Batal
            </button>
            <form id="deleteForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function showDeleteModal(id, name) {
        document.getElementById('deleteName').innerText = name;
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = '/admin/hapus-karyawan/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\StaffLog.adl\resources\views/admin/rekap-karyawan.blade.php ENDPATH**/ ?>