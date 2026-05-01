<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StaffLog - @yield('title')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
        }
        
        /* Flex wrapper untuk sticky footer */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            background: #1e293b;
            z-index: 1040;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1039;
            display: none;
        }
        
        .sidebar-backdrop.show {
            display: block;
        }
        
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
            }
        }
        
        /* Main Content Wrapper - Flex Column untuk Sticky Footer */
        .main-wrapper {
            flex: 1;
            margin-left: 0;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        @media (min-width: 768px) {
            .main-wrapper {
                margin-left: 280px;
            }
        }
        
        /* Content grows to fill space, pushing footer down */
        .main-content {
            flex: 1;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #cbd5e1;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .sidebar-link:hover {
            background: #334155;
            color: white;
        }
        
        .sidebar-link.active {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary {
            background: #1e293b;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary:hover {
            background: #334155;
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Footer - akan selalu di bawah karena flex */
        .footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
            margin-top: auto;
        }
        
        .footer p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }
        
        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 24px;
        }
        
        /* Tombol toggle sidebar di mobile */
        .sidebar-toggle-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #475569;
            padding: 8px 12px;
            border-radius: 8px;
        }
        
        .sidebar-toggle-btn:hover {
            background: #f1f5f9;
        }
        
        @media (max-width: 767px) {
            .sidebar-toggle-btn {
                display: inline-flex !important;
            }
        }
        
        @media (min-width: 768px) {
            .sidebar-toggle-btn {
                display: none !important;
            }
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #dc2626;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
            background: white;
            border: 1px solid #fee2e2;
        }
        
        .logout-btn:hover {
            background: #fee2e2;
        }
    </style>
</head>
<body>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<!-- Sidebar -->
<div id="adminSidebar" class="sidebar">
    <div class="p-5 border-b border-slate-700 text-center">
        <div class="text-xs font-semibold text-slate-400 mt-2">StaffLog.adl</div>
    </div>
    <nav class="p-4">
        <a href="{{ route('admin.rekap-karyawan') }}" class="sidebar-link" id="menuRekap">
            <i class="bi bi-people"></i> Rekap Karyawan
        </a>
        <a href="{{ route('admin.tambah-karyawan') }}" class="sidebar-link" id="menuTambah">
            <i class="bi bi-person-plus"></i> Tambah Karyawan
        </a>
        <a href="{{ route('admin.notifikasi') }}" class="sidebar-link" id="menuNotif">
            <i class="bi bi-bell"></i> Notifikasi
        </a>
    </nav>
</div>

<!-- Main Wrapper dengan Flex Column -->
<div class="main-wrapper">
    <!-- Header -->
    <div class="header">
        <div>
            <button id="sidebarToggleBtn" class="sidebar-toggle-btn">
                <i class="bi bi-list"></i>
            </button>
        </div>
        <a href="{{ route('login') }}" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
    
    <!-- Content - flex:1 agar mengisi ruang -->
    <div class="main-content">
        <div class="container-custom">
            @yield('content')
        </div>
    </div>
    
    <!-- Footer - akan selalu di bawah karena margin-top: auto -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} StaffLog.adl - Sistem Manajemen Kehadiran Karyawan</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        
        function toggleSidebar() {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }
        
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });
        }
        
        // Set active menu based on current URL
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            if (currentPath.includes(link.getAttribute('href'))) {
                link.classList.add('active');
            }
        });
        
        // Desktop: sidebar always visible
        if (window.innerWidth >= 768) {
            sidebar.classList.add('show');
        }
        
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.add('show');
                backdrop.classList.remove('show');
            } else {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>