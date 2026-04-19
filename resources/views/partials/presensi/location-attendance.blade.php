@php
    $prefix = $prefix ?? 'attendance';
    $modeTitle = $modeTitle ?? 'Check In';
    $buttonLabel = $buttonLabel ?? 'Konfirmasi';
    $checkInInfo = $checkInInfo ?? null;
@endphp

<div class="space-y-6">
    <div class="rounded-3xl border border-blue-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $modeTitle }}</h1>
                <p class="mt-1 text-sm text-slate-500">Validasi lokasi aktif dengan radius kantor 100 meter.</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-right">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Jam Saat Ini</p>
                <p id="{{ $prefix }}-clock" class="mt-1 text-2xl font-extrabold text-blue-700">--:--:--</p>
                <p id="{{ $prefix }}-date" class="text-sm text-blue-900">-</p>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
            <div id="{{ $prefix }}-map" class="h-[300px] w-full"></div>
        </div>

        <div class="mt-5 grid gap-3 text-sm text-slate-700 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="font-semibold text-slate-900">Status Lokasi</p>
                <p id="{{ $prefix }}-status" class="mt-2 font-semibold text-slate-500">Memeriksa lokasi...</p>
                <p id="{{ $prefix }}-distance" class="mt-1 text-xs text-slate-500">Jarak ke kantor: -</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="font-semibold text-slate-900">Koordinat Karyawan</p>
                <p id="{{ $prefix }}-coords" class="mt-2 text-xs text-slate-500">Belum tersedia</p>
            </div>
        </div>

        @if ($checkInInfo)
            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <p>Check-in tadi: <span id="{{ $prefix }}-checkin-value" class="font-bold">{{ $checkInInfo }}</span></p>
                <p class="mt-1">Durasi kerja: <span id="{{ $prefix }}-duration" class="font-semibold">-</span></p>
            </div>
        @endif

        <button
            id="{{ $prefix }}-submit"
            type="button"
            class="mt-6 inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none"
            disabled
        >
            {{ $buttonLabel }}
        </button>
    </div>

    <div id="{{ $prefix }}-toast" class="fixed bottom-5 right-5 hidden rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-xl">
        Absensi berhasil disimpan.
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            const officeLat = -6.2088;
            const officeLng = 106.8456;
            const allowedRadius = 100;
            const mapElementId = '{{ $prefix }}-map';
            const statusElement = document.getElementById('{{ $prefix }}-status');
            const distanceElement = document.getElementById('{{ $prefix }}-distance');
            const coordsElement = document.getElementById('{{ $prefix }}-coords');
            const submitButton = document.getElementById('{{ $prefix }}-submit');
            const toastElement = document.getElementById('{{ $prefix }}-toast');
            const clockElement = document.getElementById('{{ $prefix }}-clock');
            const dateElement = document.getElementById('{{ $prefix }}-date');
            const durationElement = document.getElementById('{{ $prefix }}-duration');
            const checkInValueElement = document.getElementById('{{ $prefix }}-checkin-value');

            const map = L.map(mapElementId).setView([officeLat, officeLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([officeLat, officeLng]).addTo(map).bindPopup('Lokasi Kantor').openPopup();
            L.circle([officeLat, officeLng], {
                color: '#2563eb',
                fillColor: '#93c5fd',
                fillOpacity: 0.2,
                radius: allowedRadius,
            }).addTo(map);

            let employeeMarker = null;
            const checkInInfo = @json($checkInInfo);

            const calculateDistance = function (lat1, lng1, lat2, lng2) {
                const earthRadius = 6371000;
                const toRadians = function (value) {
                    return value * (Math.PI / 180);
                };

                const latDelta = toRadians(lat2 - lat1);
                const lngDelta = toRadians(lng2 - lng1);
                const segment =
                    Math.sin(latDelta / 2) * Math.sin(latDelta / 2) +
                    Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
                    Math.sin(lngDelta / 2) * Math.sin(lngDelta / 2);

                return earthRadius * (2 * Math.atan2(Math.sqrt(segment), Math.sqrt(1 - segment)));
            };

            const updateDateTime = function () {
                const now = new Date();
                clockElement.textContent = now.toLocaleTimeString('id-ID');
                dateElement.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                });

                if (durationElement && checkInValueElement && checkInInfo) {
                    const timeParts = checkInInfo.split(':');
                    if (timeParts.length === 2) {
                        const checkInDate = new Date();
                        checkInDate.setHours(Number(timeParts[0]), Number(timeParts[1]), 0, 0);
                        const differenceMs = now.getTime() - checkInDate.getTime();
                        if (differenceMs > 0) {
                            const totalMinutes = Math.floor(differenceMs / 60000);
                            const hours = Math.floor(totalMinutes / 60);
                            const minutes = totalMinutes % 60;
                            durationElement.textContent = hours + ' jam ' + minutes + ' menit';
                        }
                    }
                }
            };

            const evaluateLocation = function (latitude, longitude) {
                const distance = calculateDistance(latitude, longitude, officeLat, officeLng);
                const inRange = distance <= allowedRadius;

                if (!employeeMarker) {
                    employeeMarker = L.marker([latitude, longitude]).addTo(map).bindPopup('Lokasi Anda');
                } else {
                    employeeMarker.setLatLng([latitude, longitude]);
                }

                map.panTo([latitude, longitude]);
                coordsElement.textContent = latitude.toFixed(5) + ', ' + longitude.toFixed(5);
                distanceElement.textContent = 'Jarak ke kantor: ' + Math.round(distance) + ' meter';

                if (inRange) {
                    statusElement.textContent = 'Dalam jangkauan ✓';
                    statusElement.className = 'mt-2 font-semibold text-emerald-600';
                    submitButton.disabled = false;
                } else {
                    statusElement.textContent = 'Di luar jangkauan ✗';
                    statusElement.className = 'mt-2 font-semibold text-rose-600';
                    submitButton.disabled = true;
                }
            };

            const handleGeolocationError = function (error) {
                statusElement.textContent = 'Tidak bisa mengambil lokasi.';
                statusElement.className = 'mt-2 font-semibold text-rose-600';
                distanceElement.textContent = 'Error: ' + error.message;
                submitButton.disabled = true;
            };

            if (!navigator.geolocation) {
                statusElement.textContent = 'Browser tidak mendukung geolocation.';
                statusElement.className = 'mt-2 font-semibold text-rose-600';
                submitButton.disabled = true;
            } else {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        evaluateLocation(position.coords.latitude, position.coords.longitude);
                    },
                    function (error) {
                        handleGeolocationError(error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0,
                    }
                );
            }

            submitButton.addEventListener('click', function () {
                const now = new Date();
                toastElement.textContent = '{{ $modeTitle }} berhasil pada ' + now.toLocaleTimeString('id-ID');
                toastElement.classList.remove('hidden');
                setTimeout(function () {
                    toastElement.classList.add('hidden');
                }, 2500);
            });

            updateDateTime();
            setInterval(updateDateTime, 1000);
        })();
    </script>
@endpush
