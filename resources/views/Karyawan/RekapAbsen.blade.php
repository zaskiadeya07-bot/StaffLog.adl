@extends('layouts.KaryawanLayout')

@section('title', 'Rekap Absen')

@section('content')
<div>
    <x-page-header title="Rekap Absen" description="Riwayat kehadiran Anda periode {{ $bulanNama }} {{ $tahun }}" />

    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-emerald-50 rounded-xl p-4 text-center">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="bi bi-check-circle text-emerald-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $statHadir }}</p>
            <p class="text-xs text-slate-500">Hadir</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-4 text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="bi bi-clock text-amber-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $statTerlambat }}</p>
            <p class="text-xs text-slate-500">Terlambat</p>
        </div>
        <div class="bg-slate-800 rounded-xl p-4 text-center">
            <div class="w-12 h-12 bg-slate-700 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="bi bi-file-text text-slate-300 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-white">{{ $statIzin }}</p>
            <p class="text-xs text-slate-300">Izin</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4 text-center">
            <div class="w-12 h-12 bg-slate-200 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="bi bi-x-circle text-slate-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-slate-600">{{ $statAlpha }}</p>
            <p class="text-xs text-slate-500">Alpha</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('karyawan.rekap-absen') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Bulan</label>
                    <select name="bulan" class="input-field">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Tahun</label>
                    <select name="tahun" class="input-field">
                        @for($i = date('Y') - 5; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
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
                <table id="rekapAbsenTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Hari</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jam Keluar</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Keterlambatan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($presensi as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600" data-order="{{ $p->tanggal }}">
                                {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">
                                {{ $p->check_in ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 font-mono">
                                {{ $p->check_out ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($p->status == 'hadir')
                                    <x-badge type="hadir"><i class="bi bi-check-circle"></i> Hadir</x-badge>
                                @elseif($p->status == 'terlambat')
                                    <x-badge type="terlambat"><i class="bi bi-clock-history"></i> Terlambat</x-badge>
                                @elseif($p->status == 'izin')
                                    <x-badge type="izin"><i class="bi bi-pencil-square"></i> Izin</x-badge>
                                @else
                                    <x-badge type="alpha"><i class="bi bi-x-circle"></i> Alpha</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ ($p->menit_terlambat ?? 0) > 0 ? $p->menit_terlambat . ' menit' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                    class="btn-detail bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition text-sm font-medium inline-flex items-center gap-1"
                                    title="Detail Absensi"
                                    data-id="{{ $p->id_presensi }}">
                                    <i class="bi bi-eye"></i> 
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Kehadiran -->
<div id="detailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 max-h-[85vh] overflow-y-auto shadow-2xl">
        <div class="sticky top-0 z-10 bg-gradient-to-r from-slate-800 to-slate-700 p-5 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-calendar-check text-amber-400"></i>
                Detail Kehadiran
            </h3>
            <button onclick="closeDetailModal()" class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center text-white/60 hover:text-white transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        <div class="p-5" id="detailModalBody"></div>
    </div>
</div>

@push('scripts')
<script>
    const rekapDetailData = {
        @forelse($presensi as $p)
        {{ $p->id_presensi }}: Object.assign(@json($p->toArray()), {
            _tgl: '{{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}',
            _hari: '{{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd') }}',
        }),
        @empty
        @endforelse
    };

    const rekapIzinData = {
        @forelse($presensi as $p)
        @if($p->perizinan)
        {{ $p->id_presensi }}: Object.assign(@json($p->perizinan->toArray()), {
            _tglMulai: '{{ \Carbon\Carbon::parse($p->perizinan->tgl_mulai)->locale('id')->isoFormat('D MMMM YYYY') }}',
            _tglSelesai: '{{ \Carbon\Carbon::parse($p->perizinan->tgl_selesai)->locale('id')->isoFormat('D MMMM YYYY') }}',
        }),
        @endif
        @empty
        @endforelse
    };

    function openDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function statusBadgeHTML(status) {
        const map = {
            hadir:     '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700"><i class="bi bi-check-circle mr-1"></i> Hadir</span>',
            terlambat: '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 text-amber-700"><i class="bi bi-clock-history mr-1"></i> Terlambat</span>',
            izin:      '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-blue-100 text-blue-700"><i class="bi bi-pencil-square mr-1"></i> Izin</span>',
            alpha:     '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-600"><i class="bi bi-x-circle mr-1"></i> Alpha</span>',
        };
        return map[status] || status;
    }

    function izinBadgeHTML(status) {
        const map = {
            pending:    '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>',
            disetujui:  '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Disetujui</span>',
            ditolak:    '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-red-100 text-red-700">Ditolak</span>',
            dibatalkan: '<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">Dibatalkan</span>',
        };
        return map[status] || status;
    }

    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        const d = rekapDetailData[id];
        const izin = rekapIzinData[id] || null;
        if (!d) return;

        const tgl = d._tgl || '-';
        const hari = d._hari || '-';

        const checkIn = d.check_in || '<span class="text-slate-300">—</span>';
        const checkOut = d.check_out || '<span class="text-slate-300">—</span>';

        let izinSection = '';
        if (d.status === 'izin' && izin) {
            const tglMulai = izin._tglMulai || '-';
            const tglSelesai = izin._tglSelesai || '-';
            izinSection = `
                <div class="border-t border-slate-200 pt-5 mt-3">
                    <h4 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="bi bi-file-text text-blue-600"></i> Detail Perizinan
                    </h4>
                    <div class="bg-blue-50 rounded-2xl p-5 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-tag text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-500">Jenis Izin</p>
                                <p class="font-semibold text-blue-900">${izin.jenis_izin || '-'}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-calendar-range text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-500">Periode</p>
                                <p class="font-semibold text-blue-900">${tglMulai} — ${tglSelesai}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-chat-dots text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-500">Keterangan</p>
                                <p class="text-sm text-blue-900">${izin.keterangan || '-'}</p>
                            </div>
                        </div>
                        ${izin.catatan_admin ? `
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-journal-text text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-500">Catatan Admin</p>
                                <p class="text-sm text-blue-900">${izin.catatan_admin}</p>
                            </div>
                        </div>` : ''}
                        <div class="pt-2 border-t border-blue-200">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-blue-500">Status Pengajuan</span>
                                ${izinBadgeHTML(izin.status_approval)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        const html = `
            <!-- Header Info -->
            <div class="text-center mb-5">
                <p class="text-2xl font-bold text-slate-800">${tgl}</p>
                <p class="text-sm text-slate-500">${hari}</p>
                <div class="mt-2">${statusBadgeHTML(d.status)}</div>
            </div>

            <!-- Jam Masuk & Keluar -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-emerald-50 rounded-2xl p-4 text-center border border-emerald-200">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="bi bi-box-arrow-in-right text-emerald-600 text-lg"></i>
                    </div>
                    <p class="text-xs text-emerald-600 font-medium mb-1">Jam Masuk</p>
                    <p class="text-xl font-bold text-emerald-700 font-mono">${checkIn}</p>
                </div>
                <div class="bg-red-50 rounded-2xl p-4 text-center border border-red-200">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="bi bi-box-arrow-right text-red-600 text-lg"></i>
                    </div>
                    <p class="text-xs text-red-600 font-medium mb-1">Jam Keluar</p>
                    <p class="text-xl font-bold text-red-700 font-mono">${checkOut}</p>
                </div>
            </div>

            <!-- Info Detail -->
            <div class="bg-slate-50 rounded-2xl p-5 space-y-4">
                ${(d.menit_terlambat && d.menit_terlambat > 0) ? `
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-clock-history text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Keterlambatan</p>
                        <p class="font-semibold text-slate-800">${d.menit_terlambat} menit${d.catatan_keterlambatan ? ' — ' + d.catatan_keterlambatan : ''}</p>
                    </div>
                </div>
                <hr class="border-slate-200">` : ''}

                ${(d.check_in_lat && d.check_in_lng) ? `
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-slate-200 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-geo-alt text-slate-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Lokasi Check-in</p>
                        <p class="text-sm font-mono text-slate-700">${d.check_in_lat}, ${d.check_in_lng}</p>
                    </div>
                </div>` : ''}

                ${(d.check_out_lat && d.check_out_lng) ? `
                <hr class="border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-slate-200 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-geo-alt text-slate-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Lokasi Check-out</p>
                        <p class="text-sm font-mono text-slate-700">${d.check_out_lat}, ${d.check_out_lng}</p>
                    </div>
                </div>` : ''}
            </div>

            ${izinSection}
        `;

        $('#detailModalBody').html(html);
        openDetailModal();
    });

    $(document).on('click', function(e) {
        if ($(e.target).closest('#detailModal .bg-white').length === 0 && !$(e.target).closest('.btn-detail').length) {
            const modal = document.getElementById('detailModal');
            if (!modal.classList.contains('hidden')) {
                closeDetailModal();
            }
        }
    });

    $(document).ready(function() {
        $('#rekapAbsenTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                emptyTable: 'Belum ada data kehadiran untuk periode ini'
            },
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: 6 }
            ],
            drawCallback: function() {
                $('.dataTables_empty').addClass('px-4 py-12 text-center text-slate-500');
            }
        });
    });
</script>
@endpush
@endsection
