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
        body {
            font-family: 'Inter', sans-serif;
            background: #1e293b;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md px-4">
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-8 shadow-xl">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800">StaffLog</h1>
                <p class="text-slate-500 text-sm">Masuk ke akun Anda</p>
            </div>
            
            <div id="errorMessage" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 hidden"></div>
            
            <form id="loginForm">
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Login Sebagai</label>
                    <select id="role" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <option value="admin">Admin</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
                    <input type="text" id="username" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-200" placeholder="Masukkan username">
                </div>
                
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                    <input type="password" id="password" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-200" placeholder="Masukkan password">
                </div>
                
                <button type="submit" class="w-full bg-slate-800 text-white py-3 rounded-xl font-semibold hover:bg-slate-700 transition">
                    Login
                </button>
            </form>
        </div>
    </div>

    <script>
        const employees = [
            { username: 'budi', password: 'budi123', name: 'Budi Santoso', division: 'IT' },
            { username: 'siti', password: 'siti123', name: 'Siti Aminah', division: 'HRD' }
        ];
        const adminAccount = { username: 'admin', password: 'admin123', name: 'Administrator' };
        
        function showError(msg) {
            const errDiv = document.getElementById('errorMessage');
            errDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill mr-2"></i> ' + msg;
            errDiv.classList.remove('hidden');
            setTimeout(() => errDiv.classList.add('hidden'), 3000);
        }
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;
            
            if (!username || !password) {
                showError('Username dan password harus diisi!');
                return;
            }
            
            if (role === 'admin') {
                if (username === adminAccount.username && password === adminAccount.password) {
                    localStorage.setItem('userRole', 'admin');
                    localStorage.setItem('userName', adminAccount.name);
                    window.location.href = "<?php echo e(route('admin.rekap-karyawan')); ?>";
                } else {
                    showError('Username atau password admin salah!');
                }
            } else {
                const emp = employees.find(e => e.username === username && e.password === password);
                if (emp) {
                    localStorage.setItem('userRole', 'karyawan');
                    localStorage.setItem('userName', emp.name);
                    localStorage.setItem('userDivision', emp.division);
                    window.location.href = "<?php echo e(route('karyawan.dashboard')); ?>";
                } else {
                    showError('Username atau password karyawan salah!');
                }
            }
        });
        
        document.getElementById('role').addEventListener('change', function() {
            if (this.value === 'admin') {
                document.getElementById('username').value = 'admin';
                document.getElementById('password').value = 'admin123';
            } else {
                document.getElementById('username').value = 'budi';
                document.getElementById('password').value = 'budi123';
            }
        });
    </script>
</body>
</html><?php /**PATH C:\laragon\www\rekapkehadiran\resources\views/auth/login.blade.php ENDPATH**/ ?>