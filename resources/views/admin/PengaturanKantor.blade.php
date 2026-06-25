@extends('layouts.AdminLayout')

@section('title', 'Pengaturan Kantor')

@section('content')

{{-- ── PAGE HEADER ─────────────────────────────────────────────────────────── --}}
<x-page-header title="Pengaturan" description="Kelola pengaturan aplikasi secara keseluruhan" />

{{-- ── FLASH MESSAGE ─────────────────────────────────────────────────────────── --}}
@if (session('success'))
    <div id="flashSuccess" data-type="success" data-message="{{ session('success') }}" class="hidden"></div>
@endif
@if (session('error'))
    <div id="flashError" data-type="error" data-message="{{ session('error') }}" class="hidden"></div>
@endif

{{-- ── TAB NAVIGATION ─────────────────────────────────────────────────────── --}}
<div class="mb-6 border-b border-slate-200">
    <div class="flex gap-1 -mb-px">
        <button onclick="switchTab('kantor')" id="tabBtn-kantor" class="tab-btn px-5 py-3 text-sm font-semibold border-b-2 border-slate-800 text-slate-800">Lokasi</button>
        <button onclick="switchTab('divisi')" id="tabBtn-divisi" class="tab-btn px-5 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-600">Divisi</button>
        <button onclick="switchTab('password')" id="tabBtn-password" class="tab-btn px-5 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-600">Kata Sandi</button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1: KANTOR --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-kantor" class="tab-content">
