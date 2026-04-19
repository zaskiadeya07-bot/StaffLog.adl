<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? 'StaffLog Dashboard' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            crossorigin=""
        />

        @stack('head')
    </head>
    @php
        $role = $role ?? 'karyawan';
        $userName = $userName ?? 'Dina Andini';
        $logoText = $role === 'admin' ? 'AdminStaffLog' : 'StaffLog.adl';
        $menuItems = $role === 'admin'
            ? [
                ['route' => 'admin.rekap-karyawan', 'label' => 'Rekap Karyawan', 'icon' => 'rekap'],
                ['route' => 'admin.tambah-karyawan', 'label' => 'Tambah Karyawan', 'icon' => 'tambah'],
            ]
            : [
                ['route' => 'karyawan.profil', 'label' => 'Profil', 'icon' => 'profil'],
                ['route' => 'karyawan.check-in', 'label' => 'Check-In', 'icon' => 'checkin'],
                ['route' => 'karyawan.check-out', 'label' => 'Check-Out', 'icon' => 'checkout'],
                ['route' => 'karyawan.riwayat-presensi', 'label' => 'Riwayat Presensi', 'icon' => 'riwayat'],
            ];
        $avatarInitials = collect(explode(' ', $userName))
            ->filter()
            ->take(2)
            ->map(fn (string $namePart) => mb_strtoupper(mb_substr($namePart, 0, 1)))
            ->implode('');
    @endphp
    <body class="min-h-screen bg-white text-slate-800" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="relative min-h-screen overflow-x-hidden">
            <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_8%_10%,rgba(37,99,235,0.16),transparent_36%),radial-gradient(circle_at_95%_18%,rgba(59,130,246,0.1),transparent_32%),linear-gradient(to_bottom,#ffffff,#f7fbff)]"></div>

            <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/40 md:hidden"></div>

            <aside
                id="dashboard-sidebar"
                class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-blue-100 bg-white/95 p-5 shadow-xl backdrop-blur transition-transform duration-300 md:translate-x-0"
            >
                <div class="mb-8 flex items-center justify-between">
                    <span class="text-xl font-extrabold tracking-tight text-blue-700">{{ $logoText }}</span>
                    <button id="sidebar-close" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 md:hidden" type="button" aria-label="Close sidebar">
                        ✕
                    </button>
                </div>

                <nav class="space-y-2">
                    @foreach ($menuItems as $menuItem)
                        @include('partials.dashboard.sidebar-item', [
                            'href' => route($menuItem['route']),
                            'label' => $menuItem['label'],
                            'icon' => $menuItem['icon'],
                            'active' => request()->routeIs($menuItem['route']),
                        ])
                    @endforeach
                </nav>
            </aside>

            <div class="md:pl-72">
                <header class="sticky top-0 z-20 border-b border-blue-100 bg-white/90 px-4 py-4 backdrop-blur sm:px-6">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <button id="sidebar-toggle" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 md:hidden" type="button" aria-label="Open sidebar">
                                ☰
                            </button>
                            <div>
                                <p class="text-lg font-extrabold tracking-tight text-blue-700">{{ $logoText }}</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $pageTitle ?? 'Halaman Dashboard' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-800">{{ $userName }}</p>
                                <p class="text-xs text-slate-500">{{ $role === 'admin' ? 'Administrator' : 'Karyawan' }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                {{ $avatarInitials !== '' ? $avatarInitials : 'SL' }}
                            </div>
                        </div>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8">
                    @yield('content')
                </main>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            (function () {
                const sidebar = document.getElementById('dashboard-sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                const openButton = document.getElementById('sidebar-toggle');
                const closeButton = document.getElementById('sidebar-close');

                const openSidebar = function () {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                };

                const closeSidebar = function () {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                };

                if (openButton) {
                    openButton.addEventListener('click', openSidebar);
                }

                if (closeButton) {
                    closeButton.addEventListener('click', closeSidebar);
                }

                if (backdrop) {
                    backdrop.addEventListener('click', closeSidebar);
                }

                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 768) {
                        backdrop.classList.add('hidden');
                        sidebar.classList.remove('-translate-x-full');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                    }
                });
            })();
        </script>
        @stack('scripts')
    </body>
</html>
