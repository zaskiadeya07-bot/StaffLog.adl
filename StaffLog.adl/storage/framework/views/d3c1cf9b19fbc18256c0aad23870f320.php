<?php $__env->startSection('title', 'Pengaturan Kantor'); ?>

<?php $__env->startSection('content'); ?>


<div class="flex justify-between items-center flex-wrap gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Pengaturan Kantor</h1>
        <p class="text-slate-500 text-sm">Atur lokasi, radius, dan jam kerja kantor</p>
    </div>
    <span class="inline-flex items-center gap-2 text-sm text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg">
        <i class="bi bi-building"></i> Konfigurasi Absensi
    </span>
</div>


<?php if(session('success')): ?>
    <div id="flashSuccess"
         class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700
                rounded-xl px-4 py-3 mb-5 shadow-sm">
        <i class="bi bi-check-circle-fill text-emerald-500 text-lg flex-shrink-0"></i>
        <span class="text-sm font-medium"><?php echo e(session('success')); ?></span>
        <button onclick="document.getElementById('flashSuccess').remove()"
                class="ml-auto text-emerald-500 hover:text-emerald-700 transition">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>


<form action="<?php echo e(route('admin.pengaturan-kantor.update')); ?>" method="POST" id="formPengaturan">
    <?php echo csrf_field(); ?>
    <?php echo method_field('POST'); ?>

    
    <div class="card mb-5">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="bi bi-map text-blue-500 text-lg"></i>
            <h2 class="font-semibold text-slate-700">Lokasi Kantor di Peta</h2>
            <span class="ml-auto text-xs text-slate-400 hidden sm:block">
                Drag marker atau klik peta untuk memindahkan posisi kantor
            </span>
        </div>

        
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="adminSearchInput" placeholder="Cari lokasi kantor..."
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white">
                </div>
                <button id="adminSearchBtn" type="button"
                    class="btn-primary px-4 py-2 text-sm rounded-xl">
                    <i class="bi bi-search"></i> <span class="hidden sm:inline">Cari</span>
                </button>
                <button id="adminMyLocationBtn" type="button" title="Lokasi Saya"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 transition">
                    <i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>
                </button>
            </div>
            <div id="adminSearchResults"
                 class="mt-2 hidden border border-slate-200 rounded-xl overflow-hidden bg-white shadow-lg max-h-52 overflow-y-auto relative z-50">
            </div>
        </div>

        
        <div id="officeMap" style="height:450px; width:100%; border-radius:0;"></div>

        
        <div class="px-5 py-2 bg-blue-50 border-t border-blue-100 flex items-center gap-2 sm:hidden">
            <i class="bi bi-info-circle text-blue-400 text-sm"></i>
            <p class="text-xs text-blue-600">Drag marker atau ketuk peta untuk memindahkan posisi kantor.</p>
        </div>
    </div>

    
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="bi bi-sliders text-indigo-500 text-lg"></i>
            <h2 class="font-semibold text-slate-700">Parameter Absensi</h2>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                
                <div class="flex flex-col gap-1.5">
                    <label for="jam_masuk_std"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-clock text-slate-400"></i> Jam Masuk Standar
                    </label>
                    <input
                        type="time"
                        id="jam_masuk_std"
                        name="jam_masuk_std"
                        class="input-field w-full"
                        value="<?php echo e(old('jam_masuk_std', $setting->jam_masuk_std ?? '08:00')); ?>"
                        required
                    >
                    <p class="text-xs text-slate-400">Batas waktu karyawan dianggap hadir tepat waktu.</p>
                </div>

                
                <div class="flex flex-col gap-1.5">
                    <label for="jam_pulang_std"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-clock-history text-slate-400"></i> Jam Pulang Standar
                    </label>
                    <input
                        type="time"
                        id="jam_pulang_std"
                        name="jam_pulang_std"
                        class="input-field w-full"
                        value="<?php echo e(old('jam_pulang_std', $setting->jam_pulang_std ?? '17:00')); ?>"
                        required
                    >
                    <p class="text-xs text-slate-400">Waktu normal karyawan diperbolehkan pulang.</p>
                </div>

                
                <div class="flex flex-col gap-1.5">
                    <label for="toleransi_menit"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-hourglass-split text-slate-400"></i> Toleransi Keterlambatan
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="toleransi_menit"
                            name="toleransi"
                            class="input-field w-full pr-14"
                            min="0"
                            step="1"
                            value="<?php echo e(old('toleransi', $setting->toleransi ?? 15)); ?>"
                            required
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">
                            menit
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Menit tambahan sebelum absen dihitung terlambat.</p>
                </div>

                
                <div class="flex flex-col gap-1.5">
                    <label for="latitude"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-geo text-slate-400"></i> Latitude
                        <span class="ml-auto text-xs text-blue-500 font-normal bg-blue-50 px-2 py-0.5 rounded-full">
                            Auto dari peta
                        </span>
                    </label>
                    <input
                        type="number"
                        id="latitude"
                        name="lat_kantor"
                        class="input-field w-full bg-slate-50 cursor-not-allowed font-mono text-sm"
                        step="any"
                        readonly
                        value="<?php echo e(old('lat_kantor', $setting->lat_kantor ?? -6.20876500)); ?>"
                        required
                    >
                    <p class="text-xs text-slate-400">Diperbarui otomatis saat marker dipindahkan.</p>
                </div>

                
                <div class="flex flex-col gap-1.5">
                    <label for="longitude"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-geo-alt text-slate-400"></i> Longitude
                        <span class="ml-auto text-xs text-blue-500 font-normal bg-blue-50 px-2 py-0.5 rounded-full">
                            Auto dari peta
                        </span>
                    </label>
                    <input
                        type="number"
                        id="longitude"
                        name="long_kantor"
                        class="input-field w-full bg-slate-50 cursor-not-allowed font-mono text-sm"
                        step="any"
                        readonly
                        value="<?php echo e(old('long_kantor', $setting->long_kantor ?? 106.84559300)); ?>"
                        required
                    >
                    <p class="text-xs text-slate-400">Diperbarui otomatis saat marker dipindahkan.</p>
                </div>

                
                <div class="flex flex-col gap-1.5">
                    <label for="radius_meter"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-circle text-slate-400"></i> Radius Absensi
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="radius_meter"
                            name="radius"
                            class="input-field w-full pr-12"
                            min="10"
                            step="1"
                            value="<?php echo e(old('radius', $setting->radius ?? 100)); ?>"
                            required
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">
                            meter
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Jangkauan area di mana karyawan bisa absen.</p>
                </div>

            </div>

            
            <div class="mt-5 flex items-center gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                <div id="radiusPreviewDot"
                     class="w-8 h-8 rounded-full border-2 border-blue-400 bg-blue-100 flex-shrink-0 flex items-center justify-center">
                    <i class="bi bi-building text-blue-500 text-xs"></i>
                </div>
                <div class="text-sm text-indigo-700">
                    Lingkaran biru di peta menunjukkan radius
                    <strong id="radiusPreviewLabel"><?php echo e(old('radius', $setting->radius ?? 100)); ?></strong>
                    meter dari titik kantor.
                    Karyawan yang berada di dalam lingkaran ini dapat melakukan absensi.
                </div>
            </div>

        </div>

        
        <div class="px-5 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <i class="bi bi-info-circle"></i>
                <span>Pastikan koordinat sudah benar sebelum menyimpan.</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        onclick="resetToDefault()"
                        class="btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Default
                </button>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-floppy"></i> Simpan Pengaturan
                </button>
            </div>
        </div>

    </div>

