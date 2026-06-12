<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - StaffLog</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #1e293b; }

        .back-link {
            position: fixed;
            top: 24px;
            left: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            z-index: 10;
            opacity: 0;
            transform: translateX(-20px);
            animation: slideIn 0.6s ease-out forwards;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: white;
        }
        .back-link i {
            font-size: 20px;
            transition: transform 0.2s;
        }
        .back-link:hover i {
            transform: translateX(-4px);
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative">
    <a href="{{ route('landing') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div class="w-full max-w-md px-4">
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-8 shadow-xl">

            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800">StaffLog</h1>
                <p class="text-slate-500 text-sm">Masuk ke akun Anda</p>
            </div>

            {{-- Pesan error dari server --}}
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Pesan sukses (misal setelah logout) --}}
            @if (session('success'))
                <div class="bg-green-50 text-green-600 p-3 rounded-lg text-sm mb-4 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form login — POST ke server, role dicek otomatis --}}
            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                {{-- Username --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pengguna</label>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        autofocus
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 transition
                               {{ $errors->has('username') ? 'border-red-400 focus:ring-red-200' : 'border-slate-200 focus:ring-slate-200' }}"
                        placeholder="Masukkan username">
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="passwordInput"
                            autocomplete="current-password"
                            class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-200 pr-10"
                            placeholder="Masukkan kata sandi">
                        <button type="button" onclick="togglePass('passwordInput', 'eyeIcon')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full bg-slate-800 text-white py-3 rounded-xl font-semibold hover:bg-slate-700 active:scale-[0.98] transition">
                    <i class="bi bi-box-arrow-in-right mr-2"></i>Masuk
                </button>
            </form>

        </div>
    </div>

    <script src="{{ asset('js/helpers.js') }}"></script>
</body>
</html>
