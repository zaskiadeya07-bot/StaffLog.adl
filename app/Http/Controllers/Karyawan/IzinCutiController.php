<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IzinCutiController extends Controller
{
    public function index()
    {
        if (!session()->has('pengguna_id')) {
            return redirect()->route('login');
        }
        $pengguna = Pengguna::find(session('pengguna_id'));
        return view('karyawan.IzinCuti', compact('pengguna'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'jenis_izin' => 'required|string',
                'tgl_mulai' => 'required|date',
                'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
                'keterangan' => 'required|string|min:5',
            ]);

            if (!session()->has('pengguna_id')) {
                return response()->json(['success' => false, 'message' => 'Sesi login habis'], 401);
            }

            // Konversi jenis_izin dari display ke database
            $jenisDb = $this->konversiJenisIzin($request->jenis_izin);

            // PERBAIKAN: Sesuaikan dengan struktur tabel perizinan
            $perizinan = Perizinan::create([
                'id_pengguna_pengaju' => session('pengguna_id'),  // kolom yang benar
                'id_admin_validator' => null,
                'jenis_izin' => $jenisDb,                         // cuti_tahunan / cuti_sakit / izin
                'tgl_pengajuan' => now()->toDateString(),
                'tgl_mulai' => $request->tgl_mulai,               // kolom yang benar
                'tgl_selesai' => $request->tgl_selesai,           // kolom yang benar
                'keterangan' => $request->keterangan,
                'file_surat' => null,
                'status_approval' => 'pending',
                'catatan_admin' => null,
                'tgl_validasi' => null,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Permohonan berhasil dikirim', 
                'data' => $perizinan
            ]);

        } catch (\Exception $e) {
            Log::error('Error simpan izin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan permohonan.'
            ], 500);
        }
    }

    /**
     * Konversi jenis izin dari display ke database
     */
    private function konversiJenisIzin($jenis)
    {
        switch ($jenis) {
            case 'Cuti Tahunan':
                return 'cuti_tahunan';
            case 'Cuti Sakit':
                return 'cuti_sakit';
            case 'Izin':
                return 'izin';
            default:
                return 'izin';
        }
    }

    public function getData()
    {
        if (!session()->has('pengguna_id')) {
            return response()->json([]);
        }
        
        $perizinan = Perizinan::where('id_pengguna_pengaju', session('pengguna_id'))
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();
        
        return response()->json($perizinan);
    }
}