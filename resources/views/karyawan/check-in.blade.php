@extends('layouts.karyawan-layout')

@section('title', 'Check In')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div><h1 class="text-2xl font-bold text-slate-800">Check In</h1><p class="text-slate-500 text-sm">Validasi lokasi aktif dengan radius kantor 100 meter.</p></div>
        <div class="clock-card"><small class="text-slate-400 text-xs uppercase">Jam Saat Ini</small><h3 class="text-2xl font-bold text-white" id="clock">--:--:--</h3><small class="text-slate-400" id="date">--</small></div>
    </div>
    
    <div class="card mb-5 overflow-hidden"><div id="map" class="h-96 w-full"></div></div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
        <div class="card p-5"><label class="font-semibold text-slate-700 mb-2 block"><i class="bi bi-geo-alt-fill text-blue-500 mr-1"></i> Status Lokasi</label><div id="locationStatusContainer"><p class="text-slate-500">Mendeteksi lokasi...</p></div></div>
        <div class="card p-5"><label class="font-semibold text-slate-700 mb-2 block"><i class="bi bi-pin-map-fill text-blue-500 mr-1"></i> Koordinat Karyawan</label><p id="coordinates" class="text-slate-500 text-sm">Belum tersedia</p></div>
    </div>
    
    <div class="text-center"><button id="actionBtn" class="btn-checkin px-8 py-3 rounded-xl font-semibold shadow-md"><i class="bi bi-check-circle mr-2"></i> Check In Sekarang</button></div>
</div>

<script>
    const OFFICE_LOCATION = { lat: -6.200000, lng: 106.816666, radius: 100 };
    let currentUser = { name: localStorage.getItem('userName') || 'Budi Santoso', id: localStorage.getItem('userId') || 'KRY-001' };
    let map, userMarker, officeMarker, officeCircle, currentPosition, isWithinRadius = false, hasCheckedToday = false;
    
    function updateClock() { const now = new Date(); document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false }); document.getElementById('date').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); }
    setInterval(updateClock, 1000); updateClock();
    
    function checkTodayAttendance() { const today = new Date().toDateString(); const lastCheckIn = localStorage.getItem(`lastCheckIn_${currentUser.id}`); if (lastCheckIn === today) { hasCheckedToday = true; enableButton(false, 'Anda sudah melakukan check in hari ini'); } }
    
    function initMap() { map = L.map('map').setView([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], 17); L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map); officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng]).addTo(map).bindPopup('📍 Lokasi Kantor'); officeCircle = L.circle([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], { color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 0.1, radius: OFFICE_LOCATION.radius }).addTo(map); }
    
    function calculateDistance(lat1, lon1, lat2, lon2) { const R = 6371000; const dLat = (lat2 - lat1) * Math.PI / 180; const dLon = (lon2 - lon1) * Math.PI / 180; const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2); const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); return R * c; }
    
    function updateLocationStatus(position) {
        const userLat = position.coords.latitude, userLng = position.coords.longitude;
        currentPosition = { lat: userLat, lng: userLng };
        const distance = calculateDistance(userLat, userLng, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng);
        isWithinRadius = distance <= OFFICE_LOCATION.radius;
        document.getElementById('coordinates').innerHTML = `<strong>Latitude:</strong> ${userLat.toFixed(6)}<br><strong>Longitude:</strong> ${userLng.toFixed(6)}<br><strong>Akurasi:</strong> ±${position.coords.accuracy.toFixed(1)} meter`;
        const statusContainer = document.getElementById('locationStatusContainer');
        if (isWithinRadius) { statusContainer.innerHTML = `<div class="bg-emerald-100 text-emerald-700 p-3 rounded-xl"><i class="bi bi-check-circle-fill mr-2"></i><strong>✅ Dalam Radius Kantor</strong><br><small>Jarak: ${distance.toFixed(2)} meter</small></div>`; enableButton(true); }
        else { statusContainer.innerHTML = `<div class="bg-red-100 text-red-700 p-3 rounded-xl"><i class="bi bi-x-circle-fill mr-2"></i><strong>❌ Di Luar Radius Kantor</strong><br><small>Jarak: ${distance.toFixed(2)} meter</small></div>`; enableButton(false, 'Anda berada di luar radius kantor'); }
        if (userMarker) map.removeLayer(userMarker);
        const userIcon = L.divIcon({ html: `<div style="background-color: ${isWithinRadius ? '#10b981' : '#ef4444'}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>`, iconSize: [20, 20] });
        userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map).bindPopup(`Jarak: ${distance.toFixed(2)} meter`);
        map.setView([userLat, userLng], 17);
    }
    
    function handleLocationError(error) { document.getElementById('locationStatusContainer').innerHTML = `<div class="bg-red-100 text-red-700 p-3 rounded-xl"><i class="bi bi-exclamation-triangle-fill mr-2"></i><strong>Error</strong><br><small>Izin lokasi ditolak</small></div>`; enableButton(false, 'Izin lokasi ditolak'); }
    
    function enableButton(enabled, reason = '') { const btn = document.getElementById('actionBtn'); if (enabled && !hasCheckedToday) { btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; } else { btn.disabled = true; btn.style.opacity = '0.5'; btn.style.cursor = 'not-allowed'; if (reason) btn.title = reason; } }
    
    function performAction() {
        if (!isWithinRadius) { alert('Anda tidak dapat check in karena berada di luar radius kantor!'); return; }
        if (hasCheckedToday) { alert('Anda sudah melakukan check in hari ini!'); return; }
        const now = new Date(); const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); const date = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }); const day = now.toLocaleDateString('id-ID', { weekday: 'long' });
        localStorage.setItem(`lastCheckIn_${currentUser.id}`, now.toDateString());
        alert(`✅ Check In Berhasil!\n\nHari/Tanggal: ${day}, ${date}\nPukul: ${time}\n\nSelamat bekerja!`);
        setTimeout(() => { window.location.href = "{{ route('karyawan.dashboard') }}"; }, 1500);
    }
    
    document.getElementById('actionBtn').addEventListener('click', performAction);
    initMap(); checkTodayAttendance();
    if (navigator.geolocation) navigator.geolocation.watchPosition(updateLocationStatus, handleLocationError, { enableHighAccuracy: true });
    else document.getElementById('locationStatusContainer').innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">Browser tidak mendukung geolocation</div>';
</script>
@endsection