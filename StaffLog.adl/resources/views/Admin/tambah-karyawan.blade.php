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
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" class="input-field" placeholder="Masukkan nama lengkap" required>
                    </div>

                    {{-- ID Karyawan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">ID Karyawan <span class="text-red-500">*</span></label>
                        <input type="text" id="idKaryawan" class="input-field" placeholder="EMP-001" required>
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="username" class="input-field" placeholder="Masukkan username untuk login" required>
                    </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Alamat</label>
                        <textarea id="alamat" rows="2" class="input-field" placeholder="Masukkan alamat lengkap"></textarea>
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nomor HP</label>
                        <input type="tel" id="phone" class="input-field" placeholder="08123456789" maxlength="12">
                    </div>

                    {{-- Tanggal Mulai Kerja --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Tanggal Mulai Kerja</label>
                        <input type="date" id="tanggalMulai" class="input-field">
                    </div>

                    {{-- Divisi --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Divisi <span class="text-red-500">*</span></label>
                        <select id="divisi" class="input-field" required>
                            <option value="" disabled selected>Pilih divisi</option>
                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->id_devisi }}">{{ $divisi->nama_devisi }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password" class="input-field" placeholder="Buat password akun" required>
                    </div>

                    {{-- Konfirmasi Password --}}
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
    document.getElementById('tambahKaryawanForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Ambil data form (LENGKAP dengan alamat dan idKaryawan)
        const formData = {
            nama: document.getElementById('nama').value,
            idKaryawan: document.getElementById('idKaryawan').value,
            username: document.getElementById('username').value,
            alamat: document.getElementById('alamat').value,
            phone: document.getElementById('phone').value,
            tanggalMulai: document.getElementById('tanggalMulai').value,
            divisi: document.getElementById('divisi').value,
            password: document.getElementById('password').value,
            konfirmasiPassword: document.getElementById('konfirmasiPassword').value
        };
        
        // Validasi sederhana
        if (!formData.nama || !formData.idKaryawan || !formData.username || !formData.divisi || !formData.password) {
            showToast('Harap isi semua field yang wajib!', 'danger');
            return;
        }
        
        if (formData.password !== formData.konfirmasiPassword) {
            showToast('Password dan Konfirmasi Password tidak cocok!', 'danger');
            return;
        }
        
        // Disable button & show loading
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('{{ route("admin.tambah-karyawan.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => {
                    window.location.href = "{{ route('admin.rekap-karyawan') }}";
                }, 2000);
            } else {
                showToast(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan, silakan coba lagi', 'danger');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
    
    function showToast(message, type = 'success') {
        const bgColor = type === 'success' ? '#10b981' : '#ef4444';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        const toastHtml = `
            <div class="fixed bottom-5 right-5 z-50 animate-in slide-in-from-right-5">
                <div style="background-color: ${bgColor}" class="text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2">
                    <i class="bi bi-${icon}"></i>
                    <span>${message}</span>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => {
            const toast = document.querySelector('.fixed.bottom-5.right-5:last-child');
            if (toast) toast.remove();
        }, 3000);
    }
</script>
@endsection