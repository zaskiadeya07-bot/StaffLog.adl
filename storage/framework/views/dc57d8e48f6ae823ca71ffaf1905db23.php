<?php $__env->startSection('title', 'Rekap Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Karyawan</h1>
            <p class="text-slate-500 text-sm">Kelola data karyawan yang terdaftar</p>
        </div>
        <a href="<?php echo e(route('admin.tambah-karyawan')); ?>" class="btn-primary inline-flex items-center gap-2">
            <i class="bi bi-person-plus"></i> Tambah Karyawan
        </a>
    </div>
    
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="employeeTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="employeeTableBody">
                        <!-- Data will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4">
        <div class="bg-red-600 text-white p-4 rounded-t-2xl">
            <h3 class="font-bold text-lg"><i class="bi bi-exclamation-triangle-fill mr-2"></i> Konfirmasi Hapus</h3>
        </div>
        <div class="p-6">
            <p class="text-slate-700">Apakah Anda yakin ingin menghapus karyawan <strong id="deleteName"></strong>?</p>
            <p class="text-slate-400 text-sm mt-2">Data yang dihapus tidak dapat dikembalikan.</p>
            <input type="hidden" id="deleteId">
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-end gap-3">
            <button class="btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <button class="btn-danger" onclick="confirmDelete()">Hapus</button>
        </div>
    </div>
</div>

<script>
    let employees = JSON.parse(localStorage.getItem('employees')) || [
        { id: 1, name: 'Aulia Pramesti', division: 'Engineering', email: 'aulia@stafflog.com', phone: '08123456789', idKaryawan: 'EMP-001', username: 'aulia' },
        { id: 2, name: 'Bimo Santoso', division: 'Marketing', email: 'bimo@stafflog.com', phone: '08123456788', idKaryawan: 'EMP-002', username: 'bimo' },
        { id: 3, name: 'Citra Nabila', division: 'HR', email: 'citra@stafflog.com', phone: '08123456787', idKaryawan: 'EMP-003', username: 'citra' }
    ];
    
    let deleteId = null;
    
    function showToast(message, type = 'success') {
        const toastHtml = `
            <div class="fixed bottom-5 right-5 z-50 animate-in slide-in-from-right-5">
                <div class="bg-${type === 'success' ? 'emerald-500' : 'red-500'} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    <span>${message}</span>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => {
            const toast = document.querySelector('.animate-in');
            if (toast) toast.remove();
        }, 3000);
    }
    
    function renderTable() {
        const tbody = document.getElementById('employeeTableBody');
        tbody.innerHTML = '';
        
        employees.forEach((emp, index) => {
            const row = `
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 text-sm text-slate-600">${index + 1}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">${emp.name}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">${emp.division}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="<?php echo e(route('admin.detail-rekap-kehadiran')); ?>?id=${emp.id}" class="bg-emerald-50 text-emerald-600 p-2 rounded-lg hover:bg-emerald-100 transition">
                                <i class="bi bi-calendar-check"></i>
                            </a>
                            <a href="<?php echo e(route('admin.edit-karyawan')); ?>?id=${emp.id}" class="bg-amber-50 text-amber-600 p-2 rounded-lg hover:bg-amber-100 transition">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="showDeleteModal(${emp.id}, '${emp.name}')" class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }
    
    function showDeleteModal(id, name) {
        deleteId = id;
        document.getElementById('deleteName').innerText = name;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
        deleteId = null;
    }
    
    function confirmDelete() {
        if (deleteId) {
            employees = employees.filter(emp => emp.id !== deleteId);
            localStorage.setItem('employees', JSON.stringify(employees));
            renderTable();
            showToast('Karyawan berhasil dihapus!', 'success');
            closeDeleteModal();
        }
    }
    
    renderTable();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\rekapkehadiran\resources\views/admin/rekap-karyawan.blade.php ENDPATH**/ ?>