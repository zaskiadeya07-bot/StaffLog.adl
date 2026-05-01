@extends('layouts.karyawan-layout')

@section('title', 'Profile')

@section('content')
<div>
    <div class="bg-slate-800 rounded-2xl p-6 mb-6 flex flex-col md:flex-row items-center gap-6 text-white">
        <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center text-slate-800 text-4xl font-bold shadow-lg" id="profileAvatar">ZD</div>
        <div class="text-center md:text-left">
            <h2 class="text-2xl font-bold" id="profileName">Zaskia Deya Ramadhani</h2>
            <p><i class="bi bi-building mr-2"></i> <span id="profileDivision">IT Support</span></p>
            <p><i class="bi bi-envelope mr-2"></i> <span id="profileEmail">zaskia@stafflog.com</span></p>
            <p><i class="bi bi-person-check mr-2"></i> Karyawan Aktif</p>
        </div>
    </div>
    
    <div class="card p-6">
        <h3 class="font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100"><i class="bi bi-info-circle mr-2 text-blue-500"></i> Informasi Lengkap</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-slate-600 uppercase">Nama Lengkap</label><input type="text" id="namaLengkap" class="input-field bg-slate-50" readonly value="Zaskia Deya Ramadhani"></div>
            <div><label class="block text-xs font-semibold text-slate-600 uppercase">Nomor ID Karyawan</label><input type="text" id="idKaryawan" class="input-field bg-slate-50" readonly value="EMP-001"></div>
            <div class="md:col-span-2"><label class="block text-xs font-semibold text-slate-600 uppercase">Alamat</label><textarea id="alamat" rows="2" class="input-field bg-slate-50" readonly>Batam, Kepulauan Riau, Indonesia</textarea></div>
            <div><label class="block text-xs font-semibold text-slate-600 uppercase">Nomor HP</label><input type="tel" id="nomorHp" class="input-field bg-slate-50" readonly value="08123456789"></div>
            <div><label class="block text-xs font-semibold text-slate-600 uppercase">Tanggal Mulai Kerja</label><input type="text" id="tanggalMulai" class="input-field bg-slate-50" readonly value="01 Januari 2024"></div>
            <div><label class="block text-xs font-semibold text-slate-600 uppercase">Username</label><input type="text" id="username" class="input-field bg-slate-50" readonly value="zaskia.deya"></div>
            <div><label class="block text-xs font-semibold text-slate-600 uppercase">Divisi</label><input type="text" id="divisi" class="input-field bg-slate-50" readonly value="IT Support"></div>
            <div><label class="block text-xs font-semibold text-slate-600 uppercase">Password</label><input type="password" id="password" class="input-field bg-slate-50" readonly placeholder="********"></div>
        </div>
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
            <a href="{{ route('karyawan.dashboard') }}" class="btn-secondary">Kembali</a>
            <button id="editBtn" class="btn-primary">Edit Profile</button>
        </div>
    </div>
</div>

<script>
    let isEditMode = false;
    const editableFields = ['alamat', 'nomorHp', 'password'];
    let loggedInUser = JSON.parse(sessionStorage.getItem('loggedInUser')) || { id: 1, name: 'Zaskia Deya Ramadhani', idKaryawan: 'EMP-001', division: 'IT Support', email: 'zaskia@stafflog.com', phone: '08123456789', address: 'Batam, Kepulauan Riau, Indonesia', startDate: '01 Januari 2024', username: 'zaskia.deya', password: '123456' };
    
    function loadProfileData() {
        document.getElementById('namaLengkap').value = loggedInUser.name || 'Zaskia Deya Ramadhani';
        document.getElementById('idKaryawan').value = loggedInUser.idKaryawan || 'EMP-001';
        document.getElementById('alamat').value = loggedInUser.address || 'Batam, Kepulauan Riau, Indonesia';
        document.getElementById('nomorHp').value = loggedInUser.phone || '08123456789';
        document.getElementById('tanggalMulai').value = loggedInUser.startDate || '01 Januari 2024';
        document.getElementById('username').value = loggedInUser.username || 'zaskia.deya';
        document.getElementById('divisi').value = loggedInUser.division || 'IT Support';
        document.getElementById('profileName').innerText = loggedInUser.name;
        document.getElementById('profileDivision').innerText = loggedInUser.division;
        document.getElementById('profileEmail').innerText = loggedInUser.email;
        const initials = (loggedInUser.name || 'Zaskia Deya Ramadhani').split(' ').map(n => n[0]).join('').toUpperCase();
        document.getElementById('profileAvatar').innerText = initials;
    }
    
    function toggleEditMode() {
        const editBtn = document.getElementById('editBtn');
        isEditMode = !isEditMode;
        editableFields.forEach(fieldId => { const input = document.getElementById(fieldId); if (input) { if (isEditMode) { input.removeAttribute('readonly'); input.classList.add('bg-white'); } else { input.setAttribute('readonly', true); input.classList.remove('bg-white'); } } });
        editBtn.innerHTML = isEditMode ? '<i class="bi bi-save"></i> Simpan Perubahan' : '<i class="bi bi-pencil"></i> Edit Profile';
        editBtn.classList.toggle('bg-emerald-600');
    }
    
    function saveChanges() {
        loggedInUser.address = document.getElementById('alamat').value;
        loggedInUser.phone = document.getElementById('nomorHp').value;
        const newPassword = document.getElementById('password').value;
        if (newPassword) loggedInUser.password = newPassword;
        sessionStorage.setItem('loggedInUser', JSON.stringify(loggedInUser));
        let employees = JSON.parse(localStorage.getItem('employees')) || [];
        const index = employees.findIndex(emp => emp.id === loggedInUser.id);
        if (index !== -1) { employees[index].phone = loggedInUser.phone; employees[index].alamat = loggedInUser.address; if (newPassword) employees[index].password = newPassword; localStorage.setItem('employees', JSON.stringify(employees)); }
        loadProfileData();
        showToast('Profile berhasil diperbarui!', 'success');
    }
    
    function showToast(message, type = 'success') {
        const toastHtml = `<div class="fixed bottom-5 right-5 z-50"><div class="bg-${type === 'success' ? 'emerald-500' : 'red-500'} text-white px-5 py-3 rounded-xl shadow-lg">${message}</div></div>`;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => { const toast = document.querySelector('.fixed.bottom-5.right-5'); if (toast) toast.remove(); }, 3000);
    }
    
    document.getElementById('editBtn').addEventListener('click', () => { if (isEditMode) saveChanges(); toggleEditMode(); });
    loadProfileData();
</script>
@endsection