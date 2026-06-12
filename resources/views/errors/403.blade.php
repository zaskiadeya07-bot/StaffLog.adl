<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-3xl shadow-xl max-w-lg w-full p-10 text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="bi bi-shield-lock-fill text-4xl text-red-500"></i>
        </div>
        <h1 class="text-6xl font-black text-slate-800 mb-2">403</h1>
        <h2 class="text-xl font-semibold text-slate-700 mb-3">Akses Ditolak</h2>
        <p class="text-slate-500 mb-8">Halaman ini bukan untuk role Anda.</p>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-slate-800 text-white px-6 py-3 rounded-xl font-semibold hover:bg-slate-700 transition">
            <i class="bi bi-box-arrow-in-right"></i> Kembali ke Login
        </a>
    </div>
</body>
</html>