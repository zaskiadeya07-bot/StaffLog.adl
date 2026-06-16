<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Karyawan\AbsenKeluarRequest;
use App\Models\MasterData;
use App\Models\Pengguna;
use App\Models\Presensi;
use App\Services\PresensiService;
use Psr\Log\LoggerInterface;

class AbsenKeluar extends Controller
{
    public function __construct(
        protected PresensiService $presensiService,
        protected LoggerInterface $logger
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        if (!$request->session()->has('pengguna_id')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $setting = MasterData::first();
        $pengguna = Pengguna::find($request->session()->get('pengguna_id'));

        if (!$pengguna) {
            $request->session()->flush();
            return redirect()->route('login')
                ->with('error', 'Akun tidak ditemukan. Silakan login kembali.');
        }

        return view('karyawan.Absen', ['mode' => 'pulang', 'setting' => $setting, 'pengguna' => $pengguna]);
    }

    public function status(\Illuminate\Http\Request $request)
    {
        try {
            if (!$request->session()->has('pengguna_id')) {
                return response()->json([
                    'hasCheckedOut' => false,
                    'error' => 'Sesi tidak valid'
                ], 401);
            }

            return response()->json(
                $this->presensiService->statusCheckOut($request->session()->get('pengguna_id'))
            );
        } catch (\Exception $e) {
            $this->logger->error('Error cek status check out: ' . $e->getMessage());

            return response()->json([
                'hasCheckedOut' => false,
                'error' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function store(AbsenKeluarRequest $request)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $validated = $request->validated();

            if (!$request->session()->has('pengguna_id')) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda telah berakhir. Silakan login kembali.',
                    'code' => 'SESSION_EXPIRED'
                ], 401);
            }

            $penggunaId = $request->session()->get('pengguna_id');

            $pengguna = Pengguna::find($penggunaId);

            if (!$pengguna) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data pengguna tidak ditemukan. Silakan hubungi administrator.',
                    'code' => 'USER_NOT_FOUND'
                ], 404);
            }

            $presensi = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();

            if (!$presensi) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum absen masuk hari ini. Silakan absen masuk terlebih dahulu.',
                    'code' => 'NO_CHECK_IN'
                ], 400);
            }

            if (!is_null($presensi->check_out)) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah absen pulang hari ini pada pukul ' . $presensi->check_out,
                    'code' => 'ALREADY_CHECKED_OUT'
                ], 400);
            }

            $now = now();
            if ($now->hour >= 0 && $now->hour < 6) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Batas absen pulang kemarin sudah lewat (23:59). Status anda akan otomatis alfa.',
                    'code' => 'DEADLINE_PASSED'
                ], 400);
            }

            $setting = MasterData::first();

            if ($setting) {
                $radiusCheck = $this->presensiService->checkRadius(
                    (float)$request->latitude,
                    (float)$request->longitude,
                    $setting
                );

                if (!$radiusCheck['di_dalam_radius']) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar radius kantor. Jarak: ' . round($radiusCheck['jarak']) . ' meter (Maks: ' . $setting->radius . ' meter)',
                        'code' => 'OUT_OF_RADIUS',
                        'data' => [
                            'distance' => round($radiusCheck['jarak']),
                            'max_radius' => $setting->radius
                        ]
                    ], 400);
                }
            }

            $presensi->update([
                'check_out' => now()->toTimeString(),
                'check_out_lat' => $request->latitude,
                'check_out_lng' => $request->longitude
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absen Pulang Berhasil! Selamat Beristirahat!',
                'code' => 'SUCCESS',
                'data' => [
                    'check_out_time' => $presensi->check_out,
                    'tanggal' => $presensi->tanggal,
                    'latitude' => $presensi->check_out_lat,
                    'longitude' => $presensi->check_out_lng
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal. Periksa kembali input Anda.',
                'code' => 'VALIDATION_ERROR',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            $this->logger->error('Database error saat check out: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.',
                'code' => 'DATABASE_ERROR'
            ], 500);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            $this->logger->error('Error check out: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }
}
