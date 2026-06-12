<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StaffLog Admin - @yield('title', 'Panel Admin')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px;
            height: 100%;
            background: #1e293b;
            z-index: 1040;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        .sidebar.show { transform: translateX(0); }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1039;
            display: none;
        }
        .sidebar-backdrop.show { display: block; }

        @media (min-width: 768px) {
            .sidebar { transform: translateX(0); }
            .sidebar-backdrop { display: none !important; }
        }

        /* Main wrapper */
        .main-wrapper {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        @media (min-width: 768px) {
            .main-wrapper { margin-left: 260px; }
        }

        /* Header */
        .header {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        /* Sidebar Links */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: #cbd5e1;
            border-radius: 10px;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 14px;
        }
        .sidebar-link:hover { background: #334155; color: white; }
        .sidebar-link.active { background: #2563eb; color: white; }

        /* Content */
        .main-content { flex: 1; padding: 24px; }

        /* Footer */
        .footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        /* Buttons */
        .btn-primary {
            background: #1e293b;
            color: white;
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #334155; }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-secondary:hover { background: #e2e8f0; }

        /* Input */
        .input-field {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            background: white;
            transition: border 0.2s;
        }
        .input-field:focus { border-color: #94a3b8; }

        /* Logout */
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #dc2626;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            background: white;
            border: 1px solid #fee2e2;
            font-size: 14px;
            transition: background 0.2s;
        }

        /* Toggle button mobile */
        .sidebar-toggle-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #475569;
            padding: 6px 10px;
            border-radius: 8px;
            display: none;
        }
        .sidebar-toggle-btn:hover { background: #f1f5f9; }
        @media (max-width: 767px) { .sidebar-toggle-btn { display: inline-flex !important; } }

        @media (max-width: 768px) {
            .main-content { padding: 16px; }
        }

        /* ── Animations ─────────────────────────────────────── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .main-content {
            animation: fadeInUp 0.45s ease-out;
        }
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .btn-primary, .btn-secondary {
            transition: background 0.2s, transform 0.15s;
        }
        .btn-primary:active, .btn-secondary:active {
            transform: scale(0.96);
        }
        .sidebar-link {
            transition: background 0.2s, color 0.2s, transform 0.15s;
        }
        .sidebar-link:active {
            transform: scale(0.96);
        }
    </style>
</head>
<body>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<!-- Sidebar -->
<div id="adminSidebar" class="sidebar">
    <div class="p-5 border-b border-slate-700">
        <h2 class="text-white font-bold text-lg">StaffLog.adl</h2>
        <p class="text-slate-400 text-xs mt-1">Admin </p>
    </div>
    <nav class="p-3 space-y-1 mt-2">
        <a href="{{ route('admin.rekap-karyawan') }}" class="sidebar-link">
            <i class="bi bi-people text-lg"></i>
            <span>Data Karyawan</span>
        </a>
        <a href="{{ route('admin.notifikasi') }}" class="sidebar-link">
            <i class="bi bi-bell text-lg"></i>
            <span>Notifikasi Perizinan</span>
        </a>
        <a href="{{ route('admin.pengaturan-kantor') }}" class="sidebar-link">
            <i class="bi bi-building-gear text-lg"></i>
            <span>Pengaturan Kantor</span>
        </a>
    </nav>
</div>

<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Header -->
    <div class="header">
        <div class="flex items-center gap-3">
            <button id="sidebarToggleBtn" class="sidebar-toggle-btn">
                <i class="bi bi-list"></i>
            </button>
            <span class="text-slate-700 font-semibold text-sm">@yield('title', 'Panel Admin')</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500 hidden sm:block">
                Halo, <strong>{{ session('pengguna_nama', 'Admin') }}</strong>
            </span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; {{ date('Y') }} StaffLog.adl — Sistem Manajemen Kehadiran Karyawan
    </div>

</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar  = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggleBtn');

        function openSidebar()  { sidebar.classList.add('show');    backdrop.classList.add('show'); }
        function closeSidebar() { sidebar.classList.remove('show'); backdrop.classList.remove('show'); }

        if (toggleBtn)  toggleBtn.addEventListener('click', () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar());
        if (backdrop)   backdrop.addEventListener('click', closeSidebar);

        // Aktifkan menu sesuai URL
        const path = window.location.pathname;
        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (path.startsWith(link.getAttribute('href'))) link.classList.add('active');
        });

        // Desktop: langsung tampilkan sidebar
        if (window.innerWidth >= 768) openSidebar();

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) { openSidebar(); backdrop.classList.remove('show'); }
            else closeSidebar();
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('js/helpers.js') }}"></script>
@stack('scripts')
</body>
</html>
