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
                        @for($i = 2022; $i <= 2026; $i++)
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($presensi as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-600">
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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                <i class="bi bi-inbox text-5xl block mb-3"></i>
                                <p>Belum ada data kehadiran untuk periode ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#rekapAbsenTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
@endsection
