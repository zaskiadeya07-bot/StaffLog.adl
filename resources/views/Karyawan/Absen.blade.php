@extends('layouts.KaryawanLayout')

@section('title', $mode === 'masuk' ? 'Absen Masuk' : 'Absen Pulang')

@section('content')
<div>
    <x-page-header
        title="Absen {{ ucfirst($mode) }}"
        description="Validasi lokasi aktif, radius kantor {{ $setting->radius ?? 100 }} meter."
    >
        <x-slot:actionSlot>
            <div class="clock-card">
                <small class="text-slate-400 text-xs uppercase">Jam Saat Ini</small>
                <h3 class="text-2xl font-bold text-white" id="clock">--:--:--</h3>
                <small class="text-slate-400" id="date">--</small>
            </div>
        </x-slot:actionSlot>
    </x-page-header>

    <div class="card mb-3 p-3">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Cari lokasi..."
                    class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>
            <button id="searchBtn" class="btn-primary px-4 py-2 text-sm rounded-xl">Cari</button>
            <button id="myLocationBtn" title="Lokasi Saya"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 transition">
                <i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>
            </button>
        </div>
        <div id="searchResults" class="mt-2 hidden border border-slate-200 rounded-xl overflow-hidden bg-white shadow-md max-h-48 overflow-y-auto z-50 relative"></div>
    </div>

    <div class="card mb-5 overflow-hidden">
        <div id="map" class="h-96 w-full"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
        <div class="card p-5">
            <label class="font-semibold text-slate-700 mb-2 block">
                <i class="bi bi-geo-alt-fill text-blue-500 mr-1"></i> Status Lokasi
            </label>
            <div id="locationStatusContainer">
                <p class="text-slate-500">Mendeteksi lokasi...</p>
            </div>
        </div>
        <div class="card p-5">
            <label class="font-semibold text-slate-700 mb-2 block">
                <i class="bi bi-pin-map-fill text-blue-500 mr-1"></i> Koordinat Karyawan
            </label>
            <p id="coordinates" class="text-slate-500 text-sm">Belum tersedia</p>
        </div>
    </div>

    <div class="text-center">
        <button id="actionBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="bi {{ $mode === 'masuk' ? 'bi-check-circle' : 'bi-box-arrow-right' }} mr-2"></i>
            Absen {{ ucfirst($mode) }} Sekarang
        </button>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
.clock-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var MODE = '{{ $mode }}';
var OFFICE_LOCATION = {
    lat: {{ $setting->lat_kantor ?? -6.208765 }},
    lng: {{ $setting->long_kantor ?? 106.845593 }},
    radius: {{ $setting->radius ?? 100 }}
};
var JAM_MASUK_STD = '{{ $setting->jam_masuk_std ?? null }}';
var JAM_PULANG_STD = '{{ $setting->jam_pulang_std ?? null }}';
function getJamBukaAbsen() {
    if (!JAM_MASUK_STD) return '';
    var parts = JAM_MASUK_STD.split(':');
    var d = new Date();
    d.setHours(parseInt(parts[0]), parseInt(parts[1] || 0), 0);
    d.setMinutes(d.getMinutes() - 30);
    return ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
}
var STATUS_ROUTE = '{{ $mode === "masuk" ? route("karyawan.checkin.status") : route("karyawan.checkout.status") }}';
var STORE_ROUTE = '{{ $mode === "masuk" ? route("karyawan.checkin.store") : route("karyawan.checkout.store") }}';

var hasDoneToday = false;
var map = null;
var userMarker = null;
var officeMarker = null;
var officeCircle = null;
var currentPosition = null;
var isWithinRadius = false;
var watchId = null;
var gpsFirstFix = false;
var gpsAccuracyWarned = false;

function showNotification(icon, message) {
    Swal.fire({ icon: icon, title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: icon === 'success' ? 3000 : 4000, timerProgressBar: true });
}

