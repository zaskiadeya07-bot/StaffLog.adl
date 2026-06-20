<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Services\PerizinanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifikasiController extends Controller
{
    public function __construct(
        protected PerizinanService $perizinanService
    ) {}

    public function index()
    {
        return view('admin.Notifikasi');
    }

    public function getData(Request $request)
    {
        $jenis = $request->get('jenis', 'semua');
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $query = Perizinan::with(['pengaju', 'pengaju.devisi'])
            ->where('status_approval', 'pending')
            ->orderBy('tgl_pengajuan', 'desc');

        if (in_array($jenis, ['izin', 'sakit', 'cuti'])) {
            $query->where('jenis_izin', $jenis);
        }

        $perizinan = $query->paginate($perPage, ['*'], 'page', $page);

        $result = [];
        foreach ($perizinan->items() as $item) {
            $result[] = [
                'id' => $item->id_izin,
                'tanggal' => $item->tgl_pengajuan,
                'nama' => $item->pengaju->nama_lengkap ?? 'Tidak Diketahui',
                'divisi' => $item->pengaju->devisi->nama_devisi ?? '-',
                'jenis' => $this->perizinanService->getJenisDisplay($item->jenis_izin),
                'alasan' => $item->keterangan,
                'durasi' => $this->perizinanService->formatDurasi($item->tgl_mulai, $item->tgl_selesai),
                'status' => $this->perizinanService->getStatusDisplay($item->status_approval),
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

            if ($statusDb === 'disetujui') {
                $this->perizinanService->buatPresensiIzin($perizinan);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status perizinan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal update status perizinan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status perizinan.'
            ], 500);
        }
    }
}