</form>

<?php $__env->stopSection(); ?>




<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    'use strict';

    /* ── 1. NILAI AWAL ──────────────────────────────────────────────────── */
    const DEFAULTS = {
        lat      : -6.20876500,
        lng      : 106.84559300,
        radius   : 100,
        zoom     : 16
    };

    const initLat    = parseFloat(document.getElementById('latitude').value)    || DEFAULTS.lat;
    const initLng    = parseFloat(document.getElementById('longitude').value)   || DEFAULTS.lng;
    const initRadius = parseFloat(document.getElementById('radius_meter').value) || DEFAULTS.radius;

    /* ── 2. INISIALISASI PETA ───────────────────────────────────────────── */
    const map = L.map('officeMap', {
        center     : [initLat, initLng],
        zoom       : DEFAULTS.zoom,
        zoomControl: true
    });

    /* CartoDB Positron (light, no label noise) */
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution : '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains  : 'abcd',
        maxZoom     : 20
    }).addTo(map);

    /* ── 3. CUSTOM MARKER ICON ──────────────────────────────────────────── */
    const officeIcon = L.divIcon({
        className : '',
        html      : `
            <div style="
                width:36px; height:36px;
                background:#2563eb;
                border:3px solid white;
                border-radius:50% 50% 50% 0;
                transform:rotate(-45deg);
                box-shadow:0 2px 8px rgba(37,99,235,.45);
                display:flex; align-items:center; justify-content:center;
            ">
                <div style="
                    width:10px; height:10px;
                    background:white;
                    border-radius:50%;
                    transform:rotate(45deg);
                "></div>
            </div>`,
        iconSize    : [36, 36],
        iconAnchor  : [18, 36],
        popupAnchor : [0, -38]
    });

    /* ── 4. MARKER & CIRCLE ─────────────────────────────────────────────── */
    const marker = L.marker([initLat, initLng], {
        icon     : officeIcon,
        draggable: true
    }).addTo(map);

    marker.bindTooltip('📍 Kantor', {
        permanent  : false,
        direction  : 'top',
        offset     : [0, -38],
        className  : 'leaflet-tooltip-custom'
    });

    const circle = L.circle([initLat, initLng], {
        color       : '#2563eb',
        fillColor   : '#3b82f6',
        fillOpacity : 0.12,
        weight      : 2,
        radius      : initRadius
    }).addTo(map);

    /* ── 5. HELPER: UPDATE FIELD & LINGKARAN ───────────────────────────── */
    function updateCoords(lat, lng) {
        document.getElementById('latitude').value  = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]);
    }

    function syncRadiusLabel() {
        const r = parseFloat(document.getElementById('radius_meter').value) || 0;
        document.getElementById('radiusPreviewLabel').textContent = r;
        circle.setRadius(r);
    }

    /* ── 6. EVENT: DRAG MARKER ──────────────────────────────────────────── */
    marker.on('drag', function (e) {
        const pos = e.target.getLatLng();
        document.getElementById('latitude').value  = pos.lat.toFixed(8);
        document.getElementById('longitude').value = pos.lng.toFixed(8);
        circle.setLatLng([pos.lat, pos.lng]);
    });

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateCoords(pos.lat, pos.lng);
        showCoordToast(pos.lat, pos.lng);
    });

    /* ── 7. EVENT: KLIK DI PETA ─────────────────────────────────────────── */
    map.on('click', function (e) {
        updateCoords(e.latlng.lat, e.latlng.lng);
        map.panTo([e.latlng.lat, e.latlng.lng], { animate: true });
        showCoordToast(e.latlng.lat, e.latlng.lng);
    });

    /* ── 8. EVENT: RADIUS INPUT ─────────────────────────────────────────── */
    document.getElementById('radius_meter').addEventListener('input', syncRadiusLabel);

    /* ── 9. TOAST KOORDINAT ─────────────────────────────────────────────── */
    let toastTimer = null;
    function showCoordToast(lat, lng) {
        let toast = document.getElementById('coordToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'coordToast';
            toast.style.cssText = `
                position:fixed; bottom:24px; left:50%; transform:translateX(-50%);
                background:#1e293b; color:white;
                padding:10px 18px; border-radius:999px;
                font-size:13px; font-family:monospace;
                box-shadow:0 4px 16px rgba(0,0,0,.25);
                z-index:9999; transition:opacity .3s;
                display:flex; align-items:center; gap:8px;
            `;
            document.body.appendChild(toast);
        }
        toast.innerHTML = `<span style="color:#60a5fa">📍</span> ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        toast.style.opacity = '1';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { toast.style.opacity = '0'; }, 2500);
    }

    /* ── 10. RESET TO DEFAULT ───────────────────────────────────────────── */
    window.resetToDefault = function () {
        if (!confirm('Reset koordinat ke lokasi default?')) return;

        document.getElementById('latitude').value      = DEFAULTS.lat.toFixed(8);
        document.getElementById('longitude').value     = DEFAULTS.lng.toFixed(8);
        document.getElementById('radius_meter').value  = DEFAULTS.radius;
        document.getElementById('radiusPreviewLabel').textContent = DEFAULTS.radius;

        updateCoords(DEFAULTS.lat, DEFAULTS.lng);
        circle.setRadius(DEFAULTS.radius);
        map.flyTo([DEFAULTS.lat, DEFAULTS.lng], DEFAULTS.zoom, { animate: true, duration: 1 });
    };

    /* ── 11. VALIDASI FORM ──────────────────────────────────────────────── */
    document.getElementById('formPengaturan').addEventListener('submit', function (e) {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        const rad = parseFloat(document.getElementById('radius_meter').value);

        if (isNaN(lat) || isNaN(lng)) {
            e.preventDefault();
            alert('Koordinat kantor belum diatur. Klik atau drag marker di peta.');
            return;
        }
        if (rad < 10) {
            e.preventDefault();
            alert('Radius minimal adalah 10 meter.');
            return;
        }
    });

    /* ── 12. FIX: LEAFLET MAP SIZE (jika layout menyebabkan container 0px) */
    setTimeout(function () { map.invalidateSize(); }, 300);

    /* ── SEARCH LOKASI (Nominatim) ──────────────────────────────────────── */
    var adminSearchTimeout = null;

    function adminSearchLocation(query) {
        if (!query.trim()) return;
        var resultsEl = document.getElementById('adminSearchResults');
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
            resultsEl.innerHTML = data.map(function(item) {
                return '<div class="admin-search-item px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 border-b border-slate-100 last:border-0 flex items-start gap-2" data-lat="' + item.lat + '" data-lng="' + item.lon + '">' +
                    '<i class="bi bi-geo-alt text-blue-400 mt-0.5 flex-shrink-0"></i>' +
                    '<span>' + item.display_name + '</span></div>';
            }).join('');

            resultsEl.querySelectorAll('.admin-search-item').forEach(function(el) {
                el.addEventListener('click', function() {
                    var lat = parseFloat(el.dataset.lat);
                    var lng = parseFloat(el.dataset.lng);
                    updateCoords(lat, lng);
                    map.flyTo([lat, lng], 17, { animate: true, duration: 1 });
                    resultsEl.classList.add('hidden');
                    document.getElementById('adminSearchInput').value = el.querySelector('span').textContent;
                });
            });
        })
        .catch(function() {
            resultsEl.innerHTML = '<div class="px-4 py-3 text-sm text-red-400">Gagal menghubungi layanan pencarian.</div>';
        });
    }

    /* ── TOMBOL LOKASI SAYA ─────────────────────────────────────────────── */
    function adminGoToMyLocation() {
        var btn = document.getElementById('adminMyLocationBtn');
        btn.innerHTML = '<i class="bi bi-arrow-repeat" style="display:inline-block;animation:spin 0.8s linear infinite"></i> <span class="hidden sm:inline">Mencari...</span>';
        btn.disabled = true;

        if (!navigator.geolocation) {
            alert('Browser tidak mendukung geolocation.');
            btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>';
            btn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                var lat = pos.coords.latitude, lng = pos.coords.longitude;
                updateCoords(lat, lng);
                map.flyTo([lat, lng], 18, { animate: true, duration: 1.2 });
                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>';
                btn.disabled = false;
            },
            function() {
                alert('Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.');
                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    /* ── EVENT LISTENERS ────────────────────────────────────────────────── */
    document.getElementById('adminSearchBtn').addEventListener('click', function() {
        adminSearchLocation(document.getElementById('adminSearchInput').value);
    });
    document.getElementById('adminSearchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') adminSearchLocation(e.target.value);
    });
    document.getElementById('adminSearchInput').addEventListener('input', function(e) {
        clearTimeout(adminSearchTimeout);
        if (e.target.value.length >= 3) {
            adminSearchTimeout = setTimeout(function() { adminSearchLocation(e.target.value); }, 600);
        } else {
            document.getElementById('adminSearchResults').classList.add('hidden');
        }
    });
    document.getElementById('adminMyLocationBtn').addEventListener('click', adminGoToMyLocation);

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#adminSearchInput') && !e.target.closest('#adminSearchResults') && !e.target.closest('#adminSearchBtn')) {
            document.getElementById('adminSearchResults').classList.add('hidden');
        }
    });

})();
</script>

<style>
    /* Tambahan style khusus halaman ini */
    #officeMap { z-index: 0; }

    .leaflet-container { font-family: 'Inter', sans-serif; }

    /* Toolbar Leaflet lebih bersih */
    .leaflet-control-zoom a {
        font-size: 16px !important;
        border-radius: 8px !important;
    }

    /* Input readonly */
    input[readonly] {
        background-color: #f8fafc !important;
        color: #475569 !important;
    }

    /* Smooth flash dismiss */
    #flashSuccess { transition: opacity .3s; }

    /* Animasi spin untuk tombol Lokasi Saya */
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\StaffLog.adl\StaffLog.adl\resources\views/admin/pengaturan-kantor.blade.php ENDPATH**/ ?>