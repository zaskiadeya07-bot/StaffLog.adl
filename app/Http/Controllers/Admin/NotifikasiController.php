<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
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
     * API untuk mendapatkan data perizinan (JSON)
     */
    public function getData(Request $request)
    {
        $jenis = $request->get('jenis', 'semua');
        
        $query = Perizinan::with(['pengaju', 'pengaju.devisi'])
            ->orderBy('tgl_pengajuan', 'desc');
        
        // PERBAIKAN: filter sesuai nilai enum di database (huruf kecil semua)
        if ($jenis == 'izin') {
            $query->where('jenis_izin', 'izin');  // ← huruf kecil
        } elseif ($jenis == 'sakit') {
            $query->where('jenis_izin', 'cuti_sakit');  // ← sesuai database
        }
        
        $perizinan = $query->get();
        
        $result = [];
        foreach ($perizinan as $item) {
            // Konversi jenis izin ke display
            $jenisDisplay = $this->getJenisDisplay($item->jenis_izin);
            // Konversi status ke display
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
            ];
        }
        
        return response()->json($result);
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
            default:
                return $status;
        }
    }

    /**
     * Update status perizinan (approve/reject)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $perizinan = Perizinan::findOrFail($id);
            
            // Konversi status dari display ke database
            $statusDb = $request->status;
            if ($statusDb == 'Disetujui') $statusDb = 'disetujui';
            if ($statusDb == 'Ditolak') $statusDb = 'ditolak';
            if ($statusDb == 'Menunggu') $statusDb = 'pending';
            
            $perizinan->update([
                'status_approval' => $statusDb,
                'catatan_admin' => $request->catatan,
                'id_admin_validator' => session('pengguna_id'),
                'tgl_validasi' => now(),
            ]);
            
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