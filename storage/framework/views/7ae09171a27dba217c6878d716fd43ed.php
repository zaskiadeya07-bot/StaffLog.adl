<?php
    // Pastikan tidak ada @extends atau @include ke file sendiri
?>

<tr class="border-b border-slate-100 hover:bg-slate-50">
    <td class="px-4 py-3 text-sm"><?php echo e($index ?? ''); ?></td>
    <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($employee['name'] ?? ''); ?></td>
    <td class="px-4 py-3 text-slate-600"><?php echo e($employee['division'] ?? ''); ?></td>
    <td class="px-4 py-3">
        <button 
            type="button"
            class="detail-button rounded-lg bg-blue-500 px-3 py-1.5 text-sm text-white transition hover:bg-blue-600"
            data-name="<?php echo e($employee['name'] ?? ''); ?>"
            data-division="<?php echo e($employee['division'] ?? ''); ?>"
        >
            Detail
        </button>
    </td>
</tr><?php /**PATH C:\laragon\www\rekapkehadiran\resources\views/partials/admin/employee-row.blade.php ENDPATH**/ ?>