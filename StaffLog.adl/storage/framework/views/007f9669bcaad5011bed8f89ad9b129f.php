<?php $__env->startSection('title', 'Edit Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Karyawan</h1>
        <p class="text-slate-500 text-sm">Perbarui data karyawan yang sudah terdaftar</p>
    </div>
    
    <div class="card">
        <div class="p-6">
            <form id="editKaryawanForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" class="input-field" value="<?php echo e($employee->nama_lengkap); ?>" required>
                    </div>
                    
                    <!-- ID Karyawan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nomor ID Karyawan <span class="text-red-500">*</span></label>
                        <input type="text" id="idKaryawan" class="input-field" value="<?php echo e($employee->id_karyawan); ?>" required>
                    </div>
                    
                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="username" class="input-field" value="<?php echo e($employee->username); ?>" required>
                    </div>
                    
                    <!-- Tanggal Mulai Kerja -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Tanggal Mulai Kerja</label>
                        <input type="date" id="tanggalMulai" class="input-field" value="<?php echo e($employee->tgl_mulai_kerja ? $employee->tgl_mulai_kerja->format('Y-m-d') : ''); ?>">
                    </div>
                    
                    <!-- Divisi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Divisi <span class="text-red-500">*</span></label>
                        <select id="divisi" class="input-field" required>
                            <option value="">Pilih Divisi</option>
                            <?php $__currentLoopData = $divisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $divisi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($divisi->id_devisi); ?>" <?php echo e($employee->divisi == $divisi->id_devisi ? 'selected' : ''); ?>>
                                    <?php echo e($divisi->nama_devisi); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Nomor HP -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Nomor HP</label>
                        <input type="tel" id="phone" class="input-field" value="<?php echo e($employee->nomor_hp); ?>">
                    </div>
                    
                    <!-- Alamat (Full Width) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Alamat</label>
                        <textarea id="alamat" rows="3" class="input-field"><?php echo e($employee->alamat); ?></textarea>
                    </div>
                    
                    <!-- Password Baru -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Password Baru</label>
                        <input type="password" id="password" class="input-field" placeholder="Kosongkan jika tidak ingin mengubah">
                        <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                    </div>
                    
                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Konfirmasi Password Baru</label>
                        <input type="password" id="konfirmasiPassword" class="input-field" placeholder="Konfirmasi password baru">
                    </div>
                </div>
                
                <div class="flex gap-3 mt-8 pt-4 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    <a href="<?php echo e(route('admin.rekap-karyawan')); ?>" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const employeeId = <?php echo e($employee->id_pengguna); ?>;
    
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
    
    document.getElementById('editKaryawanForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Ambil data dari form
        const formData = {
            nama: document.getElementById('nama').value,
            idKaryawan: document.getElementById('idKaryawan').value,
            username: document.getElementById('username').value,
            tanggalMulai: document.getElementById('tanggalMulai').value,
            divisi: document.getElementById('divisi').value,
            phone: document.getElementById('phone').value,
            alamat: document.getElementById('alamat').value,
            password: document.getElementById('password').value,
            konfirmasiPassword: document.getElementById('konfirmasiPassword').value
        };
        
        // Validasi
        if (!formData.nama || !formData.idKaryawan || !formData.username || !formData.divisi) {
            showToast('Harap isi semua field yang wajib!', 'danger');
            return;
        }
        
        if (formData.password && formData.password !== formData.konfirmasiPassword) {
            showToast('Password baru dan konfirmasi tidak cocok!', 'danger');
            return;
        }
        
        // Disable button
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch(`/admin/edit-karyawan/${employeeId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => {
                    window.location.href = "<?php echo e(route('admin.rekap-karyawan')); ?>";
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
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\StaffLog.adl\StaffLog.adl\resources\views/admin/edit-karyawan.blade.php ENDPATH**/ ?>