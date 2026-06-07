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
                'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            if (!session()->has('pengguna_id')) {
                return response()->json(['success' => false, 'message' => 'Sesi login habis'], 401);
            }

            $jenisDb = $this->konversiJenisIzin($validated['jenis_izin']);

            $filePath = null;
            if ($request->hasFile('file_surat')) {
                $filePath = $request->file('file_surat')->store('perizinan', 'public');
            }

            $perizinan = Perizinan::create([
                'id_pengguna_pengaju' => session('pengguna_id'),
                'id_admin_validator' => null,
                'jenis_izin' => $jenisDb,
                'tgl_pengajuan' => now()->toDateString(),
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                'keterangan' => $validated['keterangan'],
                'file_surat' => $filePath,
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

    private function konversiJenisIzin($jenis)
    {
        return match ($jenis) {
            'Cuti Tahunan' => 'cuti_tahunan',
            'Cuti Sakit' => 'cuti_sakit',
            'Izin' => 'izin',
            default => 'izin',
        };
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
