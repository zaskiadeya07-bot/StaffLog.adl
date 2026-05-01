@extends('layouts.admin-layout')

@section('title', 'Edit Karyawan')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Karyawan</h1>
        <p class="text-slate-500 text-sm">Perbaharui data karyawan yang sudah terdaftar</p>
    </div>
    
    <div class="card">
        <div class="p-6">
            <form id="editKaryawanForm">
                <input type="hidden" id="employeeId">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" class="input-field" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nomor ID Karyawan <span class="text-red-500">*</span></label>
                        <input type="text" id="idKaryawan" class="input-field" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Alamat</label>
                        <textarea id="alamat" rows="3" class="input-field"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nomor HP</label>
                        <input type="tel" id="phone" class="input-field">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Tanggal Mulai Kerja</label>
                        <input type="date" id="tanggalMulai" class="input-field">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="username" class="input-field" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Divisi <span class="text-red-500">*</span></label>
                        <select id="divisi" class="input-field" required>
                            <option value="Engineering">Engineering</option>
                            <option value="Marketing">Marketing</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Password Baru</label>
                        <input type="password" id="password" class="input-field" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Konfirmasi Password Baru</label>
                        <input type="password" id="konfirmasiPassword" class="input-field" placeholder="Konfirmasi password baru">
                    </div>
                </div>
                
                <div class="flex gap-3 mt-8 pt-4 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.rekap-karyawan') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let employees = JSON.parse(localStorage.getItem('employees')) || [];
    const urlParams = new URLSearchParams(window.location.search);
    const employeeId = parseInt(urlParams.get('id'));
    let currentEmployee = employees.find(emp => emp.id === employeeId);
    
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
    
    if (currentEmployee) {
        document.getElementById('employeeId').value = currentEmployee.id;
        document.getElementById('nama').value = currentEmployee.name || '';
        document.getElementById('idKaryawan').value = currentEmployee.idKaryawan || '';
        document.getElementById('alamat').value = currentEmployee.alamat || '';
        document.getElementById('phone').value = currentEmployee.phone || '';
        document.getElementById('tanggalMulai').value = currentEmployee.tanggalMulai || '';
        document.getElementById('username').value = currentEmployee.username || '';
        document.getElementById('divisi').value = currentEmployee.division || '';
    } else {
        showToast('Data karyawan tidak ditemukan!', 'danger');
        setTimeout(() => {
            window.location.href = "{{ route('admin.rekap-karyawan') }}";
        }, 1500);
    }
    
    document.getElementById('editKaryawanForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = parseInt(document.getElementById('employeeId').value);
        const nama = document.getElementById('nama').value;
        const idKaryawan = document.getElementById('idKaryawan').value;
        const alamat = document.getElementById('alamat').value;
        const phone = document.getElementById('phone').value;
        const tanggalMulai = document.getElementById('tanggalMulai').value;
        const username = document.getElementById('username').value;
        const divisi = document.getElementById('divisi').value;
        const passwordBaru = document.getElementById('password').value;
        const konfirmasi = document.getElementById('konfirmasiPassword').value;
        
        if (!nama || !idKaryawan || !username || !divisi) {
            showToast('Harap isi semua field yang wajib!', 'danger');
            return;
        }
        
        if (passwordBaru && passwordBaru !== konfirmasi) {
            showToast('Password baru dan konfirmasi tidak cocok!', 'danger');
            return;
        }
        
        const index = employees.findIndex(emp => emp.id === id);
        if (index !== -1) {
            employees[index] = {
                ...employees[index],
                name: nama,
                idKaryawan: idKaryawan,
                alamat: alamat,
                phone: phone,
                tanggalMulai: tanggalMulai,
                username: username,
                division: divisi
            };
            
            if (passwordBaru) {
                employees[index].password = passwordBaru;
            }
            
            localStorage.setItem('employees', JSON.stringify(employees));
            showToast('Data karyawan berhasil diperbarui!', 'success');
            
            setTimeout(() => {
                window.location.href = "{{ route('admin.rekap-karyawan') }}";
            }, 1500);
        }
    });
</script>
@endsection