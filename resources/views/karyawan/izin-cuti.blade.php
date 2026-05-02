@extends('layouts.karyawan-layout')

@section('title', 'Izin & Cuti')

@section('content')
<div>
    <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Izin & Cuti</h1>
            <p class="text-slate-500 text-sm">Kelola permohonan izin dan cuti Anda</p>
        </div>
        <button id="openFormBtn" class="btn-primary inline-flex items-center gap-2">
            <i class="bi bi-plus-circle"></i> Buat Permohonan Baru
        </button>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"><i class="bi bi-calendar-check text-blue-600 text-xl"></i></div>
            <div><small class="text-slate-500">Sisa Cuti</small><h3 class="text-2xl font-bold text-blue-600" id="sisaCuti">12</h3><small class="text-slate-400">Hari</small></div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center"><i class="bi bi-clock-history text-amber-600 text-xl"></i></div>
            <div><small class="text-slate-500">Menunggu</small><h3 class="text-2xl font-bold text-amber-600" id="totalPending">0</h3></div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center"><i class="bi bi-check-circle text-emerald-600 text-xl"></i></div>
            <div><small class="text-slate-500">Disetujui</small><h3 class="text-2xl font-bold text-emerald-600" id="totalApproved">0</h3></div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center"><i class="bi bi-x-circle text-red-600 text-xl"></i></div>
            <div><small class="text-slate-500">Ditolak</small><h3 class="text-2xl font-bold text-red-600" id="totalRejected">0</h3></div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="card">
        <div class="p-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
            <div class="relative"><i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i><input type="text" id="searchInput" class="pl-9 input-field w-64" placeholder="Cari permohonan..."></div>
            <div class="flex gap-2"><button class="filter-btn active px-3 py-1.5 rounded-full text-sm" data-status="all">Semua</button><button class="filter-btn px-3 py-1.5 rounded-full text-sm" data-status="pending">Menunggu</button><button class="filter-btn px-3 py-1.5 rounded-full text-sm" data-status="approved">Disetujui</button><button class="filter-btn px-3 py-1.5 rounded-full text-sm" data-status="rejected">Ditolak</button></div>
        </div>
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Durasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="izinTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-slate-800 p-5 rounded-t-3xl sticky top-0"><div class="flex justify-between items-center"><h3 class="text-xl font-bold text-white"><i class="bi bi-file-text mr-2"></i> Form Pengajuan Izin / Cuti</h3><button class="close-modal text-slate-400 hover:text-white text-2xl">&times;</button></div></div>
        <div class="p-6">
            <form id="izinForm" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-semibold mb-1">Jenis Permohonan <span class="text-red-500">*</span></label><select id="jenisIzin" class="input-field" required><option value="">Pilih jenis</option><option value="Cuti Tahunan">Cuti Tahunan</option><option value="Cuti Sakit">Cuti Sakit</option><option value="Izin">Izin</option></select></div>
                    <div><label class="block text-sm font-semibold mb-1">Durasi <span class="text-red-500">*</span></label><div class="flex gap-2"><input type="number" id="durasi" class="input-field" placeholder="Jumlah" required><select id="satuanDurasi" class="input-field w-28"><option value="Hari">Hari</option><option value="Jam">Jam</option></select></div></div>
                    <div><label class="block text-sm font-semibold mb-1">Tanggal Mulai <span class="text-red-500">*</span></label><input type="date" id="tanggalMulai" class="input-field" required></div>
                    <div><label class="block text-sm font-semibold mb-1">Tanggal Selesai <span class="text-red-500">*</span></label><input type="date" id="tanggalSelesai" class="input-field" required></div>
                    
                    <!-- Upload File Section -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-1">Lampiran Dokumen</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 hover:border-blue-400 transition-colors">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="bi bi-cloud-upload text-3xl text-slate-400"></i>
                                <p class="text-sm text-slate-600">Klik atau drag & drop file untuk upload</p>
                                <p class="text-xs text-slate-400">Support: PDF, JPG, PNG, DOC (Max 5MB)</p>
                                <input type="file" id="lampiran" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <button type="button" onclick="document.getElementById('lampiran').click()" class="btn-secondary text-sm px-4 py-2">
                                    <i class="bi bi-folder2-open"></i> Pilih File
                                </button>
                            </div>
                            <div id="fileInfo" class="hidden mt-3 p-2 bg-blue-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-file-earmark-text text-blue-600"></i>
                                        <span id="fileName" class="text-sm text-blue-700"></span>
                                        <span id="fileSize" class="text-xs text-blue-500"></span>
                                    </div>
                                    <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Alasan <span class="text-red-500">*</span></label><textarea id="alasan" rows="3" class="input-field" placeholder="Jelaskan alasan pengajuan..." required></textarea></div>
                </div>
            </form>
        </div>
        <div class="p-5 border-t border-slate-100 flex justify-end gap-3"><button class="close-modal btn-secondary px-5">Batal</button><button onclick="submitIzin()" class="btn-primary px-5">Kirim Permohonan</button></div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-slate-800 p-5 rounded-t-3xl sticky top-0"><div class="flex justify-between items-center"><h3 class="text-xl font-bold text-white">Detail Permohonan</h3><button class="close-modal-detail text-slate-400 hover:text-white text-2xl">&times;</button></div></div>
        <div class="p-6" id="detailContent"></div>
        <div class="p-5 border-t border-slate-100 flex justify-center"><button class="close-modal-detail btn-secondary px-6">Tutup</button></div>
    </div>
