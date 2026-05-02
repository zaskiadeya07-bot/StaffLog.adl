<?php $__env->startSection('title', 'Landing Page'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex flex-col">
    <!-- Header - Tombol Masuk lebih ke kanan -->
    <header class="bg-slate-800 sticky top-0 z-50 shadow-lg">
        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Bootstrap Icons CDN -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <div class="flex justify-end px-6 py-4">
            <a href="<?php echo e(route('login')); ?>" class="bg-amber-400 text-slate-800 px-6 py-2 rounded-full font-semibold hover:bg-amber-500 transition flex items-center gap-2 mr-2">
                <i class="bi bi-box-arrow-in-right"></i>
                Masuk
            </a>
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
                    <a href="<?php echo e(route('login')); ?>" class="text-slate-300 hover:text-amber-400 transition">Masuk ke Akun</a>
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

<script>
    // Modal functionality
    const tentangModal = document.getElementById('tentangModal');
    const bantuanModal = document.getElementById('bantuanModal');
    
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
    
    document.querySelectorAll('.close-modal, .close-modal-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(tentangModal);
            closeModal(bantuanModal);
        });
    });
    
    window.addEventListener('click', (e) => {
        if (e.target === tentangModal) closeModal(tentangModal);
        if (e.target === bantuanModal) closeModal(bantuanModal);
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\stafflog-app\resources\views/landing/index.blade.php ENDPATH**/ ?>