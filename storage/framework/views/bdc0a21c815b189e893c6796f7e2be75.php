<aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full transform bg-white shadow-xl transition-transform duration-300 ease-in-out md:translate-x-0">
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between p-4 border-b">
            <span class="text-xl font-bold text-blue-600"><?php echo e($logoText); ?></span>
            <button id="sidebar-close" class="md:hidden text-gray-500">&times;</button>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($item['route'])); ?>" class="block px-4 py-2 rounded-lg hover:bg-blue-50 transition">
                    <?php echo e($item['label']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </div>
</aside><?php /**PATH C:\laragon\www\rekapkehadiran\resources\views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>