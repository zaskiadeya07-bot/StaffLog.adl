@extends('layouts.karyawan-layout')

@section('title', 'Check In / Absen Masuk')

@section('content')
<div>
    {{-- ========== HEADER HALAMAN ========== --}}
    {{-- Menampilkan judul halaman dan radius kantor dari database --}}
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Check In</h1>
            <p class="text-slate-500 text-sm">Validasi lokasi aktif dengan radius kantor {{ $setting->radius ?? 100 }} meter.</p>
        </div>
        {{-- Jam real-time --}}
        <div class="clock-card">
            <small class="text-slate-400 text-xs uppercase">Jam Saat Ini</small>
            <h3 class="text-2xl font-bold text-white" id="clock">--:--:--</h3>
            <small class="text-slate-400" id="date">--</small>
        </div>
    </div>

    {{-- ========== SEARCH BAR LOKASI ========== --}}
    {{-- Fitur mencari alamat menggunakan Nominatim API (OpenStreetMap) --}}
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

    {{-- ========== PETA LEAFLET ========== --}}
    {{-- Menampilkan peta interaktif dengan marker kantor dan lingkaran radius --}}
    <div class="card mb-5 overflow-hidden">
        <div id="map" class="h-96 w-full"></div>
    </div>

    {{-- ========== STATUS DAN KOORDINAT ========== --}}
    {{-- Menampilkan status apakah dalam radius kantor dan koordinat GPS karyawan --}}
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

    {{-- ========== TOMBOL CHECK IN ========== --}}
    {{-- Tombol ini akan aktif hanya jika karyawan berada dalam radius kantor --}}
    <div class="text-center">
        <button id="actionBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold shadow-md transition">
            <i class="bi bi-check-circle mr-2"></i> Check In Sekarang
        </button>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// =========================================================================
// DATA DARI LARAVEL (BACKEND KE FRONTEND)
// =========================================================================
// Data kantor diambil dari database melalui controller ($setting)
// Koordinat dan radius ini bisa diubah admin lewat halaman Pengaturan Kantor
var OFFICE_LOCATION = {
    lat: {{ $setting->lat_kantor ?? -6.208765 }},
    lng: {{ $setting->long_kantor ?? 106.845593 }},
    radius: {{ $setting->radius ?? 100 }}
};

// Data user diambil dari session Laravel (yang login)
var currentUserName = '{{ session("pengguna_nama", "") }}';
var currentUserId = '{{ session("pengguna_id", "") }}';
var currentUser = {
    name: currentUserName,
    id: currentUserId
};

// Variabel global untuk menyimpan state aplikasi
var hasCheckedToday = false;      // Apakah sudah check in hari ini?
var map = null;                   // Objek peta Leaflet
var userMarker = null;            // Marker posisi user di peta
var officeMarker = null;          // Marker posisi kantor
var officeCircle = null;          // Lingkaran radius kantor
var currentPosition = null;       // Posisi terakhir user
var isWithinRadius = false;       // Apakah dalam radius kantor?

// =========================================================================
// FUNGSI JAM REAL-TIME
// =========================================================================
// Menampilkan jam dan tanggal saat ini yang berjalan otomatis
function updateClock() {
    var now = new Date();
    var clockElement = document.getElementById('clock');
    var dateElement = document.getElementById('date');
    if (clockElement) {
        clockElement.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    }
    if (dateElement) {
        dateElement.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }
}
setInterval(updateClock, 1000);  // Update setiap 1 detik
updateClock();

// =========================================================================
// CEK STATUS CHECK IN HARI INI (KE DATABASE)
// =========================================================================
// Mengecek apakah karyawan sudah melakukan check in hari ini
// Data diambil dari database via AJAX (GET request)
function checkTodayAttendance() {
    fetch('{{ route("karyawan.checkin.status") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(result) {
        if (result.hasCheckedIn) {
            hasCheckedToday = true;
            enableButton(false, 'Anda sudah melakukan check in hari ini');
            showStatusMessage('Anda sudah melakukan check in hari ini', 'warning');
        }
    })
    .catch(function(error) {
        console.error('Error cek status:', error);
    });
}

