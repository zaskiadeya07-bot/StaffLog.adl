<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\BulanHelper;
use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Presensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DetailRekapKehadiranController extends Controller
{
    public function __construct(
        protected BulanHelper $bulanHelper
    ) {}

    public function index(Request $request, $id)
    {
        $karyawan = Pengguna::with('devisi')->find($id);

        if (!$karyawan) {
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }

        $bulan = $request->get('b   ulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $bulanNama = $this->bulanHelper->getNamaBulanByAngka((int)$bulan);

        $presensi = Presensi::where('id_pengguna', $id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();
        $statHadir = $presensi->where('status', 'hadir')->count();
        $statTerlambat = $presensi->where('status', 'terlambat')->count();
        $statIzin = $presensi->where('status', 'izin')->count();
        $statAlpha = $presensi->where('status', 'alpha')->count();

        return view('admin.DetailRekapKehadiran', compact(
            'karyawan', 'presensi', 'bulan', 'tahun',
            'bulanNama', 'statHadir', 'statTerlambat',
            'statIzin', 'statAlpha'
        ));
    }

    public function exportPdf(Request $request, $id)
    {
        $karyawan = Pengguna::with('devisi')->find($id);

        if (!$karyawan) {
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $bulanNama = $this->bulanHelper->getNamaBulanByAngka((int)$bulan);

        $presensi = Presensi::where('id_pengguna', $id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $statHadir = $presensi->where('status', 'hadir')->count();
        $statTerlambat = $presensi->where('status', 'terlambat')->count();
        $statIzin = $presensi->where('status', 'izin')->count();
        $statAlpha = $presensi->where('status', 'alpha')->count();

        $pdf = Pdf::loadView('admin.pdf.DetailRekapKehadiranPdf', compact(
            'karyawan', 'presensi', 'bulan', 'tahun',
            'bulanNama', 'statHadir', 'statTerlambat',
            'statIzin', 'statAlpha'
        ));

        $namaFile = 'Rekap_Kehadiran_' . str_replace(' ', '_', $karyawan->nama_lengkap) . '_' . $bulanNama . '_' . $tahun . '.pdf';

        return $pdf->download($namaFile);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:hadir,terlambat,izin,alpha',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $presensi = Presensi::findOrFail($id);

            $presensi->update([
                'status' => $validated['status'],
                'catatan_keterlambatan' => $validated['keterangan'] ?? $presensi->catatan_keterlambatan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal update status presensi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status kehadiran.'
            ], 500);
        }
    }
}
