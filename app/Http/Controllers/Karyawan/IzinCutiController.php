<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\Pengguna;
use App\Models\MasterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IzinCutiController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->session()->has('pengguna_id')) {
            return redirect()->route('login');
        }
        $pengguna = Pengguna::find($request->session()->get('pengguna_id'));
        $setting = MasterData::first();
        $jatahCuti = $setting ? $setting->jatah_cuti_tahunan : 12;
        return view('karyawan.IzinCuti', compact('pengguna', 'jatahCuti'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_izin' => 'required|string',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'required|string|min:5',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        if (!$request->session()->has('pengguna_id')) {
            return response()->json(['success' => false, 'message' => 'Sesi login habis'], 401);
        }

        $penggunaId = $request->session()->get('pengguna_id');
        $hariIni = now()->startOfDay();
        $tglMulai = \Carbon\Carbon::parse($validated['tgl_mulai'])->startOfDay();
        $tglSelesai = \Carbon\Carbon::parse($validated['tgl_selesai'])->startOfDay();
        $durasi = $tglMulai->diffInDays($tglSelesai) + 1;

        // Tidak boleh sebelum hari ini
        if ($tglMulai->lessThan($hariIni)) {
            return response()->json([
                'success' => false,
                'message' => "Tanggal mulai tidak boleh sebelum hari ini."
            ], 422);
        }

        // Cek tumpang tindih tanggal dengan pengajuan lain (pending/disetujui)
        $overlap = Perizinan::where('id_pengguna_pengaju', $penggunaId)
            ->whereIn('status_approval', ['pending', 'disetujui'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('tgl_mulai', [$validated['tgl_mulai'], $validated['tgl_selesai']])
                  ->orWhereBetween('tgl_selesai', [$validated['tgl_mulai'], $validated['tgl_selesai']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('tgl_mulai', '<=', $validated['tgl_mulai'])
                         ->where('tgl_selesai', '>=', $validated['tgl_selesai']);
                  });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal yang diajukan bertumpuk dengan pengajuan izin/cuti lain yang sudah ada.'
            ], 422);
        }

        // Cek sisa cuti untuk semua jenis
        $setting = MasterData::first();
        $jatahCuti = $setting ? $setting->jatah_cuti_tahunan : 12;

        $cutiTerpakai = Perizinan::where('id_pengguna_pengaju', $penggunaId)
            ->where('jenis_izin', 'cuti_tahunan')
            ->where('status_approval', 'disetujui')
            ->get()
            ->sum(function ($item) {
                $start = new \DateTime($item->tgl_mulai);
                $end = new \DateTime($item->tgl_selesai);
                return $start->diff($end)->days + 1;
            });

        $sisaCuti = $jatahCuti - $cutiTerpakai;
        if ($durasi > $sisaCuti) {
            return response()->json([
                'success' => false,
                'message' => "Sisa cuti Anda hanya {$sisaCuti} hari."
            ], 422);
        }

        try {
            $jenisDb = $this->konversiJenisIzin($validated['jenis_izin']);

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

            $perizinan->update([
                'status_approval' => 'dibatalkan',
                'tgl_validasi' => now(),
            ]);

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

    private function konversiJenisIzin($jenis)
    {
        return match ($jenis) {
            'Cuti Tahunan' => 'cuti_tahunan',
            'Cuti Sakit' => 'cuti_sakit',
            'Izin' => 'izin',
            default => 'izin',
        };
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