// =========================================================================
// INISIALISASI PETA LEAFLET
// =========================================================================
// Membuat peta, menambahkan tile layer, marker kantor, dan lingkaran radius
function initMap() {
    if (typeof L === 'undefined') {
        console.error('Leaflet not loaded');
        return;
    }
    
    // Buat peta dengan pusat di koordinat kantor, zoom level 17
    map = L.map('map').setView([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], 17);
    
    // Tile layer dari CartoDB (gaya peta light)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
    }).addTo(map);
    
    // Marker kantor (icon marker di peta)
    officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng]).addTo(map).bindPopup('🏢 Lokasi Kantor');
    
    // Lingkaran radius (menunjukkan area yang boleh absen)
    officeCircle = L.circle([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], {
        color: '#3b82f6',
        fillColor: '#3b82f6',
        fillOpacity: 0.1,
        radius: OFFICE_LOCATION.radius
    }).addTo(map);
}

// =========================================================================
// FUNGSI HITUNG JARAK (HAVERSINE FORMULA)
// =========================================================================
// Menghitung jarak antara dua titik koordinat (dalam meter)
// Rumus matematika untuk menghitung jarak di permukaan bumi
function calculateDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000;  // Radius bumi dalam meter
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;  // Jarak dalam meter
}

// =========================================================================
// UPDATE LOKASI & STATUS (DIPANGGIL SETIAP GPS BERUBAH)
// =========================================================================
// Fungsi ini dipanggil setiap kali posisi GPS karyawan berubah
function updateLocationStatus(position) {
    var userLat = position.coords.latitude;
    var userLng = position.coords.longitude;
    var accuracy = position.coords.accuracy;
    
    currentPosition = { lat: userLat, lng: userLng };
    
    // Hitung jarak ke kantor
    var distance = calculateDistance(userLat, userLng, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng);
    isWithinRadius = distance <= OFFICE_LOCATION.radius;
    
    // Update tampilan koordinat di halaman
    var coordsElement = document.getElementById('coordinates');
    if (coordsElement) {
        coordsElement.innerHTML = '<strong>Latitude:</strong> ' + userLat.toFixed(6) +
            '<br><strong>Longitude:</strong> ' + userLng.toFixed(6) +
            '<br><strong>Akurasi:</strong> ±' + accuracy.toFixed(1) + ' meter' +
            '<br><strong>Jarak ke Kantor:</strong> ' + distance.toFixed(2) + ' meter';
    }
    
    // Update status lokasi (dalam radius atau tidak)
    var statusContainer = document.getElementById('locationStatusContainer');
    if (statusContainer) {
        if (isWithinRadius) {
            statusContainer.innerHTML = '<div class="bg-emerald-100 text-emerald-700 p-3 rounded-xl">' +
                '<i class="bi bi-check-circle-fill mr-2"></i>' +
                '<strong>✅ Dalam Radius Kantor</strong><br>' +
                '<small>Jarak: ' + distance.toFixed(2) + ' meter (Maks: ' + OFFICE_LOCATION.radius + ' meter)</small>' +
                '</div>';
            enableButton(true);
        } else {
            statusContainer.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">' +
                '<i class="bi bi-x-circle-fill mr-2"></i>' +
                '<strong>❌ Di Luar Radius Kantor</strong><br>' +
                '<small>Jarak: ' + distance.toFixed(2) + ' meter (Maks: ' + OFFICE_LOCATION.radius + ' meter)</small>' +
                '</div>';
            enableButton(false, 'Anda berada di luar radius kantor');
        }
    }
    
    // Update marker posisi user di peta (warna hijau jika dalam radius, merah jika di luar)
    if (map && userMarker) {
        map.removeLayer(userMarker);
    }
    
    if (map && typeof L !== 'undefined') {
        var userIcon = L.divIcon({
            html: '<div style="background-color: ' + (isWithinRadius ? '#10b981' : '#ef4444') +
                  '; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>',
            iconSize: [20, 20],
            className: 'user-marker'
        });
        userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
        userMarker.bindPopup('📍 Posisi Anda<br>Jarak: ' + distance.toFixed(2) + ' meter');
        map.setView([userLat, userLng], 17);
    }
}

