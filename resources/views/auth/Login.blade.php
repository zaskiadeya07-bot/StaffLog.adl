<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StaffLog</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #1e293b; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="passwordInput"
                            autocomplete="current-password"
                            class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-200 pr-10"
                            placeholder="Masukkan password">
                        <button type="button" onclick="togglePassword()"
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

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
