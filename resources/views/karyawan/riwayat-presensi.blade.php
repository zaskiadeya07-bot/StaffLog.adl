@extends('layouts.dashboard', [
    'role' => 'karyawan',
    'userName' => 'Dina Andini',
    'pageTitle' => 'Riwayat Presensi',
])

@php
    $summaryCards = [
        ['label' => 'Masuk', 'value' => 18, 'accent' => 'blue'],
        ['label' => 'Pulang', 'value' => 17, 'accent' => 'green'],
        ['label' => 'Perizinan', 'value' => 2, 'accent' => 'amber'],
    ];

    $historyRows = [
        ['date' => '01 Apr 2026', 'check_in' => '08:08', 'check_out' => '17:12', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '02 Apr 2026', 'check_in' => '08:12', 'check_out' => '17:03', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '03 Apr 2026', 'check_in' => '08:05', 'check_out' => '17:10', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '04 Apr 2026', 'check_in' => '-', 'check_out' => '-', 'status' => 'Izin', 'note' => 'Acara keluarga'],
        ['date' => '05 Apr 2026', 'check_in' => '08:20', 'check_out' => '16:58', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '06 Apr 2026', 'check_in' => '08:03', 'check_out' => '17:05', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '07 Apr 2026', 'check_in' => '08:17', 'check_out' => '17:14', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '08 Apr 2026', 'check_in' => '-', 'check_out' => '-', 'status' => 'Sakit', 'note' => 'Demam'],
        ['date' => '09 Apr 2026', 'check_in' => '08:09', 'check_out' => '17:08', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '10 Apr 2026', 'check_in' => '08:13', 'check_out' => '17:11', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '11 Apr 2026', 'check_in' => '08:04', 'check_out' => '17:02', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '12 Apr 2026', 'check_in' => '-', 'check_out' => '-', 'status' => 'Izin', 'note' => 'Keperluan administrasi'],
        ['date' => '13 Apr 2026', 'check_in' => '08:15', 'check_out' => '17:00', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '14 Apr 2026', 'check_in' => '08:11', 'check_out' => '17:16', 'status' => 'Hadir', 'note' => '-'],
        ['date' => '15 Apr 2026', 'check_in' => '08:07', 'check_out' => '17:04', 'status' => 'Hadir', 'note' => '-'],
    ];

    $monthlySummary = [
        ['key' => 'H', 'value' => 18, 'description' => 'Hadir'],
        ['key' => 'S', 'value' => 1, 'description' => 'Sakit'],
        ['key' => 'I', 'value' => 2, 'description' => 'Izin'],
        ['key' => 'A', 'value' => 0, 'description' => 'Alpha'],
    ];

    $monthlyTotal = collect($monthlySummary)->sum('value');
@endphp

@section('content')
    <section class="space-y-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Riwayat Presensi</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan dan histori kehadiran bulan ini.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($summaryCards as $summaryCard)
                @include('partials.presensi.summary-card', [
                    'label' => $summaryCard['label'],
                    'value' => $summaryCard['value'],
                    'accent' => $summaryCard['accent'],
                ])
            @endforeach
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 text-left text-sm font-bold text-slate-700">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Check-In</th>
                            <th class="px-4 py-3">Check-Out</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @foreach ($historyRows as $historyRow)
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $historyRow['date'] }}</td>
                                <td class="px-4 py-3">{{ $historyRow['check_in'] }}</td>
                                <td class="px-4 py-3">{{ $historyRow['check_out'] }}</td>
                                <td class="px-4 py-3">
                                    @include('partials.presensi.status-badge', ['status' => $historyRow['status']])
                                </td>
                                <td class="px-4 py-3">{{ $historyRow['note'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-sm">
            <div class="bg-linear-to-r from-blue-700 to-blue-600 p-5 text-white">
                <h2 class="text-lg font-extrabold">Summary Bulanan</h2>
                <p class="mt-1 text-sm text-blue-100">Distribusi status presensi bulan ini • Total {{ $monthlyTotal }} hari tercatat</p>
            </div>

            <div class="grid gap-3 p-5 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($monthlySummary as $summaryItem)
                    @include('partials.presensi.monthly-summary-item', [
                        'key' => $summaryItem['key'],
                        'value' => $summaryItem['value'],
                        'description' => $summaryItem['description'],
                        'total' => $monthlyTotal,
                    ])
                @endforeach
            </div>
        </div>
    </section>
@endsection
