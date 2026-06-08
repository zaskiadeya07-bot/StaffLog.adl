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

class AbsenKeluar extends Controller
{
    public function __construct(
        protected PresensiService $presensiService
    ) {}

    public function index(Request $request)
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

        return view('karyawan.CheckOut', compact('setting', 'pengguna'));
    }

    public function status(Request $request)
    {
        try {
            if (!$request->session()->has('pengguna_id')) {
                return response()->json([
                    'hasCheckedOut' => false,
                    'error' => 'Session tidak valid'
                ], 401);
            }

            return response()->json(
                $this->presensiService->statusCheckOut($request->session()->get('pengguna_id'))
            );
        } catch (\Exception $e) {
            Log::error('Error cek status check out: ' . $e->getMessage());

            return response()->json([
                'hasCheckedOut' => false,
                'error' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180'
            ], [
                'latitude.required' => 'Latitude wajib diisi',
                'latitude.numeric' => 'Latitude harus berupa angka',
                'latitude.between' => 'Latitude harus antara -90 dan 90',
                'longitude.required' => 'Longitude wajib diisi',
                'longitude.numeric' => 'Longitude harus berupa angka',
                'longitude.between' => 'Longitude harus antara -180 dan 180'
            ]);

            if (!$request->session()->has('pengguna_id')) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda telah berakhir. Silakan login kembali.',
                    'code' => 'SESSION_EXPIRED'
                ], 401);
            }

            $penggunaId = $request->session()->get('pengguna_id');

            $pengguna = Pengguna::find($penggunaId);

            if (!$pengguna) {
                DB::rollBack();
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
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan check in hari ini. Silakan check in terlebih dahulu.',
                    'code' => 'NO_CHECK_IN'
                ], 400);
            }

            if (!is_null($presensi->check_out)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check out hari ini pada pukul ' . $presensi->check_out,
                    'code' => 'ALREADY_CHECKED_OUT'
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
                    DB::rollBack();
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check Out Berhasil! Selamat Beristirahat!',
                'code' => 'SUCCESS',
                'data' => [
                    'check_out_time' => $presensi->check_out,
                    'tanggal' => $presensi->tanggal,
                    'latitude' => $presensi->check_out_lat,
                    'longitude' => $presensi->check_out_lng
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal. Periksa kembali input Anda.',
                'code' => 'VALIDATION_ERROR',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            Log::error('Database error saat check out: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.',
                'code' => 'DATABASE_ERROR'
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error check out: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }
}
