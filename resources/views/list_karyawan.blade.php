<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRPresence - Kelola Karyawan</title>
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
                        <a href="#" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">Dashboard</a>
                        <a href="#" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">Divisi</a>
                        <a href="#" class="px-4 py-2 text-sm font-bold text-indigo-600 bg-indigo-50 rounded-full">Karyawan</a>
                        <a href="#" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">Laporan</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
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
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Manajemen Karyawan</h1>
                <p class="text-slate-500 mt-1">Daftarkan karyawan baru dan kelola penugasan divisi mereka.</p>
            </div>
            <div class="flex gap-3">
                <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-xl shadow-slate-200 hover:bg-slate-800 transition">
                    <i class="fas fa-user-plus"></i>
                    Tambah Karyawan Baru
                </button>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
            <div class="p-8 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h3 class="text-xl font-black text-slate-900">Daftar Karyawan Terdaftar</h3>
                <div class="relative w-full sm:w-72">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Cari nama atau ID..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-slate-400 text-[11px] uppercase tracking-[0.15em] font-bold">
                            <th class="px-8 py-5 text-left">Profil Karyawan</th>
                            <th class="px-8 py-5 text-left">Divisi Terkait</th>
                            <th class="px-8 py-5 text-center">Username Sistem</th>
                            <th class="px-8 py-5 text-right">Manajemen Akun</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <img src="https://ui-avatars.com/api/?name=Ayu+Diana&background=random" class="w-11 h-11 rounded-2xl object-cover">
                                    <div>
                                        <p class="text-sm font-black text-slate-800">Ayu Diana Tasya</p>
                                        <p class="text-xs text-slate-500 font-medium tracking-tight">ID: 3312501071</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold uppercase">
                                    IT Support
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-sm text-slate-600 font-medium">ayu.diana</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <button class="p-2 text-blue-500 hover:bg-blue-50 rounded-xl transition" title="Edit Profil">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition" title="Hapus Akun">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <img src="https://ui-avatars.com/api/?name=Lily+Luthfiah&background=random" class="w-11 h-11 rounded-2xl object-cover">
                                    <div>
                                        <p class="text-sm font-black text-slate-800">Lily Luthfiah</p>
                                        <p class="text-xs text-slate-500 font-medium tracking-tight">ID: 3312501087</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold uppercase">
                                    Kitchen
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-sm text-slate-600 font-medium">lily.luthfiah</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <button class="p-2 text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-slate-50/50 flex justify-between items-center px-8 border-t border-slate-50">
                <p class="text-xs text-slate-500 font-bold">Total Karyawan: 12 Personil</p>
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