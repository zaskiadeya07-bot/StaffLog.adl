<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\MasterData;
use App\Models\Pengguna;
use App\Models\Presensi;
use App\Services\PresensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AbsenMasuk extends Controller
{
    public function __construct(
        protected PresensiService $presensiService
    ) {}

    public function index(Request $request)
    {
        if (!$request->session()->has('pengguna_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $setting = MasterData::first();
        $pengguna = Pengguna::find($request->session()->get('pengguna_id'));

        if (!$pengguna) {
            $request->session()->flush();
            return redirect()->route('login')->with('error', 'Akun tidak ditemukan.');
        }

        return view('karyawan.CheckIn', compact('setting', 'pengguna'));
    }

    public function status(Request $request)
    {
        try {
            if (!$request->session()->has('pengguna_id')) {
                return response()->json(['sudah_check_in' => false, 'error' => 'Session tidak valid'], 401);
            }

            return response()->json(
                $this->presensiService->statusCheckIn($request->session()->get('pengguna_id'))
            );
        } catch (\Exception $e) {
            Log::error('Error cek status check in: ' . $e->getMessage());
            return response()->json(['sudah_check_in' => false, 'error' => 'Terjadi kesalahan'], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'jam_masuk' => 'nullable|string'
            ]);

            if (!$request->session()->has('pengguna_id')) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Sesi login Anda telah berakhir.'], 401);
            }

            $penggunaId = $request->session()->get('pengguna_id');
            $pengguna = Pengguna::find($penggunaId);

            if (!$pengguna) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Data pengguna tidak ditemukan.'], 404);
            }

            $existingPresensi = $this->presensiService->cekSudahCheckIn($penggunaId);

            if ($existingPresensi) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan check in hari ini!'], 400);
            }

            $setting = MasterData::first();

            if ($setting) {
                $radiusCheck = $this->presensiService->checkRadius(
                    (float)$request->latitude,
                    (float)$request->longitude,
                    $setting
                );

                if (!$radiusCheck['di_dalam_radius']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar radius kantor! Jarak: ' . round($radiusCheck['jarak']) . ' meter'
                    ], 400);
                }
            }

            $jamMasukRaw = $request->jam_masuk ?? now()->format('H:i:s');
            $jamMasuk = str_replace('.', ':', $jamMasukRaw);

            $menitTerlambat = 0;
            if ($setting && $jamMasuk) {
                $menitTerlambat = $this->presensiService->hitungMenitTerlambat($jamMasuk, $setting);
            }

            $presensi = Presensi::create([
                'id_pengguna' => $penggunaId,
                'tanggal' => today()->toDateString(),
                'check_in' => $jamMasuk,
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'status' => $menitTerlambat > 0 ? 'terlambat' : 'hadir',
                'menit_terlambat' => round($menitTerlambat, 1),
                'catatan_keterlambatan' => $menitTerlambat > 0 ? "Terlambat " . round($menitTerlambat, 1) . " menit" : null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Selamat Bekerja!',
                'data' => [
                    'check_in' => $presensi->check_in,
                    'jarak' => round($radiusCheck['jarak'] ?? 0),
                    'menit_terlambat' => round($menitTerlambat, 1)
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validasi gagal: ' . implode(', ', $e->errors())], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error check in: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat check in.'], 500);
        }
    }
}