// =========================================================================
// BAGIAN 7: ERROR HANDLER GEOLOCATION
// =========================================================================
// Menangani error jika GPS tidak bisa mengakses lokasi
function handleLocationError(error) {
    var statusContainer = document.getElementById('locationStatusContainer');
    var errorMessage = '';
    
    switch(error.code) {
        case error.PERMISSION_DENIED:
            errorMessage = 'Izin lokasi ditolak. Silakan aktifkan GPS dan izinkan akses lokasi.';
            break;
        case error.POSITION_UNAVAILABLE:
            errorMessage = 'Informasi lokasi tidak tersedia.';
            break;
        case error.TIMEOUT:
            errorMessage = 'Waktu permintaan lokasi habis.';
            break;
        default:
            errorMessage = 'Terjadi kesalahan saat mengakses lokasi.';
    }
    
    if (statusContainer) {
        statusContainer.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">' +
            '<i class="bi bi-exclamation-triangle-fill mr-2"></i>' +
            '<strong>Error</strong><br><small>' + errorMessage + '</small>' +
            '</div>';
    }
    enableButton(false, errorMessage);
}

// =========================================================================
// ENABLE/DISABLE BUTTON
// =========================================================================
// Mengaktifkan atau menonaktifkan tombol check in berdasarkan kondisi
function enableButton(enabled, reason) {
    var btn = document.getElementById('actionBtn');
    if (!btn) return;
    
    if (enabled && !hasCheckedToday) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    } else {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
        if (reason) btn.title = reason;
    }
}

// =========================================================================
// SHOW TOAST MESSAGE
// =========================================================================
function showStatusMessage(message, type) {
    var toast = document.createElement('div');
    var bgColor = (type === 'warning') ? '#f59e0b' : '#ef4444';
    toast.style.cssText = 'position:fixed; bottom:20px; right:20px; background:' + bgColor + '; color:white; padding:12px 20px; border-radius:10px; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.2);';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(function() {
        toast.remove();
    }, 3000);
}