</div>

<script>
    let currentUser = { id: localStorage.getItem('userId') || 'KRY-001', name: localStorage.getItem('userName') || 'Budi Santoso', division: localStorage.getItem('userDivision') || 'IT' };
    let izinData = [];
    let currentFilter = 'all';
    let selectedFile = null;
    
    function showToast(message, type = 'success') {
        const toastHtml = `<div class="fixed bottom-5 right-5 z-50 animate-fade-in-up"><div class="bg-${type === 'success' ? 'emerald-500' : 'red-500'} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2"><i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i><span>${message}</span></div></div>`;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => { const toast = document.querySelector('.fixed.bottom-5.right-5'); if(toast) toast.remove(); }, 3000);
    }
    
    function formatTanggal(tgl) { return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }); }
    
    function getStatusBadge(status) {
        const badges = { pending: '<span class="badge-secondary"><i class="bi bi-hourglass-split"></i> Menunggu</span>', approved: '<span class="badge-success"><i class="bi bi-check-circle"></i> Disetujui</span>', rejected: '<span class="badge-danger"><i class="bi bi-x-circle"></i> Ditolak</span>' };
        return badges[status] || badges.pending;
    }
    
    function getFileIcon(fileName) {
        if (!fileName) return 'bi bi-file-earmark';
        const ext = fileName.split('.').pop().toLowerCase();
        if (ext === 'pdf') return 'bi bi-file-earmark-pdf text-red-600';
        if (ext === 'jpg' || ext === 'jpeg' || ext === 'png') return 'bi bi-file-earmark-image text-green-600';
        if (ext === 'doc' || ext === 'docx') return 'bi bi-file-earmark-word text-blue-600';
        return 'bi bi-file-earmark-text';
    }
    
    function loadData() {
        const stored = localStorage.getItem(`izinData_${currentUser.id}`);
        izinData = stored ? JSON.parse(stored) : [{ 
            id: 1, tanggalPengajuan: '2024-04-01', jenis: 'Cuti Tahunan', tanggalMulai: '2024-04-10', 
            tanggalSelesai: '2024-04-12', durasi: 3, satuan: 'Hari', alasan: 'Liburan keluarga', 
            status: 'approved', lampiran: null 
        }, { 
            id: 2, tanggalPengajuan: '2024-04-05', jenis: 'Izin', tanggalMulai: '2024-04-08', 
            tanggalSelesai: '2024-04-08', durasi: 1, satuan: 'Hari', alasan: 'Urusan keluarga', 
            status: 'pending', lampiran: null 
        }];
        saveData();
        updateStats();
        renderTable();
    }
    
    function saveData() { localStorage.setItem(`izinData_${currentUser.id}`, JSON.stringify(izinData)); }
    
    function updateStats() {
        const pending = izinData.filter(i => i.status === 'pending').length;
        const approved = izinData.filter(i => i.status === 'approved').length;
        const rejected = izinData.filter(i => i.status === 'rejected').length;
        document.getElementById('totalPending').innerText = pending;
        document.getElementById('totalApproved').innerText = approved;
        document.getElementById('totalRejected').innerText = rejected;
        const cutiApproved = izinData.filter(i => i.status === 'approved' && i.jenis === 'Cuti Tahunan').reduce((sum, i) => sum + i.durasi, 0);
        document.getElementById('sisaCuti').innerText = Math.max(0, 12 - cutiApproved);
    }
    
    function formatFileSize(bytes) {
        if (!bytes) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    
    // Handle file selection
    document.getElementById('lampiran').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran file maksimal 5MB!', 'danger');
                this.value = '';
                selectedFile = null;
                return;
            }
            
            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Format file tidak didukung! Gunakan PDF, JPG, PNG, atau DOC', 'danger');
                this.value = '';
                selectedFile = null;
                return;
            }
            
            selectedFile = file;
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            
            fileName.innerText = file.name;
            fileSize.innerText = formatFileSize(file.size);
            fileInfo.classList.remove('hidden');
        }
    });
    
    function removeFile() {
        selectedFile = null;
        document.getElementById('lampiran').value = '';
        document.getElementById('fileInfo').classList.add('hidden');
    }
    
    // Convert file to base64 for storage
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }
    
    function renderTable() {
        const tbody = document.getElementById('izinTableBody');
        tbody.innerHTML = '';
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        let filtered = izinData.filter(i => currentFilter === 'all' || i.status === currentFilter);
        if (searchText) filtered = filtered.filter(i => i.jenis.toLowerCase().includes(searchText) || i.alasan.toLowerCase().includes(searchText));
        filtered.sort((a, b) => new Date(b.tanggalPengajuan) - new Date(a.tanggalPengajuan));
        if (filtered.length === 0) { tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-400">Belum ada data permohonan</td></tr>'; return; }
        filtered.forEach((item, index) => { 
            tbody.innerHTML += `<tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-sm">${index+1}</td>
                <td class="px-4 py-3 text-sm">${formatTanggal(item.tanggalPengajuan)}</td>
                <td class="px-4 py-3 font-medium">${item.jenis}</td>
                <td class="px-4 py-3 text-sm">${formatTanggal(item.tanggalMulai)} - ${formatTanggal(item.tanggalSelesai)}</td>
                <td class="px-4 py-3 text-sm">${item.durasi} ${item.satuan}</td>
                <td class="px-4 py-3">${getStatusBadge(item.status)}</td>
                <td class="px-4 py-3">
                    <button onclick="viewDetail(${item.id})" class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition">
                        <i class="bi bi-eye"></i>
                    </button>
                    ${item.status === 'pending' ? `<button onclick="cancelIzin(${item.id})" class="bg-red-50 text-red-600 p-2 rounded-lg ml-1 hover:bg-red-100 transition"><i class="bi bi-x-circle"></i></button>` : ''}
                </td>
            </tr>`; 
        });
    }
    
    function viewDetail(id) {
        const item = izinData.find(i => i.id === id);
        if (!item) return;
        
        let lampiranHtml = '';
        if (item.lampiran) {
            const fileIcon = getFileIcon(item.lampiran.name);
            lampiranHtml = `
                <div class="mb-2">
                    <p class="text-slate-500 text-xs">Lampiran</p>
                    <div class="mt-1">
                        <a href="${item.lampiran.data}" download="${item.lampiran.name}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-2 rounded-lg">
                            <i class="${fileIcon}"></i>
                            <span class="text-sm">${item.lampiran.name}</span>
                            <i class="bi bi-download ml-1"></i>
                        </a>
                    </div>
                </div>
            `;
        }
        
        document.getElementById('detailContent').innerHTML = `
            <div class="mb-2"><p class="text-slate-500 text-xs">Jenis</p><p class="font-semibold">${item.jenis}</p></div>
            <div class="grid grid-cols-2 gap-2 mb-2"><div><p class="text-slate-500 text-xs">Tanggal Pengajuan</p><p>${formatTanggal(item.tanggalPengajuan)}</p></div><div><p class="text-slate-500 text-xs">Status</p><div>${getStatusBadge(item.status)}</div></div></div>
            <div class="mb-2"><p class="text-slate-500 text-xs">Periode</p><p>${formatTanggal(item.tanggalMulai)} - ${formatTanggal(item.tanggalSelesai)}</p><small>Durasi: ${item.durasi} ${item.satuan}</small></div>
            <div><p class="text-slate-500 text-xs">Alasan</p><p>${item.alasan}</p></div>
            ${lampiranHtml}
        `;
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex');
    }
    
    function cancelIzin(id) { if (confirm('Batalkan permohonan ini?')) { izinData = izinData.filter(i => i.id !== id); saveData(); updateStats(); renderTable(); showToast('Permohonan dibatalkan', 'success'); } }
    
    async function submitIzin() {
        const jenis = document.getElementById('jenisIzin').value, 
              durasi = document.getElementById('durasi').value, 
              satuan = document.getElementById('satuanDurasi').value, 
              tanggalMulai = document.getElementById('tanggalMulai').value, 
              tanggalSelesai = document.getElementById('tanggalSelesai').value, 
              alasan = document.getElementById('alasan').value;
        
        if (!jenis || !durasi || !tanggalMulai || !tanggalSelesai || !alasan) { 
            showToast('Isi semua field yang wajib diisi!', 'danger'); 
            return; 
        }
        
        if (new Date(tanggalMulai) > new Date(tanggalSelesai)) { 
            showToast('Tanggal mulai tidak boleh lebih besar dari tanggal selesai', 'danger'); 
            return; 
        }
        
        // Validate remaining leave for Cuti Tahunan
        if (jenis === 'Cuti Tahunan') {
            const cutiApproved = izinData.filter(i => i.status === 'approved' && i.jenis === 'Cuti Tahunan').reduce((sum, i) => sum + i.durasi, 0);
            const sisaCuti = 12 - cutiApproved;
            if (parseInt(durasi) > sisaCuti) {
                showToast(`Sisa cuti Anda hanya ${sisaCuti} hari!`, 'danger');
                return;
            }
        }
        
        const newId = Math.max(...izinData.map(i => i.id), 0) + 1;
        
        let lampiranData = null;
        if (selectedFile) {
            try {
                const base64 = await fileToBase64(selectedFile);
                lampiranData = {
                    name: selectedFile.name,
                    type: selectedFile.type,
                    size: selectedFile.size,
                    data: base64
                };
            } catch (error) {
                showToast('Gagal membaca file!', 'danger');
                return;
            }
        }
        
        izinData.unshift({ 
            id: newId, 
            tanggalPengajuan: new Date().toISOString().split('T')[0], 
            jenis, 
            tanggalMulai, 
            tanggalSelesai, 
            durasi: parseInt(durasi), 
            satuan, 
            alasan, 
            status: 'pending',
            lampiran: lampiranData
        });
        
        saveData(); 
        updateStats(); 
        renderTable();
        
        // Reset form
        document.getElementById('izinForm').reset();
        removeFile();
        document.getElementById('formModal').classList.add('hidden');
        showToast('Permohonan berhasil dikirim!', 'success');
    }
    
    document.getElementById('openFormBtn').onclick = () => { 
        document.getElementById('formModal').classList.remove('hidden'); 
        document.getElementById('formModal').classList.add('flex'); 
    };
    
    document.querySelectorAll('.close-modal, .close-modal-detail').forEach(btn => btn.onclick = () => { 
        document.getElementById('formModal').classList.add('hidden'); 
        document.getElementById('detailModal').classList.add('hidden'); 
    });
    
    document.querySelectorAll('.filter-btn').forEach(btn => btn.onclick = function() { 
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active')); 
        this.classList.add('active'); 
        currentFilter = this.dataset.status; 
        renderTable(); 
    });
    
    document.getElementById('searchInput').onkeyup = () => renderTable();
    
    loadData();
</script>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .badge-secondary, .badge-success, .badge-danger {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-secondary {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .badge-success {
        background-color: #d1fae5;
        color: #059669;
    }
    
    .badge-danger {
        background-color: #fee2e2;
        color: #dc2626;
    }
</style>
@endsection