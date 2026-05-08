@extends('layouts.karyawan-layout')

@section('title', 'Check Out')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div><h1 class="text-2xl font-bold text-slate-800">Check Out</h1><p class="text-slate-500 text-sm">Validasi lokasi aktif, radius kantor {{ $setting ? $setting->radius : 100 }} meter.</p></div>
        <div class="clock-card"><small class="text-slate-400 text-xs uppercase">Jam Saat Ini</small><h3 class="text-2xl font-bold text-white" id="clock">--:--:--</h3><small class="text-slate-400" id="date">--</small></div>
    </div>

    {{-- ── SEARCH BAR LOKASI ── --}}
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

    <div class="card mb-5 overflow-hidden"><div id="map" class="h-96 w-full"></div></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
        <div class="card p-5"><label class="font-semibold text-slate-700 mb-2 block"><i class="bi bi-geo-alt-fill text-blue-500 mr-1"></i> Status Lokasi</label><div id="locationStatusContainer"><p class="text-slate-500">Mendeteksi lokasi...</p></div></div>
        <div class="card p-5"><label class="font-semibold text-slate-700 mb-2 block"><i class="bi bi-pin-map-fill text-blue-500 mr-1"></i> Koordinat Karyawan</label><p id="coordinates" class="text-slate-500 text-sm">Belum tersedia</p></div>
    </div>

    <div class="text-center"><button id="actionBtn" class="btn-checkout px-8 py-3 rounded-xl font-semibold shadow-md"><i class="bi bi-box-arrow-right mr-2"></i> Check Out Sekarang</button></div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const OFFICE_LOCATION = {
        lat:    {{ $setting ? (float)$setting->lat_kantor  : -6.20876500 }},
        lng:    {{ $setting ? (float)$setting->long_kantor : 106.84559300 }},
        radius: {{ $setting ? (int)$setting->radius        : 100 }}
    };
    let currentUser = { name: localStorage.getItem('userName') || 'Budi Santoso', id: localStorage.getItem('userId') || 'KRY-001' };
    let map, userMarker, officeMarker, officeCircle, currentPosition, isWithinRadius = false;

    function updateClock() { const now = new Date(); document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false }); document.getElementById('date').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); }
    setInterval(updateClock, 1000); updateClock();

    function initMap() {
        map = L.map('map').setView([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], 17);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
        officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng]).addTo(map).bindPopup('📍 Lokasi Kantor');
        officeCircle = L.circle([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], { color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 0.1, radius: OFFICE_LOCATION.radius }).addTo(map);

        // ── Custom Leaflet control: tombol Lokasi Saya di pojok kanan atas peta ──
        const LocateControl = L.Control.extend({
            options: { position: 'topright' },
            onAdd: function () {
                const btn = L.DomUtil.create('button', '');
                btn.title = 'Lokasi Saya';
                btn.innerHTML = '<i class="bi bi-crosshair2"></i>';
                btn.style.cssText = 'background:#10b981;color:#fff;border:none;width:34px;height:34px;border-radius:6px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 5px rgba(0,0,0,.4);';
                L.DomEvent.on(btn, 'click', L.DomEvent.stopPropagation);
                L.DomEvent.on(btn, 'click', L.DomEvent.preventDefault);
                L.DomEvent.on(btn, 'click', function () { goToMyLocation(); });
                return btn;
            }
        });
        new LocateControl().addTo(map);
    }

    function calculateDistance(lat1, lon1, lat2, lon2) { const R = 6371000; const dLat = (lat2 - lat1) * Math.PI / 180; const dLon = (lon2 - lon1) * Math.PI / 180; const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2); const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); return R * c; }

    function updateLocationStatus(position) {
        const userLat = position.coords.latitude, userLng = position.coords.longitude;
        currentPosition = { lat: userLat, lng: userLng };
        const distance = calculateDistance(userLat, userLng, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng);
        isWithinRadius = distance <= OFFICE_LOCATION.radius;
        document.getElementById('coordinates').innerHTML = `<strong>Latitude:</strong> ${userLat.toFixed(6)}<br><strong>Longitude:</strong> ${userLng.toFixed(6)}<br><strong>Akurasi:</strong> ±${position.coords.accuracy.toFixed(1)} meter`;
        const statusContainer = document.getElementById('locationStatusContainer');
        if (isWithinRadius) { statusContainer.innerHTML = `<div class="bg-emerald-100 text-emerald-700 p-3 rounded-xl"><i class="bi bi-check-circle-fill mr-2"></i><strong>✅ Dalam Radius Kantor</strong><br><small>Jarak: ${distance.toFixed(2)} meter</small></div>`; enableButton(true); }
        else { statusContainer.innerHTML = `<div class="bg-red-100 text-red-700 p-3 rounded-xl"><i class="bi bi-x-circle-fill mr-2"></i><strong>❌ Di Luar Radius Kantor</strong><br><small>Jarak: ${distance.toFixed(2)} meter</small></div>`; enableButton(false); }
        if (userMarker) map.removeLayer(userMarker);
        const userIcon = L.divIcon({ html: `<div style="background-color: ${isWithinRadius ? '#10b981' : '#ef4444'}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>`, iconSize: [20, 20] });
        userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map).bindPopup(`Jarak: ${distance.toFixed(2)} meter`);
        map.setView([userLat, userLng], 17);
    }

    function handleLocationError(error) { document.getElementById('locationStatusContainer').innerHTML = `<div class="bg-red-100 text-red-700 p-3 rounded-xl"><i class="bi bi-exclamation-triangle-fill mr-2"></i><strong>Error</strong><br><small>Izin lokasi ditolak</small></div>`; enableButton(false); }

    function enableButton(enabled) { const btn = document.getElementById('actionBtn'); if (enabled) { btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; } else { btn.disabled = true; btn.style.opacity = '0.5'; btn.style.cursor = 'not-allowed'; } }

    function performAction() {
        if (!isWithinRadius) { alert('Anda tidak dapat check out karena berada di luar radius kantor!'); return; }
        const now = new Date(); const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); const date = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }); const day = now.toLocaleDateString('id-ID', { weekday: 'long' });
        alert(`✅ Check Out Berhasil!\n\nHari/Tanggal: ${day}, ${date}\nPukul: ${time}\n\nIstirahat yang cukup!`);
        setTimeout(() => { window.location.href = "{{ route('karyawan.dashboard') }}"; }, 1500);
    }

    document.getElementById('actionBtn').addEventListener('click', performAction);
    initMap();
    if (navigator.geolocation) navigator.geolocation.watchPosition(updateLocationStatus, handleLocationError, { enableHighAccuracy: true });
    else document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">Browser tidak mendukung geolocation</div>';

    // ── SEARCH LOKASI (Nominatim) ──
    let searchTimeout = null;

    function searchLocation(query) {
        if (!query.trim()) return;
        const resultsEl = document.getElementById('searchResults');
        resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400"><i class="bi bi-hourglass-split mr-2"></i>Mencari...</div>';
        resultsEl.classList.remove('hidden');

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=5&countrycodes=id`, {
            headers: { 'Accept-Language': 'id' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">Lokasi tidak ditemukan.</div>';
                return;
            }
            resultsEl.innerHTML = data.map(item => `
                <div class="search-result-item px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 border-b border-slate-100 last:border-0 flex items-start gap-2"
                     data-lat="${item.lat}" data-lng="${item.lon}">
                    <i class="bi bi-geo-alt text-blue-400 mt-0.5 flex-shrink-0"></i>
                    <span>${item.display_name}</span>
                </div>
            `).join('');

            resultsEl.querySelectorAll('.search-result-item').forEach(el => {
                el.addEventListener('click', () => {
                    const lat = parseFloat(el.dataset.lat);
                    const lng = parseFloat(el.dataset.lng);
                    map.flyTo([lat, lng], 17, { animate: true, duration: 1 });
                    resultsEl.classList.add('hidden');
                    document.getElementById('searchInput').value = el.querySelector('span').textContent;
                });
            });
        })
        .catch(() => {
            resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-red-400">Gagal menghubungi layanan pencarian.</div>';
        });
    }

    // ── TOMBOL LOKASI SAYA ──
    function goToMyLocation() {
        const btn = document.getElementById('myLocationBtn');
        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> <span class="hidden sm:inline">Mencari...</span>';
        btn.disabled = true;

        if (!navigator.geolocation) {
            alert('Browser tidak mendukung geolocation.');
            btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>';
            btn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude, lng = pos.coords.longitude;
                map.flyTo([lat, lng], 18, { animate: true, duration: 1.2 });
                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>';
                btn.disabled = false;
            },
            () => {
                alert('Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.');
                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    document.getElementById('searchBtn').addEventListener('click', () => searchLocation(document.getElementById('searchInput').value));
    document.getElementById('searchInput').addEventListener('keydown', e => { if (e.key === 'Enter') searchLocation(e.target.value); });
    document.getElementById('searchInput').addEventListener('input', e => {
        clearTimeout(searchTimeout);
        if (e.target.value.length >= 3) searchTimeout = setTimeout(() => searchLocation(e.target.value), 600);
        else document.getElementById('searchResults').classList.add('hidden');
    });
    document.getElementById('myLocationBtn').addEventListener('click', goToMyLocation);

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', e => {
        if (!e.target.closest('#searchInput') && !e.target.closest('#searchResults') && !e.target.closest('#searchBtn')) {
            document.getElementById('searchResults').classList.add('hidden');
        }
    });
</script>
@endsection
