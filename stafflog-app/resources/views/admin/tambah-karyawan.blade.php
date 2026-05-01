@extends('layouts.admin-layout')

@section('title', 'Tambah Karyawan')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Tambah Karyawan</h1>
        <p class="text-slate-500 text-sm">Form pendaftaran karyawan baru</p>
    </div>
    
    <div class="card">
        <div class="p-6">
            <form id="tambahKaryawanForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" class="input-field" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nomor ID Karyawan <span class="text-red-500">*</span></label>
                        <input type="text" id="idKaryawan" class="input-field" placeholder="EMP-001" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Alamat</label>
                        <textarea id="alamat" rows="3" class="input-field" placeholder="Masukkan alamat lengkap"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nomor HP</label>
                        <input type="tel" id="phone" class="input-field" placeholder="08123456789">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Tanggal Mulai Kerja</label>
                        <input type="date" id="tanggalMulai" class="input-field">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="username" class="input-field" placeholder="Masukkan username untuk login" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Divisi <span class="text-red-500">*</span></label>
                        <select id="divisi" class="input-field" required>
                            <option value="" disabled selected>Pilih divisi</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Marketing">Marketing</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password" class="input-field" placeholder="Buat password akun" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" id="konfirmasiPassword" class="input-field" placeholder="Konfirmasi password" required>
                    </div>
                </div>
                
                <div class="flex gap-3 mt-8 pt-4 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Tambah Karyawan</button>
                    <button type="reset" class="btn-secondary">Reset Form</button>
                    <a href="{{ route('admin.rekap-karyawan') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let employees = JSON.parse(localStorage.getItem('employees')) || [];
    
    function showToast(message, type = 'success') {
        const toastHtml = `
            <div class="fixed bottom-5 right-5 z-50">
                <div class="bg-${type === 'success' ? 'emerald-500' : 'red-500'} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    <span>${message}</span>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => {
            const toast = document.querySelector('.fixed.bottom-5.right-5');
            if (toast) toast.remove();
        }, 3000);
    }
    
    document.getElementById('tambahKaryawanForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nama = document.getElementById('nama').value;
        const idKaryawan = document.getElementById('idKaryawan').value;
        const alamat = document.getElementById('alamat').value;
        const phone = document.getElementById('phone').value;
        const tanggalMulai = document.getElementById('tanggalMulai').value;
        const username = document.getElementById('username').value;
        const divisi = document.getElementById('divisi').value;
        const password = document.getElementById('password').value;
        const konfirmasi = document.getElementById('konfirmasiPassword').value;
        
        if (!nama || !idKaryawan || !username || !divisi || !password) {
            showToast('Harap isi semua field yang wajib!', 'danger');
            return;
        }
        
        if (password !== konfirmasi) {
            showToast('Password dan Konfirmasi Password tidak cocok!', 'danger');
            return;
        }
        
        const usernameExists = employees.some(emp => emp.username === username);
        if (usernameExists) {
            showToast('Username sudah terdaftar!', 'danger');
            return;
        }
        
        const newId = employees.length > 0 ? Math.max(...employees.map(emp => emp.id)) + 1 : 1;
        
        const newEmployee = {
            id: newId,
            idKaryawan: idKaryawan,
            name: nama,
            division: divisi,
            phone: phone,
            alamat: alamat,
            tanggalMulai: tanggalMulai,
            username: username,
            password: password
        };
        
        employees.push(newEmployee);
        localStorage.setItem('employees', JSON.stringify(employees));
        
        showToast(`Karyawan ${nama} berhasil ditambahkan!`, 'success');
        
        setTimeout(() => {
            window.location.href = "{{ route('admin.rekap-karyawan') }}";
        }, 2000);
    });
</script>
@endsection