function updateClock() {
    var now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('date').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    var container = document.getElementById('locationStatusContainer');
    if (MODE === 'masuk' && isBeforeJamMasuk()) {
        enableButton(false);
        if (container && !container.querySelector('.bg-amber-50')) {
            container.innerHTML = '<div class="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-200"><i class="bi bi-clock-history mr-2"></i><strong>Belum Waktunya Absen Masuk</strong><br><small>Absen dibuka mulai pukul ' + getJamBukaAbsen() + ' (30 menit sebelum jam masuk ' + JAM_MASUK_STD + '). Silakan tunggu.</small></div>';
        }
    } else if (MODE === 'pulang' && isBeforeJamPulang()) {
        enableButton(false);
        if (container && !container.querySelector('.bg-amber-50')) {
            container.innerHTML = '<div class="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-200"><i class="bi bi-clock-history mr-2"></i><strong>Belum Waktunya Pulang</strong><br><small>Jam pulang kantor pukul ' + JAM_PULANG_STD + '. Silakan tunggu hingga jam pulang tiba.</small></div>';
        }
    } else if (container && container.querySelector('.bg-amber-50')) {
        if (currentPosition) {
            var distance = calculateDistance(currentPosition.lat, currentPosition.lng, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng);
            isWithinRadius = distance <= OFFICE_LOCATION.radius;
            if (isWithinRadius) {
                container.innerHTML = '<div class="bg-emerald-100 text-emerald-700 p-3 rounded-xl"><i class="bi bi-check-circle-fill mr-2"></i><strong>\u2705 Dalam Radius Kantor</strong><br><small>Jarak: ' + distance.toFixed(2) + ' meter (Maks: ' + OFFICE_LOCATION.radius + ' meter)</small></div>';
                enableButton(true);
            } else {
                container.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl"><i class="bi bi-x-circle-fill mr-2"></i><strong>\u274c Di Luar Radius Kantor</strong><br><small>Jarak: ' + distance.toFixed(2) + ' meter (Maks: ' + OFFICE_LOCATION.radius + ' meter)</small></div>';
                enableButton(false);
            }
        }
    }
}
setInterval(updateClock, 1000);
updateClock();

function checkTodayStatus() {
    fetch(STATUS_ROUTE, { method: 'GET', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' } })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        var sudah = MODE === 'masuk' ? (result.sudah_check_in || result.hasCheckedIn) : result.hasCheckedOut;
        if (sudah) {
            hasDoneToday = true;
            enableButton(false);
            showNotification('error', 'Anda sudah absen ' + MODE + ' hari ini');
        }
        else if (MODE === 'pulang' && !result.sudah_check_in && !result.data) {
            enableButton(false);
            showNotification('error', 'Anda belum absen masuk hari ini.');
        }
        else if (MODE === 'pulang' && !hasDoneToday) {
            updateCheckoutBlock();
        }
    })
    .catch(function(error) { console.error('Error:', error); });
}