// =========================================================================
// PROSES CHECK IN 
// =========================================================================
// Fungsi utama untuk menyimpan data check in ke database
function performAction() {
    // Validasi: cek apakah dalam radius kantor
    if (!isWithinRadius) {
        alert('Anda tidak dapat check in karena berada di luar radius kantor!');
        return;
    }
    // Validasi: cek apakah sudah check in hari ini
    if (hasCheckedToday) {
        alert('Anda sudah melakukan check in hari ini!');
        return;
    }
    
    // Tampilkan loading state
    var submitBtn = document.getElementById('actionBtn');
    var originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split spin"></i> Memproses...';
    submitBtn.disabled = true;
    
    // Kirim data ke server via AJAX (POST request)
    fetch('{{ route("karyawan.checkin.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',  // Token keamanan Laravel
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            latitude: currentPosition.lat,
            longitude: currentPosition.lng
        })
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(result) {
        if (result.success) {
            alert(result.message);
            hasCheckedToday = true;
            // Redirect ke dashboard setelah 2 detik
            setTimeout(function() {
                window.location.href = "{{ route('karyawan.dashboard') }}";
            }, 2000);
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

// =========================================================================
// EVENT LISTENER TOMBOL CHECK IN
// =========================================================================
var actionBtn = document.getElementById('actionBtn');
if (actionBtn) {
    actionBtn.addEventListener('click', performAction);
}

// =========================================================================
// INITIALISASI (MENJALANKAN SEMUA FUNGSI SAAT HALAMAN LOAD)
// =========================================================================
initMap();                     // Load peta
checkTodayAttendance();        // Cek status check in hari ini

// =========================================================================
// GEOLOCATION (MENDETEKSI LOKASI GPS KARYAWAN)
// =========================================================================
if (navigator.geolocation) {
    // watchPosition akan terus memantau perubahan lokasi
    navigator.geolocation.watchPosition(updateLocationStatus, handleLocationError, {
        enableHighAccuracy: true,  // Gunakan GPS akurasi tinggi
        timeout: 10000,            // Timeout 10 detik
        maximumAge: 0              // Jangan cache lokasi lama
    });
} else {
    var statusContainer = document.getElementById('locationStatusContainer');
    if (statusContainer) {
        statusContainer.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded-xl">Browser tidak mendukung geolocation</div>';
    }
}

// =========================================================================
// SEARCH LOKASI (NOMINATIM API - OPENTREETMAP)
// =========================================================================
// Fitur mencari alamat menggunakan API Nominatim dari OpenStreetMap
var searchTimeout = null;

function searchLocation(query) {
    if (!query.trim()) return;
    var resultsEl = document.getElementById('searchResults');
    if (!resultsEl) return;
    
    resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400"><i class="bi bi-hourglass-split mr-2"></i>Mencari...</div>';
    resultsEl.classList.remove('hidden');

    fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(query) + '&format=json&limit=5&countrycodes=id', {
        headers: { 'Accept-Language': 'id' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.length) {
            resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">Lokasi tidak ditemukan.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < data.length; i++) {
            html += '<div class="search-result-item px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 border-b border-slate-100 last:border-0 flex items-start gap-2" data-lat="' + data[i].lat + '" data-lng="' + data[i].lon + '">';
            html += '<i class="bi bi-geo-alt text-blue-400 mt-0.5 flex-shrink-0"></i>';
            html += '<span>' + data[i].display_name + '</span>';
            html += '</div>';
        }
        resultsEl.innerHTML = html;

        var items = resultsEl.querySelectorAll('.search-result-item');
        for (var j = 0; j < items.length; j++) {
            items[j].addEventListener('click', function(e) {
                var lat = parseFloat(this.dataset.lat);
                var lng = parseFloat(this.dataset.lng);
                if (map) {
                    map.flyTo([lat, lng], 17);
                }
                resultsEl.classList.add('hidden');
                var searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = this.querySelector('span').textContent;
                }
            });
        }
    })
    .catch(function() {
        if (resultsEl) {
            resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-red-400">Gagal menghubungi layanan pencarian.</div>';
        }
    });
}

// =========================================================================
// LOKASI SAYA (GPS KE POSISI TERKINI)
// =========================================================================
function goToMyLocation() {
    var btn = document.getElementById('myLocationBtn');
    if (!btn) return;
    
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> <span class="hidden sm:inline">Mencari...</span>';
    btn.disabled = true;

    if (!navigator.geolocation) {
        alert('Browser tidak mendukung geolocation.');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            if (map) {
                map.flyTo([lat, lng], 18);
            }
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        },
        function() {
            alert('Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

// =========================================================================
// EVENT LISTENER SEARCH DAN LOKASI SAYA
// =========================================================================
var searchBtn = document.getElementById('searchBtn');
var searchInput = document.getElementById('searchInput');
var myLocationBtn = document.getElementById('myLocationBtn');

if (searchBtn) {
    searchBtn.addEventListener('click', function() {
        if (searchInput) {
            searchLocation(searchInput.value);
        }
    });
}

if (searchInput) {
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            searchLocation(e.target.value);
        }
    });
    
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        if (e.target.value.length >= 3) {
            searchTimeout = setTimeout(function() {
                searchLocation(e.target.value);
            }, 600);
        } else {
            var resultsEl = document.getElementById('searchResults');
            if (resultsEl) {
                resultsEl.classList.add('hidden');
            }
        }
    });
}

if (myLocationBtn) {
    myLocationBtn.addEventListener('click', goToMyLocation);
}

// =========================================================================
// TUTUP DROPDOWN SAAT KLIK DI LUAR
// =========================================================================
document.addEventListener('click', function(e) {
    var searchInputEl = document.getElementById('searchInput');
    var searchResultsEl = document.getElementById('searchResults');
    var searchBtnEl = document.getElementById('searchBtn');
    
    if (searchResultsEl && !e.target.closest('#searchInput') && !e.target.closest('#searchResults') && !e.target.closest('#searchBtn')) {
        searchResultsEl.classList.add('hidden');
    }
});
</script>
@endsection