<form action="{{ route('admin.pengaturan-kantor.update') }}" method="POST" id="formPengaturan">
    @csrf
    @method('POST')

    {{-- CARD: MAP ──────────────────────────────────────────────────────────── --}}
    <div class="card mb-5">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="bi bi-map text-blue-500 text-lg"></i>
            <h2 class="font-semibold text-slate-700">Lokasi Kantor di Peta</h2>
            <span class="ml-auto text-xs text-slate-400 hidden sm:block">
                Seret penanda atau klik peta untuk memindahkan posisi kantor
            </span>
        </div>

        {{-- SEARCH BAR --}}
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

        {{-- MAP CONTAINER --}}
        <div id="officeMap" style="height:450px; width:100%; border-radius:0;"></div>

        {{-- MAP HINT (mobile) --}}
        <div class="px-5 py-2 bg-blue-50 border-t border-blue-100 flex items-center gap-2 sm:hidden">
            <i class="bi bi-info-circle text-blue-400 text-sm"></i>
            <p class="text-xs text-blue-600">Seret penanda atau ketuk peta untuk memindahkan posisi kantor.</p>
        </div>
    </div>

    {{-- CARD: FORM FIELDS ──────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="bi bi-sliders text-indigo-500 text-lg"></i>
            <h2 class="font-semibold text-slate-700">Parameter Absensi</h2>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- JAM MASUK STANDAR --}}
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
                        value="{{ old('jam_masuk_std', substr($setting->jam_masuk_std ?? '08:00', 0, 5)) }}"
                        required
                    >
                    <p class="text-xs text-slate-400">Batas waktu karyawan dianggap hadir tepat waktu.</p>
                </div>

                {{-- JAM PULANG STANDAR --}}
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
                        value="{{ old('jam_pulang_std', substr($setting->jam_pulang_std ?? '17:00', 0, 5)) }}"
                        required
                    >
                    <p class="text-xs text-slate-400">Waktu normal karyawan diperbolehkan pulang.</p>
                </div>

                {{-- TOLERANSI KETERLAMBATAN --}}
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
                            value="{{ old('toleransi', $setting->toleransi ?? 15) }}"
                            required
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">
                            menit
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Menit tambahan sebelum absen dihitung terlambat.</p>
                </div>

                {{-- LATITUDE --}}
                <div class="flex flex-col gap-1.5">
                    <label for="latitude"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-geo text-slate-400"></i> Lintang
                        <span class="ml-auto text-xs text-blue-500 font-normal bg-blue-50 px-2 py-0.5 rounded-full">
                            Auto dari peta
                        </span>
                    </label>
                    <input
                        type="number"
                        id="latitude"
                        name="lat_kantor"
                        class="input-field w-full font-mono text-sm"
                        step="any"
                        value="{{ old('lat_kantor', $setting->lat_kantor ?? -6.20876500) }}"
                        required
                    >
                    <p class="text-xs text-slate-400">Bisa diisi manual atau otomatis dari peta.</p>
                </div>

                {{-- LONGITUDE --}}
                <div class="flex flex-col gap-1.5">
                    <label for="longitude"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-geo-alt text-slate-400"></i> Bujur
                        <span class="ml-auto text-xs text-blue-500 font-normal bg-blue-50 px-2 py-0.5 rounded-full">
                            Auto dari peta
                        </span>
                    </label>
                    <input
                        type="number"
                        id="longitude"
                        name="long_kantor"
                        class="input-field w-full font-mono text-sm"
                        step="any"
                        value="{{ old('long_kantor', $setting->long_kantor ?? 106.84559300) }}"
                        required
                    >
                    <p class="text-xs text-slate-400">Bisa diisi manual atau otomatis dari peta.</p>
                </div>

                {{-- JATAH CUTI BULANAN --}}
                <div class="flex flex-col gap-1.5">
                    <label for="jatah_cuti_bulanan"
                           class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                        <i class="bi bi-calendar-month text-slate-400"></i> Jatah Cuti Bulanan
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="jatah_cuti_bulanan"
                            name="jatah_cuti_bulanan"
                            class="input-field w-full pr-14"
                            min="0"
                            step="1"
                            value="{{ old('jatah_cuti_bulanan', $setting->jatah_cuti_bulanan ?? 1) }}"
                            required
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">
                            hari
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Jumlah hari cuti per karyawan per bulan.</p>
                </div>

                {{-- RADIUS --}}
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
                            value="{{ old('radius', $setting->radius ?? 100) }}"
                            required
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">
                            meter
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Jangkauan area di mana karyawan bisa absen.</p>
                </div>

            </div>{{-- /grid --}}

            {{-- RADIUS VISUAL PREVIEW --}}
            <div class="mt-5 flex items-center gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                <div id="radiusPreviewDot"
                     class="w-8 h-8 rounded-full border-2 border-blue-400 bg-blue-100 flex-shrink-0 flex items-center justify-center">
                    <i class="bi bi-building text-blue-500 text-xs"></i>
                </div>
                <div class="text-sm text-indigo-700">
                    Lingkaran biru di peta menunjukkan radius
                    <strong id="radiusPreviewLabel">{{ old('radius', $setting->radius ?? 100) }}</strong>
                    meter dari titik kantor.
                    Karyawan yang berada di dalam lingkaran ini dapat melakukan absensi.
                </div>
            </div>

        </div>{{-- /card body --}}

        {{-- CARD FOOTER: ACTION BUTTONS --}}
        <div class="px-5 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <i class="bi bi-info-circle"></i>
                <span>Pastikan koordinat sudah benar sebelum menyimpan.</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        onclick="resetToDefault()"
                        class="btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Bawaan
                </button>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-floppy"></i> Simpan Pengaturan
                </button>
            </div>
        </div>

    </div>{{-- /card --}}