function initMap() {
    if (typeof L === 'undefined') return;
    map = L.map('map').setView([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], 17);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>' }).addTo(map);
    officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng]).addTo(map).bindPopup('\ud83c\udfe2 Lokasi Kantor');
    officeCircle = L.circle([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], { color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 0.1, radius: OFFICE_LOCATION.radius }).addTo(map);
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000, dLat = (lat2 - lat1) * Math.PI / 180, dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function updateLocationStatus(position) {
    var userLat = position.coords.latitude, userLng = position.coords.longitude;
    var accuracy = position.coords.accuracy;
    currentPosition = { lat: userLat, lng: userLng };
    var distance = calculateDistance(userLat, userLng, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng);
    isWithinRadius = distance <= OFFICE_LOCATION.radius;

    document.getElementById('coordinates').innerHTML = '<strong>Lintang:</strong> ' + userLat.toFixed(6) +
        '<br><strong>Bujur:</strong> ' + userLng.toFixed(6) +
        '<br><strong>Akurasi:</strong> \u00b1' + accuracy.toFixed(1) + ' meter' +
        '<br><strong>Jarak ke Kantor:</strong> ' + distance.toFixed(2) + ' meter';

    if (!gpsFirstFix) {
        gpsFirstFix = true;
        Swal.fire({ icon: 'success', title: 'Lokasi terdeteksi!', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
    }
    if (accuracy > 100 && !gpsAccuracyWarned) {
        gpsAccuracyWarned = true;
        Swal.fire({ icon: 'warning', title: 'Akurasi GPS rendah (' + accuracy.toFixed(0) + 'm)', text: 'Koordinat tetap tersimpan, namun akurasi bisa memengaruhi presisi lokasi.', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
    }

    var statusContainer = document.getElementById('locationStatusContainer');
    if (MODE === 'masuk' && isBeforeJamMasuk()) {
        enableButton(false);
        if (statusContainer && !statusContainer.querySelector('.bg-amber-50')) {
            statusContainer.innerHTML = '<div class="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-200"><i class="bi bi-clock-history mr-2"></i><strong>Belum Waktunya Absen Masuk</strong><br><small>Absen dibuka mulai pukul ' + getJamBukaAbsen() + ' (30 menit sebelum jam masuk ' + JAM_MASUK_STD + '). Silakan tunggu.</small></div>';
        }
    } else if (MODE === 'pulang' && isBeforeJamPulang()) {
        enableButton(false);
        if (statusContainer && !statusContainer.querySelector('.bg-amber-50')) {
            statusContainer.innerHTML = '<div class="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-200"><i class="bi bi-clock-history mr-2"></i><strong>Belum Waktunya Pulang</strong><br><small>Jam pulang kantor pukul ' + JAM_PULANG_STD + '. Silakan tunggu hingga jam pulang tiba.</small></div>';
        }
    } else if (isWithinRadius) {
        statusContainer.innerHTML = '<div class="bg-emerald-100 text-emerald-700 p-3 rounded-xl"><i class="bi bi-check-circle-fill mr-2"></i><strong>\u2705 Dalam Radius Kantor</strong><br><small>Jarak: ' + distance.toFixed(2) + ' meter (Maks: ' + OFFICE_LOCATION.radius + ' meter)</small></div>';
        enableButton(true);
    } else {
        statusContainer.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl"><i class="bi bi-x-circle-fill mr-2"></i><strong>\u274c Di Luar Radius Kantor</strong><br><small>Jarak: ' + distance.toFixed(2) + ' meter (Maks: ' + OFFICE_LOCATION.radius + ' meter)</small></div>';
        enableButton(false);
    }

    if (map) {
        if (userMarker) map.removeLayer(userMarker);
        var userIcon = L.divIcon({
            html: '<div style="background-color: ' + (isWithinRadius ? '#10b981' : '#ef4444') + '; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>',
            iconSize: [20, 20], className: 'user-marker'
        });
        userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map).bindPopup('\ud83d\udccd Posisi Anda<br>Jarak: ' + distance.toFixed(2) + ' meter');
        map.setView([userLat, userLng], 17);
    }
}

function handleLocationError(error) {
    var msg = '';
    switch (error.code) {
        case error.PERMISSION_DENIED: msg = 'Izin lokasi ditolak.'; break;
        case error.POSITION_UNAVAILABLE: msg = 'Informasi lokasi tidak tersedia.'; break;
        case error.TIMEOUT: msg = 'Waktu permintaan lokasi habis.'; break;
        default: msg = 'Kesalahan mengakses lokasi.';
    }
    document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl"><i class="bi bi-exclamation-triangle-fill mr-2"></i><strong>Kesalahan</strong><br><small>' + msg + '</small></div>';
    enableButton(false);
}

function enableButton(enabled) {
    var btn = document.getElementById('actionBtn');
    if (enabled && !hasDoneToday) { btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; }
    else { btn.disabled = true; btn.style.opacity = '0.5'; btn.style.cursor = 'not-allowed'; }
}

function isBeforeJamMasuk() {
    if (MODE !== 'masuk' || !JAM_MASUK_STD) return false;
    var now = new Date();
    var parts = JAM_MASUK_STD.split(':');
    var jamMasuk = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(parts[0]), parseInt(parts[1] || 0), 0);
    var bolehAbsen = new Date(jamMasuk.getTime() - 30 * 60 * 1000);
    return now < bolehAbsen;
}

function isBeforeJamPulang() {
    if (MODE !== 'pulang' || !JAM_PULANG_STD) return false;
    var now = new Date();
    var parts = JAM_PULANG_STD.split(':');
    var jamPulang = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(parts[0]), parseInt(parts[1] || 0), 0);
    return now < jamPulang;
}

function updateCheckinBlock() {
    if (MODE !== 'masuk' || !JAM_MASUK_STD || hasDoneToday) return;
    if (isBeforeJamMasuk()) {
        enableButton(false);
        document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-200"><i class="bi bi-clock-history mr-2"></i><strong>Belum Waktunya Absen Masuk</strong><br><small>Absen dibuka mulai pukul ' + getJamBukaAbsen() + ' (30 menit sebelum jam masuk ' + JAM_MASUK_STD + '). Silakan tunggu.</small></div>';
    }
}

function updateCheckoutBlock() {
    if (MODE !== 'pulang' || !JAM_PULANG_STD || hasDoneToday) return;
    if (isBeforeJamPulang()) {
        enableButton(false);
        document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-200"><i class="bi bi-clock-history mr-2"></i><strong>Belum Waktunya Pulang</strong><br><small>Jam pulang kantor pukul ' + JAM_PULANG_STD + '. Silakan tunggu hingga jam pulang tiba.</small></div>';
    }
}

function performAction() {
    if (!isWithinRadius) { showNotification('error', 'Anda berada di luar radius kantor'); return; }
    if (hasDoneToday) { showNotification('error', 'Anda sudah absen ' + MODE + ' hari ini'); return; }
    if (!currentPosition) { showNotification('error', 'Lokasi tidak terdeteksi'); return; }

    if (MODE === 'masuk') {
        if (isBeforeJamMasuk()) {
            showNotification('error', 'Belum bisa absen masuk. Absen dibuka mulai pukul ' + getJamBukaAbsen() + ' (30 menit sebelum jam masuk ' + JAM_MASUK_STD + ').');
            return;
        }
    }

    if (MODE === 'pulang') {
        if (isBeforeJamPulang()) {
            showNotification('error', 'Maaf, Anda belum bisa absen pulang. Jam pulang kantor pukul ' + JAM_PULANG_STD + '.');
            return;
        }
        var jamSekarang = new Date();
        if (jamSekarang.getHours() >= 0 && jamSekarang.getHours() < 6) {
            showNotification('error', 'Batas absen pulang kemarin sudah lewat (23:59).');
            return;
        }
    }

    var jarak = calculateDistance(currentPosition.lat, currentPosition.lng, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng).toFixed(0);
    var jam = document.getElementById('clock').textContent;

    Swal.fire({
        icon: 'question',
        title: 'Absen ' + (MODE === 'masuk' ? 'Masuk' : 'Pulang') + ' Sekarang?',
        html: '<div style="text-align:left"><div style="padding:6px 0"><strong>Jam:</strong> ' + jam + '</div><div style="padding:6px 0"><strong>Jarak ke Kantor:</strong> ' + jarak + ' meter</div><div style="padding:6px 0"><strong>Status:</strong> ' + (isWithinRadius ? 'Dalam Radius' : 'Luar Radius') + '</div></div>',
        showCancelButton: true,
        confirmButtonText: 'Ya, Absen ' + (MODE === 'masuk' ? 'Masuk' : 'Pulang') + '!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb'
    }).then(function(result) {
        if (result.isConfirmed) submit();
    });
}

function submit() {
    var btn = document.getElementById('actionBtn');
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split spin"></i> Memproses...';
    btn.disabled = true;

    Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

    var body = { latitude: currentPosition.lat, longitude: currentPosition.lng };
    if (MODE === 'masuk') {
        var now = new Date();
        body.jam_masuk = now.toLocaleTimeString('id-ID', { hour12: false }).replace(/\./g, ':');
    }

    fetch(STORE_ROUTE, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        if (result.success) {
            Swal.close();
            var pesan = MODE === 'masuk' ? (result.data && result.data.menit_terlambat > 0 ? 'Anda terlambat ' + result.data.menit_terlambat + ' menit!' : 'Selamat Bekerja!') : 'Selamat Beristirahat!';
            showNotification(result.data && result.data.menit_terlambat > 0 ? 'error' : 'success', pesan);
            hasDoneToday = true;
            setTimeout(function() { window.location.href = "{{ route('karyawan.dashboard') }}"; }, 3000);
        } else {
            Swal.close();
            showNotification('error', result.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        Swal.close();
        showNotification('error', 'Terjadi kesalahan, silakan coba lagi');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

var actionBtn = document.getElementById('actionBtn');
if (actionBtn) actionBtn.addEventListener('click', performAction);

initMap();
checkTodayStatus();
updateCheckinBlock();
updateCheckoutBlock();

if (navigator.geolocation) {
    watchId = navigator.geolocation.watchPosition(updateLocationStatus, handleLocationError, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
} else {
    document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">Browser tidak mendukung geolocation</div>';
}

var searchTimeout = null;
function searchLocation(query) {
    if (!query.trim()) return;
    var resultsEl = document.getElementById('searchResults');
    if (!resultsEl) return;
    resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400"><i class="bi bi-hourglass-split mr-2"></i>Mencari...</div>';
    resultsEl.classList.remove('hidden');
    fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(query) + '&format=json&limit=5&countrycodes=id', { headers: { 'Accept-Language': 'id' } })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.length) { resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">Lokasi tidak ditemukan.</div>'; return; }
        var html = '';
        for (var i = 0; i < data.length; i++) {
            html += '<div class="search-result-item px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 border-b border-slate-100 last:border-0 flex items-start gap-2" data-lat="' + data[i].lat + '" data-lng="' + data[i].lon + '">';
            html += '<i class="bi bi-geo-alt text-blue-400 mt-0.5 flex-shrink-0"></i><span>' + data[i].display_name + '</span></div>';
        }
        resultsEl.innerHTML = html;
        resultsEl.querySelectorAll('.search-result-item').forEach(function(el) {
            el.addEventListener('click', function() {
                map.flyTo([parseFloat(this.dataset.lat), parseFloat(this.dataset.lng)], 17);
                resultsEl.classList.add('hidden');
                document.getElementById('searchInput').value = this.querySelector('span').textContent;
            });
        });
    })
    .catch(function() { resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-red-400">Gagal menghubungi layanan pencarian.</div>'; });
}

function goToMyLocation() {
    var btn = document.getElementById('myLocationBtn');
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> <span class="hidden sm:inline">Mencari...</span>';
    btn.disabled = true;
    if (!navigator.geolocation) { showNotification('error', 'Browser tidak mendukung geolocation'); btn.innerHTML = originalHtml; btn.disabled = false; return; }
    navigator.geolocation.getCurrentPosition(
        function(pos) { map.flyTo([pos.coords.latitude, pos.coords.longitude], 18); btn.innerHTML = originalHtml; btn.disabled = false; },
        function() { showNotification('error', 'Tidak dapat mengakses lokasi'); btn.innerHTML = originalHtml; btn.disabled = false; },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

document.getElementById('searchBtn').addEventListener('click', function() { searchLocation(document.getElementById('searchInput').value); });
document.getElementById('searchInput').addEventListener('keydown', function(e) { if (e.key === 'Enter') searchLocation(e.target.value); });
document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    if (e.target.value.length >= 3) searchTimeout = setTimeout(function() { searchLocation(e.target.value); }, 600);
    else document.getElementById('searchResults').classList.add('hidden');
});
document.getElementById('myLocationBtn').addEventListener('click', goToMyLocation);
document.addEventListener('click', function(e) {
    var el = document.getElementById('searchResults');
    if (el && !e.target.closest('#searchInput') && !e.target.closest('#searchResults') && !e.target.closest('#searchBtn')) el.classList.add('hidden');
});
</script>
@endsection
