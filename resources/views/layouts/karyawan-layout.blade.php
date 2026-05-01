<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StaffLog - @yield('title', 'Karyawan Panel')</title>
    
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
        
        /* Wrapper Flex */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
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
        
        /* Main Wrapper */
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
        
        /* Content */
        .main-content {
            flex: 1;
            padding: 24px;
        }
        
        /* Sidebar Links */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #cbd5e1;
            border-radius: 12px;
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
        
        /* Footer */
        .footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
        }
        
        .footer p {
            color: #64748b;
            font-size: 14px;
        }
        
        /* Buttons */
        .btn-primary {
            background: #1e293b;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary:hover {
            background: #334155;
        }
        
        .btn-checkin {
            background: #10b981;
            color: white;
            padding: 12px 32px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        
        .btn-checkin:hover {
            background: #059669;
        }
        
        .btn-checkout {
            background: #ef4444;
            color: white;
            padding: 12px 32px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        
        .btn-checkout:hover {
            background: #dc2626;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }
        
        /* Grid */
        .grid {
            display: grid;
        }
        
        .grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        
        @media (max-width: 768px) {
            .grid-cols-2, .grid-cols-3, .grid-cols-4 {
                grid-template-columns: 1fr;
            }
            .main-content {
                padding: 16px;
            }
        }
        
        /* Utilities */
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .text-center { text-align: center; }
        
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-5 { margin-bottom: 20px; }
        .mb-6 { margin-bottom: 24px; }
        
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        
        .p-3 { padding: 12px; }
        .p-4 { padding: 16px; }
        .p-5 { padding: 20px; }
        .p-6 { padding: 24px; }
        
        .px-4 { padding-left: 16px; padding-right: 16px; }
        .px-5 { padding-left: 20px; padding-right: 20px; }
        
        .py-2 { padding-top: 8px; padding-bottom: 8px; }
        .py-3 { padding-top: 12px; padding-bottom: 12px; }
        
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        
        .text-sm { font-size: 14px; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 20px; }
        .text-2xl { font-size: 24px; }
        .text-3xl { font-size: 30px; }
        .text-5xl { font-size: 48px; }
        
        .text-white { color: white; }
        .text-slate-800 { color: #1e293b; }
        .text-slate-700 { color: #334155; }
        .text-slate-600 { color: #475569; }
        .text-slate-500 { color: #64748b; }
        .text-slate-400 { color: #94a3b8; }
        .text-slate-300 { color: #cbd5e1; }
        
        .bg-white { background: white; }
        .bg-slate-800 { background: #1e293b; }
        .bg-slate-50 { background: #f8fafc; }
        .bg-emerald-50 { background: #ecfdf5; }
        .bg-emerald-100 { background: #d1fae5; }
        .bg-amber-50 { background: #fffbeb; }
        .bg-amber-100 { background: #fef3c7; }
        .bg-blue-50 { background: #eff6ff; }
        .bg-blue-100 { background: #dbeafe; }
        .bg-red-50 { background: #fef2f2; }
        .bg-red-100 { background: #fee2e2; }
        
        .rounded-lg { border-radius: 8px; }
        .rounded-xl { border-radius: 12px; }
        .rounded-2xl { border-radius: 16px; }
        
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        
        .w-12 { width: 48px; }
        .h-12 { height: 48px; }
        .w-full { width: 100%; }
        
        .border { border-width: 1px; }
        .border-2 { border-width: 2px; }
        .border-slate-700 { border-color: #334155; }
        .border-slate-200 { border-color: #e2e8f0; }
        .border-emerald-200 { border-color: #a7f3d0; }
        .border-red-200 { border-color: #fecaca; }
        .border-amber-200 { border-color: #fde68a; }
        
        .hover\:bg-emerald-100:hover { background: #d1fae5; }
        .hover\:bg-red-100:hover { background: #fee2e2; }
        .hover\:bg-amber-100:hover { background: #fef3c7; }
        .hover\:bg-slate-100:hover { background: #f1f5f9; }
        
        .transition { transition: all 0.2s ease; }
        
        .hidden { display: none; }
        
        @media (min-width: 640px) {
            .sm\:block { display: block; }
            .sm\:grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        }
        
        @media (min-width: 768px) {
            .md\:grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
            .md\:block { display: block; }
        }
        
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
            background: white;
            border: 1px solid #fee2e2;
        }
        
        .logout-btn:hover {
            background: #fee2e2;
        }
        
        .clock-card {
            background: #1e293b;
            border-radius: 12px;
            padding: 12px 24px;
            display: inline-block;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<!-- Sidebar -->
<div id="karyawanSidebar" class="sidebar">
    <div class="p-5 border-b border-slate-700 text-center">
        <div class="text-xs font-semibold text-slate-400 mt-2">StaffLog.adl</div>
    </div>
    <nav class="p-4">
        <a href="{{ url('/karyawan/dashboard') }}" class="sidebar-link">
            <i class="bi bi-speedometer2 text-xl"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ url('/karyawan/rekap-absen') }}" class="sidebar-link">
            <i class="bi bi-calendar-check text-xl"></i>
            <span>Rekap Kehadiran</span>
        </a>
        <a href="{{ url('/karyawan/izin-cuti') }}" class="sidebar-link">
            <i class="bi bi-file-text text-xl"></i>
            <span>Izin & Cuti</span>
        </a>
        <a href="{{ url('/karyawan/profile') }}" class="sidebar-link">
            <i class="bi bi-person-circle text-xl"></i>
            <span>Profile</span>
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-wrapper">
    <div class="header">
        <div class="flex items-center gap-3">
            <button id="sidebarToggleBtn" class="sidebar-toggle-btn">
                <i class="bi bi-list text-2xl"></i>
            </button>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-600 hidden sm:block">Halo, <span id="userName">Karyawan</span></span>
            <a href="{{ url('/login') }}" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <div class="main-content">
        @yield('content')
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} StaffLog.adl - Sistem Manajemen Kehadiran Karyawan</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('karyawanSidebar');
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
        
        // Set active menu
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            if (currentPath.includes(link.getAttribute('href'))) {
                link.classList.add('active');
            }
        });
        
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
        
        // Load user name
        const userName = localStorage.getItem('userName') || 'Karyawan';
        const userNameSpan = document.getElementById('userName');
        if (userNameSpan) {
            userNameSpan.innerText = userName;
        }
    });
</script>

@stack('scripts')
</body>
</html>