</form>
</div>{{-- /tab-kantor --}}

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 2: DIVISI --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-divisi" class="tab-content hidden">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-slate-700">Daftar Divisi</h2>
        <button onclick="openModalDivisi(null)" class="btn-primary inline-flex items-center gap-2 text-sm">
            <i class="bi bi-plus-circle"></i> Tambah
        </button>
    </div>

    <div class="card">
        <div class="p-0">
            <table id="divisiTable" class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 w-16">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600">Nama Divisi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($divisis as $d)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-sm text-slate-500">{{ $d->id_devisi }}</td>
                        <td class="px-5 py-3 text-sm font-medium text-slate-800">{{ $d->nama_devisi }}</td>
                        <td class="px-5 py-3 text-right">
                            <button onclick="openModalDivisi({{ $d->id_devisi }}, '{{ $d->nama_devisi }}')"
                                class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button onclick="hapusDivisi({{ $d->id_devisi }}, '{{ $d->nama_devisi }}')"
                                class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition ml-1" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-12 text-center text-slate-400">
                            <i class="bi bi-building text-5xl block mb-3"></i>
                            <p>Belum ada divisi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Divisi --}}
    <div id="modalDivisi" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-3xl max-w-md w-full mx-4">
            <div class="bg-slate-800 p-5 rounded-t-3xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white" id="modalDivisiTitle">Tambah Divisi</h3>
                    <button onclick="tutupModalDivisi()" class="text-slate-400 hover:text-white text-2xl">&times;</button>
                </div>
            </div>
            <form id="formDivisi" method="POST">
                @csrf
                <div class="p-6">
                    <input type="hidden" id="divisiId">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Divisi</label>
                        <input type="text" id="namaDivisiInput" name="nama_devisi"
                            class="input-field w-full" placeholder="Masukkan nama divisi" maxlength="50" required>
                    </div>
                </div>
                <div class="p-5 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" onclick="tutupModalDivisi()" class="btn-secondary px-5">Batal</button>
                    <button type="submit" class="btn-primary px-5" id="btnSimpanDivisi">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>{{-- /tab-divisi --}}

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 3: PASSWORD --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-password" class="tab-content hidden">
    <div class="card max-w-2xl">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="bi bi-shield-lock text-slate-500"></i>
            <h3 class="font-semibold text-slate-700">Ubah Kata Sandi Admin</h3>
        </div>
        <div class="p-5">
            <form action="{{ route('admin.ganti-password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-lock text-slate-400 mr-1"></i> Kata Sandi Saat Ini
                    </label>
                    <div class="relative">
                        <input type="password" name="password_lama" id="pwLama"
                            class="w-full px-4 py-2.5 pr-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                            placeholder="Masukkan kata sandi saat ini">
                        <button type="button" onclick="togglePass('pwLama', 'eyeLama')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="bi bi-eye" id="eyeLama"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-key text-slate-400 mr-1"></i> Kata Sandi Baru
                    </label>
                    <div class="relative">
                        <input type="password" name="password_baru" id="pwBaru"
                            class="w-full px-4 py-2.5 pr-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePass('pwBaru', 'eyeBaru')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="bi bi-eye" id="eyeBaru"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="bi bi-key-fill text-slate-400 mr-1"></i> Konfirmasi Kata Sandi Baru
                    </label>
                    <div class="relative">
                        <input type="password" name="password_baru_confirmation" id="pwKonfirmasi"
                            class="w-full px-4 py-2.5 pr-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                            placeholder="Ulangi kata sandi baru">
                        <button type="button" onclick="togglePass('pwKonfirmasi', 'eyeKonfirmasi')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="bi bi-eye" id="eyeKonfirmasi"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2.5 px-6 rounded-xl text-sm transition flex items-center gap-2">
                    <i class="bi bi-floppy"></i> Simpan Kata Sandi Baru
                </button>
            </form>
        </div>
    </div>
</div>{{-- /tab-password --}}

