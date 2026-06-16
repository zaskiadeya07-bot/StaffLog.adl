@extends('layouts.KaryawanLayout')

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

    <!-- Filter -->
    <div class="card mb-6">
        <div class="p-4">
            <form id="filterForm" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Bulan</label>
                    <select name="bulan" id="filterBulan" class="input-field">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Tahun</label>
                    <select name="tahun" id="filterTahun" class="input-field">
                        @for($i = 2022; $i <= 2026; $i++)
                            <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2.5">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="p-0">
            <div class="table-responsive overflow-x-auto">
                <table id="izinCutiTable" class="min-w-full divide-y divide-slate-200">
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
            <form id="izinForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-semibold mb-1">Jenis Permohonan <span class="text-red-500">*</span></label><select id="jenisIzin" name="jenis_izin" class="input-field" required><option value="">Pilih jenis</option><option value="Cuti Tahunan">Cuti Tahunan</option><option value="Cuti Sakit">Cuti Sakit</option><option value="Izin">Izin</option></select></div>
                    <div><label class="block text-sm font-semibold mb-1">Tanggal Mulai <span class="text-red-500">*</span></label><input type="date" id="tanggalMulai" name="tgl_mulai" class="input-field" required></div>
                    <div><label class="block text-sm font-semibold mb-1">Tanggal Selesai <span class="text-red-500">*</span></label><input type="date" id="tanggalSelesai" name="tgl_selesai" class="input-field" required></div>

                    <!-- Upload File Section -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-1">Lampiran Dokumen</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 hover:border-blue-400 transition-colors">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="bi bi-cloud-upload text-3xl text-slate-400"></i>
                                <p class="text-sm text-slate-600">Klik atau drag & drop file untuk upload</p>
                                <p class="text-xs text-slate-400">Support: PDF, JPG, PNG, DOC (Max 5MB)</p>
                                <input type="file" id="lampiran" name="file_surat" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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

                    <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Alasan <span class="text-red-500">*</span></label><textarea id="alasan" name="keterangan" rows="3" class="input-field" placeholder="Jelaskan alasan pengajuan..." required></textarea></div>
                </div>
                <div id="aturanInfo" class="mt-4 p-3 rounded-xl text-sm hidden"></div>
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
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const STORAGE_URL = '{{ asset('storage') }}';
    const JATAH_CUTI = {{ $jatahCuti ?? 12 }};

    let izinData = [];
    let selectedFile = null;

    const JENIS_MAP = {
        cuti_tahunan: 'Cuti Tahunan',
        cuti_sakit: 'Cuti Sakit',
        izin: 'Izin'
    };

    const STATUS_MAP = {
        pending: 'pending',
        disetujui: 'approved',
        ditolak: 'rejected',
        dibatalkan: 'cancelled'
    };

    const STATUS_DISPLAY = {
        pending: 'Menunggu',
        disetujui: 'Disetujui',
        ditolak: 'Ditolak',
        dibatalkan: 'Dibatalkan'
    };

    function updateAturan() {
        const jenis = document.getElementById('jenisIzin').value;
        const info = document.getElementById('aturanInfo');
        if (!jenis) { info.classList.add('hidden'); return; }

        const cutiApproved = allIzinData.filter(i => i.status === 'approved' && i.jenis === 'Cuti Tahunan').reduce((sum, i) => sum + i.durasi, 0);
        const sisaCuti = Math.max(0, JATAH_CUTI - cutiApproved);

        const aturan = {
            'Cuti Tahunan': { icon: 'bi-calendar-check', color: 'bg-blue-50 text-blue-700 border-blue-200' },
            'Cuti Sakit': { icon: 'bi-thermometer-half', color: 'bg-red-50 text-red-700 border-red-200' },
            'Izin': { icon: 'bi-pencil-square', color: 'bg-amber-50 text-amber-700 border-amber-200' }
        };

        const r = aturan[jenis];
        if (r) {
            info.innerHTML = `<div class="flex items-start gap-2 ${r.color} border p-3 rounded-xl"><i class="bi ${r.icon} mt-0.5"></i><div><strong class="text-sm">${jenis}</strong><br><span class="text-xs">Sisa cuti Anda: <strong>${sisaCuti} hari</strong> | Bisa diajukan hari ini</span></div></div>`;
            info.classList.remove('hidden');
        }
    }

    document.getElementById('jenisIzin').addEventListener('change', updateAturan);

    function showToast(message, type = 'success') {
        const toastHtml = `<div class="fixed bottom-5 right-5 z-50 animate-fade-in-up"><div class="bg-${type === 'success' ? 'emerald-500' : 'red-500'} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2"><i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i><span>${message}</span></div></div>`;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => { const toast = document.querySelector('.fixed.bottom-5.right-5'); if(toast) toast.remove(); }, 3000);
    }

    function formatTanggal(tgl) { return new Date(tgl + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }); }

    function hitungDurasi(tglMulai, tglSelesai) {
        const start = new Date(tglMulai + 'T00:00:00');
        const end = new Date(tglSelesai + 'T00:00:00');
        return Math.max(1, Math.round((end - start) / (1000 * 60 * 60 * 24)) + 1);
    }

    function getStatusBadge(status) {
        const badges = {
            pending: '<span class="badge-secondary"><i class="bi bi-hourglass-split"></i> Menunggu</span>',
            approved: '<span class="badge-success"><i class="bi bi-check-circle"></i> Disetujui</span>',
            rejected: '<span class="badge-danger"><i class="bi bi-x-circle"></i> Ditolak</span>',
            cancelled: '<span class="badge-cancelled"><i class="bi bi-x-octagon"></i> Dibatalkan</span>'
        };
        return badges[STATUS_MAP[status]] || badges.pending;
    }

    function getStatusBadgeFromStatus(status) {
        const badges = {
            pending: '<span class="badge-secondary"><i class="bi bi-hourglass-split"></i> Menunggu</span>',
            approved: '<span class="badge-success"><i class="bi bi-check-circle"></i> Disetujui</span>',
            rejected: '<span class="badge-danger"><i class="bi bi-x-circle"></i> Ditolak</span>',
            cancelled: '<span class="badge-cancelled"><i class="bi bi-x-octagon"></i> Dibatalkan</span>'
        };
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

    let dt;
    let allIzinData = [];

    function loadAllData() {
        fetch('{{ route('karyawan.izin-cuti.data') }}')
            .then(response => response.json())
            .then(data => {
                allIzinData = data.map(item => ({
                    id: item.id_izin,
                    tanggalPengajuan: item.tgl_pengajuan,
                    jenis: JENIS_MAP[item.jenis_izin] || item.jenis_izin,
                    tanggalMulai: item.tgl_mulai,
                    tanggalSelesai: item.tgl_selesai,
                    durasi: hitungDurasi(item.tgl_mulai, item.tgl_selesai),
                    satuan: 'Hari',
                    alasan: item.keterangan,
                    status: STATUS_MAP[item.status_approval] || 'pending',
                    status_original: item.status_approval,
                    lampiran: item.file_surat ? {
                        name: item.file_surat.split('/').pop(),
                        url: STORAGE_URL + '/' + item.file_surat
                    } : null
                }));
                updateStats(allIzinData);
                renderTable(allIzinData);
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal memuat data', 'danger');
            });
    }

    function loadFilteredData(bulan, tahun) {
        let url = '{{ route('karyawan.izin-cuti.data') }}?bulan=' + bulan + '&tahun=' + tahun;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                izinData = data.map(item => ({
                    id: item.id_izin,
                    tanggalPengajuan: item.tgl_pengajuan,
                    jenis: JENIS_MAP[item.jenis_izin] || item.jenis_izin,
                    tanggalMulai: item.tgl_mulai,
                    tanggalSelesai: item.tgl_selesai,
                    durasi: hitungDurasi(item.tgl_mulai, item.tgl_selesai),
                    satuan: 'Hari',
                    alasan: item.keterangan,
                    status: STATUS_MAP[item.status_approval] || 'pending',
                    status_original: item.status_approval,
                    lampiran: item.file_surat ? {
                        name: item.file_surat.split('/').pop(),
                        url: STORAGE_URL + '/' + item.file_surat
                    } : null
                }));
                renderTable(izinData);
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal memuat data', 'danger');
            });
    }

    function updateStats(data) {
        const pending = data.filter(i => i.status === 'pending').length;
        const approved = data.filter(i => i.status === 'approved').length;
        const rejected = data.filter(i => i.status === 'rejected').length;
        document.getElementById('totalPending').innerText = pending;
        document.getElementById('totalApproved').innerText = approved;
        document.getElementById('totalRejected').innerText = rejected;
        const cutiApproved = data.filter(i => i.status === 'approved' && i.jenis === 'Cuti Tahunan').reduce((sum, i) => sum + i.durasi, 0);
        document.getElementById('sisaCuti').innerText = Math.max(0, JATAH_CUTI - cutiApproved);
    }

    function formatFileSize(bytes) {
        if (!bytes) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    document.getElementById('lampiran').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran file maksimal 5MB!', 'danger');
                this.value = '';
                selectedFile = null;
                return;
            }
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Format file tidak didukung! Gunakan PDF, JPG, PNG, atau DOC', 'danger');
                this.value = '';
                selectedFile = null;
                return;
            }
            selectedFile = file;
            document.getElementById('fileName').innerText = file.name;
            document.getElementById('fileSize').innerText = formatFileSize(file.size);
            document.getElementById('fileInfo').classList.remove('hidden');
        }
    });

    function removeFile() {
        selectedFile = null;
        document.getElementById('lampiran').value = '';
        document.getElementById('fileInfo').classList.add('hidden');
    }

    function renderTable(data) {
        if (dt) { dt.destroy(); }

        const tbody = document.getElementById('izinTableBody');
        tbody.innerHTML = '';
        if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-400">Belum ada data permohonan</td></tr>'; return; }

        data.forEach((item, index) => {
            tbody.innerHTML += `<tr>
                <td class="px-4 py-3 text-sm">${index+1}</td>
                <td class="px-4 py-3 text-sm">${formatTanggal(item.tanggalPengajuan)}</td>
                <td class="px-4 py-3 font-medium">${item.jenis}</td>
                <td class="px-4 py-3 text-sm">${formatTanggal(item.tanggalMulai)} - ${formatTanggal(item.tanggalSelesai)}</td>
                <td class="px-4 py-3 text-sm">${item.durasi} ${item.satuan}</td>
                <td class="px-4 py-3">${getStatusBadgeFromStatus(item.status)}</td>
                <td class="px-4 py-3">
                    <button onclick="viewDetail(${item.id})" class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" title="Lihat Detail">
                        <i class="bi bi-eye"></i>
                    </button>
                    ${item.status === 'pending' ? `
                    <button onclick="cancelIzin(${item.id})" class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition ml-1" title="Batalkan">
                        <i class="bi bi-x-octagon"></i>
                    </button>` : ''}
                </td>
            </tr>`;
        });

        dt = $('#izinCutiTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[1, 'desc']],
            columnDefs: [{ orderable: false, targets: [6] }]
        });
    }

    function viewDetail(id) {
        let item = izinData.find(i => i.id === id);
        if (!item) item = allIzinData.find(i => i.id === id);
        if (!item) return;

        let lampiranHtml = '';
        if (item.lampiran) {
            lampiranHtml = `
                <div class="mb-2">
                    <p class="text-slate-500 text-xs">Lampiran</p>
                    <div class="mt-1">
                        <a href="${item.lampiran.url}" download="${item.lampiran.name}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-2 rounded-lg">
                            <i class="${getFileIcon(item.lampiran.name)}"></i>
                            <span class="text-sm">${item.lampiran.name}</span>
                            <i class="bi bi-download ml-1"></i>
                        </a>
                    </div>
                </div>
            `;
        }

        document.getElementById('detailContent').innerHTML = `
            <div class="mb-2"><p class="text-slate-500 text-xs">Jenis</p><p class="font-semibold">${item.jenis}</p></div>
            <div class="grid grid-cols-2 gap-2 mb-2"><div><p class="text-slate-500 text-xs">Tanggal Pengajuan</p><p>${formatTanggal(item.tanggalPengajuan)}</p></div><div><p class="text-slate-500 text-xs">Status</p><div>${getStatusBadge(item.status_original)}</div></div></div>
            <div class="mb-2"><p class="text-slate-500 text-xs">Periode</p><p>${formatTanggal(item.tanggalMulai)} - ${formatTanggal(item.tanggalSelesai)}</p><small>Durasi: ${item.durasi} ${item.satuan}</small></div>
            <div><p class="text-slate-500 text-xs">Alasan</p><p>${item.alasan}</p></div>
            ${lampiranHtml}
        `;
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex');
    }

    async function submitIzin() {
        const jenis = document.getElementById('jenisIzin').value;
        const tanggalMulai = document.getElementById('tanggalMulai').value;
        const tanggalSelesai = document.getElementById('tanggalSelesai').value;
        const alasan = document.getElementById('alasan').value;

        if (!jenis || !tanggalMulai || !tanggalSelesai || !alasan) {
            showToast('Isi semua field yang wajib diisi!', 'danger');
            return;
        }

        if (new Date(tanggalMulai + 'T00:00:00') > new Date(tanggalSelesai + 'T00:00:00')) {
            showToast('Tanggal mulai tidak boleh lebih besar dari tanggal selesai', 'danger');
            return;
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const mulaiDate = new Date(tanggalMulai + 'T00:00:00');
        const durasi = hitungDurasi(tanggalMulai, tanggalSelesai);

        // Cek backdate
        if (mulaiDate < today) {
            showToast('Tanggal mulai tidak boleh sebelum hari ini.', 'danger');
            return;
        }

        // Cek sisa cuti
        const cutiApproved = allIzinData.filter(i => i.status === 'approved' && i.jenis === 'Cuti Tahunan').reduce((sum, i) => sum + i.durasi, 0);
        const sisaCuti = Math.max(0, JATAH_CUTI - cutiApproved);
        if (durasi > sisaCuti) {
            showToast('Sisa cuti Anda hanya ' + sisaCuti + ' hari!', 'danger');
            return;
        }

        const formData = new FormData(document.getElementById('izinForm'));

        fetch('{{ route('karyawan.izin-cuti.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('Permohonan berhasil dikirim!');
                document.getElementById('izinForm').reset();
                removeFile();
                document.getElementById('formModal').classList.add('hidden');
                loadAllData();
            } else {
                showToast(result.message || 'Gagal mengirim permohonan', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat mengirim permohonan', 'danger');
        });
    }

    document.getElementById('openFormBtn').onclick = () => {
        document.getElementById('formModal').classList.remove('hidden');
        document.getElementById('formModal').classList.add('flex');
    };

    document.querySelectorAll('.close-modal, .close-modal-detail').forEach(btn => btn.onclick = () => {
        document.getElementById('formModal').classList.add('hidden');
        document.getElementById('detailModal').classList.add('hidden');
    });

    function cancelIzin(id) {
        if (!confirm('Yakin ingin membatalkan permohonan ini?')) return;

        fetch('{{ route('karyawan.izin-cuti.cancel', ':id') }}'.replace(':id', id), {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('Permohonan berhasil dibatalkan.');
                loadAllData();
            } else {
                showToast(result.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Gagal membatalkan permohonan', 'danger');
        });
    }

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const bulan = document.getElementById('filterBulan').value;
        const tahun = document.getElementById('filterTahun').value;
        loadFilteredData(bulan, tahun);
    });

    loadAllData();

</script>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .badge-secondary, .badge-success, .badge-danger, .badge-cancelled {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-secondary { background-color: #fef3c7; color: #d97706; }
    .badge-success { background-color: #d1fae5; color: #059669; }
    .badge-danger { background-color: #fee2e2; color: #dc2626; }
    .badge-cancelled { background-color: #f1f5f9; color: #64748b; }
</style>
@endsection
