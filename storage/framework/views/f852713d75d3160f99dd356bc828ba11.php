<?php $__env->startSection('title', 'Check Out'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Check Out</h1>
            <p class="text-slate-500 text-sm">Validasi lokasi aktif, radius kantor <?php echo e($setting->radius ?? 100); ?> meter.</p>
        </div>
        <div class="clock-card">
            <small class="text-slate-400 text-xs uppercase">Jam Saat Ini</small>
            <h3 class="text-2xl font-bold text-white" id="clock">--:--:--</h3>
            <small class="text-slate-400" id="date">--</small>
        </div>
    </div>

    
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
        <button id="actionBtn" class="btn-checkout px-8 py-3 rounded-xl font-semibold shadow-md">
            <i class="bi bi-box-arrow-right mr-2"></i> Check Out Sekarang
        </button>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ========== DATA DARI LARAVEL ==========
var OFFICE_LOCATION = {
    lat: <?php echo e($setting->lat_kantor ?? -6.208765); ?>,
    lng: <?php echo e($setting->long_kantor ?? 106.845593); ?>,
    radius: <?php echo e($setting->radius ?? 100); ?>

};

// Data user dari SESSION Laravel
var currentUserName = '<?php echo e(session("pengguna_nama", "")); ?>';
var currentUserId = '<?php echo e(session("pengguna_id", "")); ?>';

var map, userMarker, officeMarker, officeCircle, currentPosition, isWithinRadius = false;
var hasCheckedOut = false;

function updateClock() {
    var now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('date').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
}
setInterval(updateClock, 1000);
updateClock();

// CEK STATUS CHECK OUT
function checkTodayCheckOut() {
    fetch('<?php echo e(route("karyawan.checkout.status")); ?>', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(function(response) { return response.json(); })
    .then(function(result) {
        if (result.hasCheckedOut) {
            hasCheckedOut = true;
            enableButton(false);
            showStatusMessage('Anda sudah melakukan check out hari ini', 'warning');
        }
    })
    .catch(function(error) { console.error('Error:', error); });
}

function initMap() {
    if (typeof L === 'undefined') return;
    
    map = L.map('map').setView([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], 17);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
    officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng]).addTo(map).bindPopup('🏢 Lokasi Kantor');
    officeCircle = L.circle([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], {
        color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 0.1, radius: OFFICE_LOCATION.radius
    }).addTo(map);
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000;
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function updateLocationStatus(position) {
    var userLat = position.coords.latitude, userLng = position.coords.longitude;
    currentPosition = { lat: userLat, lng: userLng };
    var distance = calculateDistance(userLat, userLng, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng);
    isWithinRadius = distance <= OFFICE_LOCATION.radius;
    
    document.getElementById('coordinates').innerHTML = '<strong>Latitude:</strong> ' + userLat.toFixed(6) +
        '<br><strong>Longitude:</strong> ' + userLng.toFixed(6) +
        '<br><strong>Akurasi:</strong> ±' + position.coords.accuracy.toFixed(1) + ' meter';
    
    var statusContainer = document.getElementById('locationStatusContainer');
    if (isWithinRadius) {
        statusContainer.innerHTML = '<div class="bg-emerald-100 text-emerald-700 p-3 rounded-xl">' +
            '<i class="bi bi-check-circle-fill mr-2"></i>' +
            '<strong>✅ Dalam Radius Kantor</strong><br>' +
            '<small>Jarak: ' + distance.toFixed(2) + ' meter</small></div>';
        enableButton(true);
    } else {
        statusContainer.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">' +
            '<i class="bi bi-x-circle-fill mr-2"></i>' +
            '<strong>❌ Di Luar Radius Kantor</strong><br>' +
            '<small>Jarak: ' + distance.toFixed(2) + ' meter</small></div>';
        enableButton(false);
    }
    
    if (userMarker) map.removeLayer(userMarker);
    var userIcon = L.divIcon({
        html: '<div style="background-color: ' + (isWithinRadius ? '#10b981' : '#ef4444') +
              '; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>',
        iconSize: [20, 20]
    });
    userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map).bindPopup('Jarak: ' + distance.toFixed(2) + ' meter');
    map.setView([userLat, userLng], 17);
}