@endsection

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- LEAFLET SCRIPT                                                            --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@push('scripts')
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

    /* ── 9. EVENT: INPUT MANUAL LAT/LNG ─────────────────────────────────── */
    document.getElementById('latitude').addEventListener('input', function () {
        var lat = parseFloat(this.value);
        var lng = parseFloat(document.getElementById('longitude').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            map.panTo([lat, lng], { animate: true });
        }
    });
    document.getElementById('longitude').addEventListener('input', function () {
        var lat = parseFloat(document.getElementById('latitude').value);
        var lng = parseFloat(this.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            map.panTo([lat, lng], { animate: true });
        }
    });

    /* ── 10. TOAST KOORDINAT ─────────────────────────────────────────────── */
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

    /* ── 10. TAB SWITCHING ───────────────────────────────────────────────── */
    var activeTab = localStorage.getItem('pengaturanTab') || 'kantor';

    window.switchTab = function(tab) {
        document.querySelectorAll('.tab-content').forEach(function(el) { el.classList.add('hidden'); });
        document.getElementById('tab-' + tab).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.classList.remove('border-slate-800', 'text-slate-800');
            btn.classList.add('border-transparent', 'text-slate-400');
        });
        var btn = document.getElementById('tabBtn-' + tab);
        btn.classList.remove('border-transparent', 'text-slate-400');
        btn.classList.add('border-slate-800', 'text-slate-800');
        activeTab = tab;
        localStorage.setItem('pengaturanTab', tab);

        if (tab === 'divisi' && document.getElementById('divisiTable') && !$.fn.DataTable.isDataTable('#divisiTable')) {
            $('#divisiTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                columnDefs: [{ orderable: false, targets: [2] }]
            });
        }
    };

    /* ── 11. DIVISI MODAL ────────────────────────────────────────────────── */
    window.openModalDivisi = function(id, nama) {
        var modal = document.getElementById('modalDivisi');
        var form = document.getElementById('formDivisi');
        var title = document.getElementById('modalDivisiTitle');
        var btnSimpan = document.getElementById('btnSimpanDivisi');
        var idInput = document.getElementById('divisiId');
        var namaInput = document.getElementById('namaDivisiInput');

        if (id) {
            title.textContent = 'Ubah Divisi';
            btnSimpan.innerHTML = '<i class="bi bi-save"></i> Simpan';
            idInput.value = id;
            namaInput.value = nama;
            form.action = '{{ route('admin.divisi.update', ':id') }}'.replace(':id', id);
            var methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
        } else {
            title.textContent = 'Tambah Divisi';
            btnSimpan.innerHTML = '<i class="bi bi-plus-circle"></i> Tambah';
            idInput.value = '';
            namaInput.value = '';
            form.action = '{{ route('admin.divisi.store') }}';
            var methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(function() { namaInput.focus(); }, 100);
    };

    window.tutupModalDivisi = function() {
        document.getElementById('modalDivisi').classList.add('hidden');
        document.getElementById('modalDivisi').classList.remove('flex');
    };

    window.hapusDivisi = function(id, nama) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Divisi?',
            text: 'Divisi "' + nama + '" akan dihapus permanen.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626'
        }).then(function(result) {
            if (result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.divisi.destroy', ':id') }}'.replace(':id', id);
                form.innerHTML = '@csrf @method('DELETE')';
                document.body.appendChild(form);
                form.submit();
            }
        });
    };

    /* ── 12. TOGGLE PASSWORD ─────────────────────────────────────────────── */
    window.togglePass = function(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    };

    /* ── 13. INIT TAB ────────────────────────────────────────────────────── */
    window.switchTab(activeTab);

    /* ── 14. FLASH MESSAGE → SWAL TOAST ──────────────────────────────────── */
    var flashSuccess = document.getElementById('flashSuccess');
    var flashError   = document.getElementById('flashError');

    if (flashSuccess) {
        Swal.fire({
            icon: 'success',
            title: flashSuccess.dataset.message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }
    if (flashError) {
        Swal.fire({
            icon: 'error',
            title: flashError.dataset.message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }

    @if ($errors->any())
    var validationErrors = @json($errors->toArray());
    Object.keys(validationErrors).forEach(function(key) {
        var messages = validationErrors[key];
        Swal.fire({
            icon: 'error',
            title: messages[0],
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
    });
    @endif

    /* ── 11. NILAI AWAL UNTUK DETEKSI PERUBAHAN ─────────────────────────── */
    var ORIGINAL = {
        lat      : parseFloat(document.getElementById('latitude').value)      || DEFAULTS.lat,
        lng      : parseFloat(document.getElementById('longitude').value)     || DEFAULTS.lng,
        radius   : parseFloat(document.getElementById('radius_meter').value)  || DEFAULTS.radius,
        jamMasuk : document.getElementById('jam_masuk_std').value             || '08:00',
        jamPulang: document.getElementById('jam_pulang_std').value            || '17:00',
        toleransi: parseInt(document.getElementById('toleransi_menit').value) || 15
    };

    /* ── 12. RESET TO DEFAULT ───────────────────────────────────────────── */
    window.resetToDefault = function () {
        Swal.fire({
            icon: 'question',
            title: 'Reset ke lokasi bawaan?',
            text: 'Koordinat akan kembali ke lokasi bawaan Jakarta.',
            showCancelButton: true,
            confirmButtonText: 'Ya, reset!',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            document.getElementById('latitude').value      = DEFAULTS.lat.toFixed(8);
            document.getElementById('longitude').value     = DEFAULTS.lng.toFixed(8);
            document.getElementById('radius_meter').value  = DEFAULTS.radius;
            document.getElementById('radiusPreviewLabel').textContent = DEFAULTS.radius;

            updateCoords(DEFAULTS.lat, DEFAULTS.lng);
            circle.setRadius(DEFAULTS.radius);
            map.flyTo([DEFAULTS.lat, DEFAULTS.lng], DEFAULTS.zoom, { animate: true, duration: 1 });

            Swal.fire({
                icon: 'success',
                title: 'Lokasi direset ke bawaan',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
        });
    };

    /* ── 13. VALIDASI & KONFIRMASI FORM ─────────────────────────────────── */
    function getChanges() {
        var latNow = parseFloat(document.getElementById('latitude').value);
        var lngNow = parseFloat(document.getElementById('longitude').value);
        var radNow = parseFloat(document.getElementById('radius_meter').value);
        var jamMasukNow = document.getElementById('jam_masuk_std').value;
        var jamPulangNow = document.getElementById('jam_pulang_std').value;
        var toleransiNow = parseInt(document.getElementById('toleransi_menit').value);

        var changes = [];
        if (latNow !== ORIGINAL.lat || lngNow !== ORIGINAL.lng) {
            changes.push('Lokasi kantor');
        }
        if (radNow !== ORIGINAL.radius) {
            changes.push('Radius: ' + ORIGINAL.radius + 'm &rarr; ' + radNow + 'm');
        }
        if (jamMasukNow !== ORIGINAL.jamMasuk) {
            changes.push('Jam masuk: ' + ORIGINAL.jamMasuk + ' &rarr; ' + jamMasukNow);
        }
        if (jamPulangNow !== ORIGINAL.jamPulang) {
            changes.push('Jam pulang: ' + ORIGINAL.jamPulang + ' &rarr; ' + jamPulangNow);
        }
        if (toleransiNow !== ORIGINAL.toleransi) {
            changes.push('Toleransi: ' + ORIGINAL.toleransi + ' menit &rarr; ' + toleransiNow + ' menit');
        }
        return changes;
    }

    document.getElementById('formPengaturan').addEventListener('submit', function (e) {
        var lat = parseFloat(document.getElementById('latitude').value);
        var lng = parseFloat(document.getElementById('longitude').value);
        var rad = parseFloat(document.getElementById('radius_meter').value);
        var jamMasuk = document.getElementById('jam_masuk_std').value;
        var jamPulang = document.getElementById('jam_pulang_std').value;

        /* ── Validasi dasar ──────────────────────────────────────────── */
        if (isNaN(lat) || isNaN(lng)) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Koordinat belum diatur', text: 'Klik atau drag marker di peta untuk menentukan lokasi kantor.' });
            return;
        }
        if (rad < 10) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Radius terlalu kecil', text: 'Radius minimal adalah 10 meter.' });
            return;
        }

        /* ── Deteksi perubahan ───────────────────────────────────────── */
        var changes = getChanges();
        if (changes.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Tidak ada perubahan',
                text: 'Tidak ada pengaturan yang diubah.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }

        /* ── Validasi: jam pulang sebelum jam masuk ──────────────────── */
        if (jamPulang < jamMasuk) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Jam pulang tidak boleh mendahului jam masuk.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });
            return;
        }

        /* ── Peringatan: radius terlalu kecil ────────────────────────── */
        if (rad < 30) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Radius sangat kecil',
                text: 'Radius ' + rad + ' meter mungkin terlalu kecil. Karyawan mungkin kesulitan melakukan absensi. Tetap simpan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Perbesar'
            }).then(function (result) {
                if (result.isConfirmed) submitWithConfirm(changes);
            });
            return;
        }

        /* ── Peringatan: koordinat berubah drastis ───────────────────── */
        var latDiff = Math.abs(lat - ORIGINAL.lat);
        var lngDiff = Math.abs(lng - ORIGINAL.lng);
        if (latDiff > 0.01 || lngDiff > 0.01) {
            e.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Lokasi kantor berubah jauh?',
                text: 'Koordinat berubah signifikan dari lokasi sebelumnya. Pastikan ini benar.',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Cek lagi'
            }).then(function (result) {
                if (result.isConfirmed) submitWithConfirm(changes);
            });
            return;
        }

        /* ── Konfirmasi perubahan ────────────────────────────────────── */
        e.preventDefault();
        submitWithConfirm(changes);
    });

    function submitWithConfirm(changes) {
        var changesHtml = changes.map(function (c) {
            return '<div style="text-align:left;padding:3px 0;border-bottom:1px solid #f1f5f9">&bull; ' + c + '</div>';
        }).join('');

        Swal.fire({
            icon: 'question',
            title: 'Simpan pengaturan?',
            html: '<div style="text-align:left;margin-bottom:8px;color:#64748b;font-size:14px">Perubahan yang akan disimpan:</div>' + changesHtml,
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1e293b'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            // Loading state
            Swal.fire({
                title: 'Menyimpan...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: function () { Swal.showLoading(); }
            });

            document.getElementById('formPengaturan').submit();
        });
    }

    /* ── 14. FIX: LEAFLET MAP SIZE ─────────────────────────────────────── */
    setTimeout(function () { map.invalidateSize(); }, 300);

    /* ── 15. AUTO DETECT LOKASI SAAT LOAD ──────────────────────────────── */
    setTimeout(function () {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                updateCoords(lat, lng);
                map.flyTo([lat, lng], 17, { animate: true, duration: 1.5 });
                showCoordToast(lat, lng);
            },
            function () {},
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }, 500);

    /* ── 16. SEARCH LOKASI (Nominatim) ─────────────────────────────────── */
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

    /* ── 16. TOMBOL LOKASI SAYA ────────────────────────────────────────── */
    function adminGoToMyLocation() {
        var btn = document.getElementById('adminMyLocationBtn');
        btn.innerHTML = '<i class="bi bi-arrow-repeat" style="display:inline-block;animation:spin 0.8s linear infinite"></i> <span class="hidden sm:inline">Mencari...</span>';
        btn.disabled = true;

        if (!navigator.geolocation) {
            Swal.fire({ icon: 'error', title: 'Browser tidak mendukung geolocation.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
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
                Swal.fire({ icon: 'error', title: 'Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> <span class="hidden sm:inline">Lokasi Saya</span>';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    /* ── 17. EVENT LISTENERS ───────────────────────────────────────────── */
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

    /* Swal toast tampil di atas map */
    .swal2-container { z-index: 99999 !important; }

    /* Animasi spin untuk tombol Lokasi Saya */
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

