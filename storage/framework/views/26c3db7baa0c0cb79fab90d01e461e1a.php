<div id="attendanceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl overflow-hidden">
        
        <div class="p-5 border-b text-center relative">
            <h3 class="text-2xl font-serif text-gray-800">Detail Absensi</h3>
            <button onclick="tutupModal()" class="absolute right-5 top-5 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        </div>

        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600">Nama Karyawan</label>
                    <p id="modalNama" class="text-lg font-bold text-gray-800">-</p>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600">Divisi</label>
                    <p id="modalDivisi" class="text-lg text-gray-700">-</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600">Jam Masuk</label>
                    <p id="timeMasuk" class="text-gray-700">--:--</p>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600">Jam Pulang</label>
                    <p id="timePulang" class="text-gray-700">--:--</p>
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-sm font-semibold text-gray-600 block border-b pb-1">Keterangan</label>
                <div>
                    <p id="modalDesc" class="bg-gray-100 p-3 rounded-lg text-sm text-gray-700 min-h-[40px] border border-gray-200">-</p>
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-sm font-semibold text-gray-600 block border-b pb-1 mb-3">Status Kehadiran</label>
                <select id="adminStatus" class="w-full p-2.5 bg-gray-200 border border-gray-300 rounded-full text-sm outline-none">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alfa">Alfa</option>
                </select>
            </div>
        </div>

        <div class="p-6 flex justify-center space-x-4 border-t">
            <button onclick="tutupModal()" class="px-8 py-2 bg-gray-200 text-gray-700 font-bold rounded-full text-sm">Batal</button>
            <button onclick="simpanPerubahan()" class="px-8 py-2 bg-blue-600 text-white font-bold rounded-full text-sm shadow-sm">Simpan</button>
        </div>
    </div>
</div>

<script>
    let currentEmployeeName = '';
    let currentEmployeeDivision = '';

    function bukaModal(nama, deskripsi, status, jamMasuk, jamPulang, tanggal, divisi = '') {
        currentEmployeeName = nama;
        currentEmployeeDivision = divisi;
        
        document.getElementById('modalNama').innerText = nama || '-';
        document.getElementById('modalDivisi').innerText = divisi || '-';
        document.getElementById('modalDesc').innerText = deskripsi || 'Tidak ada keterangan';
        document.getElementById('adminStatus').value = status || 'hadir';
        document.getElementById('timeMasuk').innerText = jamMasuk || '--:--';
        document.getElementById('timePulang').innerText = jamPulang || '--:--';
        
        const modal = document.getElementById('attendanceModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function tutupModal() {
        const modal = document.getElementById('attendanceModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function simpanPerubahan() {
        const status = document.getElementById('adminStatus').value;
        const keterangan = document.getElementById('modalDesc').innerText;
        
        // Simpan ke session storage (simulasi tanpa database)
        const absensiData = JSON.parse(sessionStorage.getItem('absensiData') || '{}');
        absensiData[currentEmployeeName] = {
            name: currentEmployeeName,
            division: currentEmployeeDivision,
            status: status,
            keterangan: keterangan,
            date: new Date().toLocaleString()
        };
        sessionStorage.setItem('absensiData', JSON.stringify(absensiData));
        
        alert(`Data absensi untuk ${currentEmployeeName} berhasil diperbarui!\nStatus: ${status}`);
        tutupModal();
    }

    window.onclick = function(event) {
        const modal = document.getElementById('attendanceModal');
        if (event.target == modal) tutupModal();
    }
</script><?php /**PATH C:\laragon\www\rekapkehadiran\resources\views/Admin/ModalDetailAbsensi.blade.php ENDPATH**/ ?>