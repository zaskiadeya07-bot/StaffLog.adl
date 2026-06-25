<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Karyawan\StoreIzinCutiRequest;
use App\Models\Pengguna;
use App\Models\Perizinan;
use App\Services\PerizinanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IzinCutiController extends Controller
{
    public function __construct(
        protected PerizinanService $perizinanService
    ) {}

    public function index(Request $request)
    {
        if (!$request->session()->has('pengguna_id')) {
            return redirect()->route('login');
        }
        $pengguna = Pengguna::find($request->session()->get('pengguna_id'));
        $setting = \App\Models\MasterData::first();
        $jatahCuti = $setting ? $setting->jatah_cuti_bulanan : 1;
        return view('karyawan.IzinCuti', compact('pengguna', 'jatahCuti'));
    }

    public function store(StoreIzinCutiRequest $request)
    {
        $validated = $request->validated();
        $penggunaId = $request->session()->get('pengguna_id');
        $hariIni = now()->startOfDay();
        $tglMulai = \Carbon\Carbon::parse($validated['tgl_mulai'])->startOfDay();
        $tglSelesai = \Carbon\Carbon::parse($validated['tgl_selesai'])->startOfDay();
        $durasi = $this->perizinanService->hitungDurasi($validated['tgl_mulai'], $validated['tgl_selesai']);

        if ($tglMulai->lessThan($hariIni)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai tidak boleh sebelum hari ini.'
            ], 422);
        }

        if ($this->perizinanService->cekOverlap($penggunaId, $validated['tgl_mulai'], $validated['tgl_selesai'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal yang diajukan bertumpuk dengan pengajuan izin/cuti lain yang sudah ada.'
            ], 422);
        }

        $jenisDb = $this->perizinanService->konversiJenisIzin($validated['jenis_izin']);

        if ($jenisDb === 'cuti') {
            $sisaCuti = $this->perizinanService->hitungSisaCuti($penggunaId, now()->month, now()->year);
            if ($durasi > $sisaCuti) {
                return response()->json([
                    'success' => false,
                    'message' => "Sisa cuti Anda hanya {$sisaCuti} hari."
                ], 422);
            }
        }

        try {

            $filePath = null;
            if ($request->hasFile('file_surat')) {
                $filePath = $request->file('file_surat')->store('perizinan', 'public');
            }

            $perizinan = Perizinan::create([
                'id_pengguna_pengaju' => $penggunaId,
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

    public function cancel(Request $request, $id)
    {
        try {
            $penggunaId = $request->session()->get('pengguna_id');
            if (!$penggunaId) {
                return response()->json(['success' => false, 'message' => 'Sesi login habis'], 401);
            }

            $perizinan = Perizinan::where('id_izin', $id)
                ->where('id_pengguna_pengaju', $penggunaId)
                ->where('status_approval', 'pending')
                ->firstOrFail();

            if ($perizinan->file_surat) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($perizinan->file_surat);
            }

            $perizinan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil dibatalkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error batalkan izin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan permohonan.'
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        if (!$request->session()->has('pengguna_id')) {
            return response()->json([]);
        }

        $query = Perizinan::where('id_pengguna_pengaju', $request->session()->get('pengguna_id'));

        if ($request->bulan && $request->tahun) {
            $query->whereMonth('tgl_pengajuan', $request->bulan)
                  ->whereYear('tgl_pengajuan', $request->tahun);
        }

        $perizinan = $query->orderBy('tgl_pengajuan', 'desc')->get();

        return response()->json($perizinan);
    }
}
