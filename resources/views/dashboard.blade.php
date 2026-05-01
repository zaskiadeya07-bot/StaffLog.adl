<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern HR Presence - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#f0f4f8] font-sans antialiased text-slate-900">

    <nav class="sticky top-0 z-50 bg-white/70 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-indigo-600 rounded-lg shadow-indigo-200 shadow-lg">
                            <i class="fas fa-fingerprint text-white"></i>
                        </div>
                        <span class="text-xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">
                            HRPresence
                        </span>
                    </div>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="#" class="px-4 py-2 text-sm font-bold text-indigo-600 bg-indigo-50 rounded-full">Dashboard</a>
                        <a href="#" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">Divisi</a>
                        <a href="#" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">Karyawan</a>
                        <a href="#" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">Laporan</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 text-slate-400 hover:text-indigo-600 relative">
                        <i class="far fa-bell text-xl"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Admin+HR&background=4f46e5&color=fff" class="w-9 h-9 rounded-full ring-2 ring-indigo-50">
                        <div class="hidden sm:block">
                            <p class="text-xs font-bold leading-none">Admin HR</p>
                            <p class="text-[10px] text-slate-500 mt-1">Super Admin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Halo, Selamat Pagi! 👋</h1>
                <p class="text-slate-500 mt-1">Berikut adalah ringkasan aktivitas absensi Coffee Shop hari ini.</p>
            </div>
            <div class="flex gap-3">
                <button class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold text-sm shadow-sm hover:bg-slate-50 transition">
                    <i class="fas fa-calendar-alt text-indigo-500"></i>
                    09 Maret 2026
                </button>
                <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-xl shadow-slate-200 hover:bg-slate-800 transition">
                    <i class="fas fa-plus"></i>
                    Tambah Data
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="glass-card p-6 rounded-[2rem] shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-user-friends text-xl"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Karyawan Aktif</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-black text-slate-900 mt-1">12</h3>
                    <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-0.5 rounded-full">+2 baru</span>
                </div>
            </div>

            <div class="glass-card p-6 rounded-[2rem] shadow-sm hover:shadow-md transition group border-l-4 border-l-green-500">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Hadir (Radius)</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-black text-slate-900 mt-1">10</h3>
                    <span class="text-xs font-bold text-slate-400">83% Total</span>
                </div>
            </div>

            <div class="glass-card p-6 rounded-[2rem] shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-notes-medical text-xl"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Izin & Sakit</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-black text-slate-900 mt-1">02</h3>
                    <span class="text-xs font-bold text-amber-500">Perlu Review</span>
                </div>
            </div>

            <div class="glass-card p-6 rounded-[2rem] shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Terlambat</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-black text-slate-900 mt-1">00</h3>
                    <span class="text-xs font-bold text-green-500">Good Job!</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
            <div class="p-8 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h3 class="text-xl font-black text-slate-900">Aktivitas Real-time</h3>
                <div class="relative w-full sm:w-72">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Cari karyawan..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-slate-400 text-[11px] uppercase tracking-[0.15em] font-bold">
                            <th class="px-8 py-5 text-left">Informasi Karyawan</th>
                            <th class="px-8 py-5 text-left">Jadwal & Waktu</th>
                            <th class="px-8 py-5 text-center">Validasi Lokasi</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name=Ayu+Diana&background=random" class="w-11 h-11 rounded-2xl object-cover">
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800">Ayu Diana Tasya</p>
                                        <p class="text-xs text-slate-500 font-medium tracking-tight">IT Support • ID: EMP001</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">08:00:12 AM</span>
                                    <span class="text-[10px] text-green-500 font-bold uppercase">Masuk Tepat Waktu</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase">
                                    <i class="fas fa-street-view"></i>
                                    Verified In-Radius
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-4 py-1.5 bg-green-500 text-white rounded-full text-[10px] font-black uppercase shadow-lg shadow-green-100">
                                    Present
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="w-9 h-9 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=random" class="w-11 h-11 rounded-2xl object-cover grayscale">
                                    <div>
                                        <p class="text-sm font-black text-slate-800">Budi Santoso</p>
                                        <p class="text-xs text-slate-500 font-medium tracking-tight">Kitchen • ID: EMP004</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-xs text-slate-400 italic">Tidak ada rekaman jam</span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-xs text-slate-300 font-bold">—</span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-4 py-1.5 bg-amber-100 text-amber-600 rounded-full text-[10px] font-black uppercase">
                                    Sakit (Permit)
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="px-4 py-2 text-[10px] font-black text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition uppercase">
                                    Buka Surat
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-slate-50/50 flex justify-between items-center px-8 border-t border-slate-50">
                <p class="text-xs text-slate-500 font-bold">Menampilkan 2 dari 12 karyawan</p>
                <div class="flex gap-2">
                    <button class="p-2 w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition shadow-sm">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </button>
                    <button class="p-2 w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition shadow-sm">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

    </main>

</body>
</html>