function handleLocationError(error) {
    document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">' +
        '<i class="bi bi-exclamation-triangle-fill mr-2"></i><strong>Error</strong><br><small>Izin lokasi ditolak</small></div>';
    enableButton(false);
}

function enableButton(enabled) {
    var btn = document.getElementById('actionBtn');
    if (enabled && !hasCheckedOut) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    } else {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    }
}

function showStatusMessage(message, type) {
    var toast = document.createElement('div');
    var bgColor = (type === 'warning') ? '#f59e0b' : '#ef4444';
    toast.style.cssText = 'position:fixed; bottom:20px; right:20px; background:' + bgColor +
        '; color:white; padding:12px 20px; border-radius:10px; z-index:9999;';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 3000);
}

function performAction() {
    if (!isWithinRadius) {
        alert('Anda tidak dapat check out karena berada di luar radius kantor!');
        return;
    }
    if (hasCheckedOut) {
        alert('Anda sudah melakukan check out hari ini!');
        return;
    }
    
    var submitBtn = document.getElementById('actionBtn');
    var originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split spin"></i> Memproses...';
    submitBtn.disabled = true;
    
    fetch('<?php echo e(route("karyawan.checkout.store")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ latitude: currentPosition.lat, longitude: currentPosition.lng })
    })
    .then(function(response) { return response.json(); })
    .then(function(result) {
        if (result.success) {
            alert(result.message);
            hasCheckedOut = true;
            setTimeout(function() { window.location.href = "<?php echo e(route('karyawan.dashboard')); ?>"; }, 2000);
        } else {
            alert(result.message);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan, silakan coba lagi');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

document.getElementById('actionBtn').addEventListener('click', performAction);
initMap();
checkTodayCheckOut();

if (navigator.geolocation) {
    navigator.geolocation.watchPosition(updateLocationStatus, handleLocationError, { enableHighAccuracy: true });
} else {
    document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">Browser tidak mendukung geolocation</div>';
}

// SEARCH LOKASI
var searchTimeout = null;

function searchLocation(query) {
    if (!query.trim()) return;
    var resultsEl = document.getElementById('searchResults');
    resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">Mencari...</div>';
    resultsEl.classList.remove('hidden');

    fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(query) + '&format=json&limit=5&countrycodes=id')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.length) {
            resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">Lokasi tidak ditemukan.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < data.length; i++) {
            html += '<div class="search-result-item px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 border-b border-slate-100" data-lat="' + data[i].lat + '" data-lng="' + data[i].lon + '">';
            html += '<i class="bi bi-geo-alt text-blue-400 mr-2"></i><span>' + data[i].display_name + '</span></div>';
        }
        resultsEl.innerHTML = html;

        var items = resultsEl.querySelectorAll('.search-result-item');
        for (var j = 0; j < items.length; j++) {
            items[j].addEventListener('click', function(e) {
                var lat = parseFloat(this.dataset.lat);
                var lng = parseFloat(this.dataset.lng);
                if (map) map.flyTo([lat, lng], 17);
                resultsEl.classList.add('hidden');
                document.getElementById('searchInput').value = this.querySelector('span').textContent;
            });
        }
    })
    .catch(function() { resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-red-400">Gagal menghubungi layanan pencarian.</div>'; });
}

function goToMyLocation() {
    var btn = document.getElementById('myLocationBtn');
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Mencari...';
    btn.disabled = true;

    if (!navigator.geolocation) {
        alert('Browser tidak mendukung geolocation.');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(pos) { map.flyTo([pos.coords.latitude, pos.coords.longitude], 18); btn.innerHTML = originalHtml; btn.disabled = false; },
        function() { alert('Tidak dapat mengakses lokasi.'); btn.innerHTML = originalHtml; btn.disabled = false; },
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
    if (!e.target.closest('#searchInput') && !e.target.closest('#searchResults') && !e.target.closest('#searchBtn')) {
        document.getElementById('searchResults').classList.add('hidden');
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.karyawan-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\StaffLog.adl\resources\views/karyawan/check-out.blade.php ENDPATH**/ ?>