<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kehadiran - {{ $karyawan->nama_lengkap }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2563eb;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 800;
            color: #1e40af;
            margin: 0 0 4px;
        }
        .header p {
            font-size: 10px;
            color: #64748b;
            margin: 0;
        }
        .info {
            margin-bottom: 16px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 4px;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info td {
            padding: 2px 6px;
            font-size: 10px;
        }
        .info .label {
            font-weight: 700;
            color: #475569;
            width: 100px;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 8px;
        }
        .stats .stat-box {
            flex: 1;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
        }
        .stat-box h3 {
            font-size: 14px;
            font-weight: 800;
            margin: 0;
        }
        .stat-box p {
            font-size: 8px;
            margin: 2px 0 0;
        }
        .bg-hadir { background: #d1fae5; color: #065f46; }
        .bg-terlambat { background: #fef3c7; color: #92400e; }
        .bg-izin { background: #dbeafe; color: #1e40af; }
        .bg-alpha { background: #f1f5f9; color: #475569; }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data thead th {
            background: #eff6ff;
            color: #1e40af;
            font-weight: 700;
            font-size: 8px;
            text-align: left;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
        }
        table.data tbody td {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            font-size: 8px;
        }
        table.data tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7px;
            font-weight: 700;
            border-radius: 8px;
        }
        .badge-hadir { background: #d1fae5; color: #065f46; }
        .badge-terlambat { background: #fef3c7; color: #92400e; }
        .badge-izin { background: #dbeafe; color: #1e40af; }
        .badge-alpha { background: #f1f5f9; color: #64748b; }
        .footer {
            text-align: center;
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 7px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>StaffLog.adl</h1>
        <p>Rekap Kehadiran Karyawan</p>
    </div>

    <div class="info">
        <table>
            <tr><td class="label">Nama</td><td>: {{ $karyawan->nama_lengkap ?? '-' }}</td></tr>
            <tr><td class="label">Divisi</td><td>: {{ $karyawan->devisi->nama_devisi ?? '-' }}</td></tr>
            <tr><td class="label">Periode</td><td>: {{ $bulanNama ?? '' }} {{ $tahun ?? '' }}</td></tr>
        </table>
    </div>

    <div class="stats">
        <div class="stat-box bg-hadir">
            <h3>{{ $statHadir ?? 0 }}</h3>
            <p>Hadir</p>
        </div>
        <div class="stat-box bg-terlambat">
            <h3>{{ $statTerlambat ?? 0 }}</h3>
            <p>Terlambat</p>
        </div>
        <div class="stat-box bg-izin">
            <h3>{{ $statIzin ?? 0 }}</h3>
            <p>Izin</p>
        </div>
        <div class="stat-box bg-alpha">
            <h3>{{ $statAlpha ?? 0 }}</h3>
            <p>Alpha</p>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Status</th>
                <th>Keterlambatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($presensi as $p)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                    <td>{{ $p->check_in ?? '-' }}</td>
                    <td>{{ $p->check_out ?? '-' }}</td>
                    <td>
                        @php $status = $p->status ?? 'alpha'; @endphp
                        @if ($status == 'hadir')
                            <span class="badge badge-hadir">Hadir</span>
                        @elseif ($status == 'terlambat')
                            <span class="badge badge-terlambat">Terlambat</span>
                        @elseif ($status == 'izin')
                            <span class="badge badge-izin">Izin</span>
                        @else
                            <span class="badge badge-alpha">Alpha</span>
                        @endif
                    </td>
                    <td>{{ ($p->menit_terlambat ?? 0) > 0 ? ($p->menit_terlambat ?? 0) . ' menit' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:16px;color:#94a3b8;">Belum ada data kehadiran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} &bull; StaffLog.adl
    </div>
</body>
</html>
