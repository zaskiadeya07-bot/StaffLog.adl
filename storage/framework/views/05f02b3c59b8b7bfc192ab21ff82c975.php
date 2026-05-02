<?php $__env->startSection('content'); ?>
<?php
    $employees = [
        ['name' => 'Aulia Pramesti', 'division' => 'Engineering'],
        ['name' => 'Bimo Santoso', 'division' => 'Marketing'],
        ['name' => 'Citra Nabila', 'division' => 'HR'],
        ['name' => 'Danu Mahendra', 'division' => 'Finance'],
        ['name' => 'Eka Wicaksono', 'division' => 'Operations'],
        ['name' => 'Fitri Amelia', 'division' => 'Engineering'],
        ['name' => 'Gilang Putra', 'division' => 'Finance'],
        ['name' => 'Hana Maharani', 'division' => 'Marketing'],
        ['name' => 'Indra Saputra', 'division' => 'HR'],
        ['name' => 'Jasmine Ayu', 'division' => 'Engineering'],
    ];
?>

<section class="space-y-5">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Rekap Karyawan</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau daftar karyawan dan akses detail presensi dengan cepat.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <label for="employee-search" class="mb-2 block text-sm font-semibold text-slate-700">Cari Karyawan</label>
        <input
            id="employee-search"
            type="search"
            placeholder="Cari nama karyawan..."
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none ring-blue-200 transition focus:border-blue-500 focus:ring"
        >
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-blue-50 text-left text-sm font-bold text-blue-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Divisi</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="employee-table-body" class="bg-white">
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.admin.employee-row', [
                            'index' => $loop->iteration,
                            'employee' => $employee,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <p id="employee-no-result" class="hidden px-4 py-4 text-sm text-slate-500">Karyawan tidak ditemukan.</p>
    </div>
</section>

<?php echo $__env->make('Admin.ModalDetailAbsensi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const searchInput = document.getElementById('employee-search');
        const body = document.getElementById('employee-table-body');
        const noResult = document.getElementById('employee-no-result');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const rows = Array.from(body.querySelectorAll('tr'));
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const nameCell = row.children[1];
                    const employeeName = nameCell ? nameCell.textContent.trim().toLowerCase() : '';
                    const isVisible = employeeName.includes(query);
                    row.classList.toggle('hidden', !isVisible);
                    if (isVisible) visibleCount++;
                });

                noResult.classList.toggle('hidden', visibleCount !== 0);
            });
        }
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
    'role' => 'admin',
    'pageTitle' => 'Rekap Karyawan'
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\rekapkehadiran\resources\views/Admin/RekapKehadiran.blade.php ENDPATH**/ ?>