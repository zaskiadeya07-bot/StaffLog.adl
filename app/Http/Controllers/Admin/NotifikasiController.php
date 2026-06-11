<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\Presensi;
use App\Models\MasterData;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifikasiController extends Controller
{
    /**
     * Tampilkan halaman notifikasi perizinan untuk admin
     */
    public function index()
    {
        return view('admin.Notifikasi');
    }

    /**
     * API untuk mendapatkan data perizinan (JSON) dengan pagination
     */
    public function getData(Request $request)
    {
        $jenis = $request->get('jenis', 'semua');
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $query = Perizinan::with(['pengaju', 'pengaju.devisi'])
            ->where('status_approval', 'pending')  // hanya yang pending
            ->orderBy('tgl_pengajuan', 'desc');

        if ($jenis == 'izin') {
            $query->where('jenis_izin', 'izin');
        } elseif ($jenis == 'sakit') {
            $query->where('jenis_izin', 'cuti_sakit');
        } elseif ($jenis == 'cuti_tahunan') {
            $query->where('jenis_izin', 'cuti_tahunan');
        }

        $perizinan = $query->paginate($perPage, ['*'], 'page', $page);

        $result = [];
        foreach ($perizinan->items() as $item) {
            $jenisDisplay = $this->getJenisDisplay($item->jenis_izin);
            $statusDisplay = $this->getStatusDisplay($item->status_approval);

            $result[] = [
                'id' => $item->id_izin,
                'tanggal' => $item->tgl_pengajuan,
                'nama' => $item->pengaju->nama_lengkap ?? 'Tidak Diketahui',
                'divisi' => $item->pengaju->devisi->nama_devisi ?? '-',
                'jenis' => $jenisDisplay,
                'alasan' => $item->keterangan,
                'durasi' => $this->hitungDurasi($item->tgl_mulai, $item->tgl_selesai),
                'status' => $statusDisplay,
                'status_original' => $item->status_approval,
                'tgl_mulai' => $item->tgl_mulai,
                'tgl_selesai' => $item->tgl_selesai,
                'keterangan' => $item->keterangan,
                'catatan_admin' => $item->catatan_admin,
                'file_surat' => $item->file_surat,
            ];
        }

        return response()->json([
            'data' => $result,
            'total' => $perizinan->total(),
            'per_page' => $perizinan->perPage(),
            'current_page' => $perizinan->currentPage(),
            'last_page' => $perizinan->lastPage(),
        ]);
    }

    /**
     * Konversi jenis izin dari database ke tampilan
     */
    private function getJenisDisplay($jenis)
    {
        switch ($jenis) {
            case 'cuti_tahunan':
                return 'Cuti Tahunan';
            case 'cuti_sakit':
                return 'Cuti Sakit';
            case 'izin':
                return 'Izin';
            default:
                return $jenis;
        }
    }

    /**
     * Konversi status dari database ke tampilan
     */
    private function getStatusDisplay($status)
    {
        switch ($status) {
            case 'pending':
                return 'Menunggu';
            case 'disetujui':
                return 'Disetujui';
            case 'ditolak':
                return 'Ditolak';
            case 'dibatalkan':
                return 'Dibatalkan';
            default:
                return $status;
        }
    }

    /**
     * Update status perizinan (approve/reject) + auto-create presensi
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $perizinan = Perizinan::findOrFail($id);

            $statusDb = $request->status;
            if ($statusDb == 'Disetujui') $statusDb = 'disetujui';
            if ($statusDb == 'Ditolak') $statusDb = 'ditolak';
            if ($statusDb == 'Menunggu') $statusDb = 'pending';

            $perizinan->update([
                'status_approval' => $statusDb,
                'catatan_admin' => $request->catatan,
                'id_admin_validator' => $request->session()->get('pengguna_id'),
                'tgl_validasi' => now(),
            ]);

            // Auto-create presensi 'izin' jika disetujui
            if ($statusDb === 'disetujui') {
                $this->buatPresensiIzin($perizinan);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status perizinan berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal update status perizinan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status perizinan.'
            ], 500);
        }
    }

    /**
     * Buat record presensi 'izin' untuk setiap tanggal dari tgl_mulai s/d tgl_selesai
     */
    private function buatPresensiIzin($perizinan)
    {
        $pengaturan = MasterData::first();

        $start = new \DateTime($perizinan->tgl_mulai);
        $end = new \DateTime($perizinan->tgl_selesai);
        $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end);

        foreach ($period as $date) {
            $tanggal = $date->format('Y-m-d');

            // Cek apakah sudah ada presensi di tanggal tersebut
            $existing = Presensi::where('id_pengguna', $perizinan->id_pengguna_pengaju)
                ->where('tanggal', $tanggal)
                ->first();

            if (!$existing) {
            Presensi::create([
                'id_pengguna' => $perizinan->id_pengguna_pengaju,
                'id_pengaturan' => $pengaturan ? $pengaturan->id_pengaturan : null,
                'id_izin' => $perizinan->id_izin,
                'tanggal' => $tanggal,
                'status' => 'izin',
            ]);
            }
        }
    }

    private function hitungDurasi($tgl_mulai, $tgl_selesai)
    {
        if (!$tgl_mulai || !$tgl_selesai) return '1 hari';
        $start = new \DateTime($tgl_mulai);
        $end = new \DateTime($tgl_selesai);
        $diff = $start->diff($end);
        $hari = $diff->days + 1;
        return $hari . ' hari';
    }
}
