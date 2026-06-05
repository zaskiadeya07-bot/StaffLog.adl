<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\MasterData;
use App\Models\Pengguna;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class absen_keluar extends Controller
{
    /**
     * ===================================================================
     * INDEX - Menampilkan Halaman Check Out
     * ===================================================================
     */
    public function index()
    {
        // Kemungkinan : Session tidak ada (belum login)
        if (!session()->has('pengguna_id')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Kemungkinan : Data setting mungkin kosong
        $setting = MasterData::first();
        
        // Kemungkinan : Pengguna tidak ditemukan
        $pengguna = Pengguna::find(session('pengguna_id'));
        
        if (!$pengguna) {
            // Logout jika pengguna tidak ditemukan
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Akun tidak ditemukan. Silakan login kembali.');
        }
        
        return view('karyawan.check-out', compact('setting', 'pengguna'));
    }
    
    /**
     * ===================================================================
     * STATUS - Cek Status Check Out Hari Ini
     * ===================================================================
     */
    public function status()
    {
        try {
            // Kemungkinan : Session tidak ada
            if (!session()->has('pengguna_id')) {
                return response()->json([
                    'hasCheckedOut' => false,
                    'error' => 'Session tidak valid'
                ], 401);
            }
            
            $penggunaId = session('pengguna_id');
            
            // Kemungkinan : Query database
            $todayPresensi = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();
            
            // Cek apakah sudah check out
            $hasCheckedOut = $todayPresensi && !is_null($todayPresensi->check_out);
            
            return response()->json([
                'hasCheckedOut' => $hasCheckedOut,
                'data' => $todayPresensi ? [
                    'check_in_time' => $todayPresensi->check_in,
                    'check_out_time' => $todayPresensi->check_out,
                    'tanggal' => $todayPresensi->tanggal
                ] : null
            ]);
            
        } catch (\Exception $e) {
            // Kemungkinan : Error database/koneksi
            Log::error('Error cek status check out: ' . $e->getMessage());
            
            return response()->json([
                'hasCheckedOut' => false,
                'error' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
    
    /**
     * ===================================================================
     * STORE - Menyimpan Data Check Out
     * ===================================================================
     */
    public function store(Request $request)
    {
        // Mulai transaction untuk rollback jika error
        DB::beginTransaction();
        
        try {
            // ===========================================================
            // KEMUNGKINAN : Validasi Input Gagal
            // ===========================================================
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
            
            // ===========================================================
            // KEMUNGKINAN : Session Tidak Ada (Belum Login)
            // ===========================================================
            if (!session()->has('pengguna_id')) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda telah berakhir. Silakan login kembali.',
                    'code' => 'SESSION_EXPIRED'
                ], 401);
            }
            
            $penggunaId = session('pengguna_id');
            
            // ===========================================================
            // KEMUNGKINAN : Pengguna Tidak Ditemukan di Database
            // ===========================================================
            $pengguna = Pengguna::find($penggunaId);
            
            if (!$pengguna) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data pengguna tidak ditemukan. Silakan hubungi administrator.',
                    'code' => 'USER_NOT_FOUND'
                ], 404);
            }
            
            // ===========================================================
            // KEMUNGKINAN : Belum Check In Hari Ini
            // ===========================================================
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
            
            // ===========================================================
            // KEMUNGKINAN : Sudah Check Out Hari Ini
            // ===========================================================
            if (!is_null($presensi->check_out)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check out hari ini pada pukul ' . $presensi->check_out,
                    'code' => 'ALREADY_CHECKED_OUT'
                ], 400);
            }
            
            // ===========================================================
            // KEMUNGKINAN : Validasi Radius Kantor (Server Side)
            // ===========================================================
            $setting = MasterData::first();
            
            if ($setting) {
                $distance = $this->calculateDistance(
                    (float)$request->latitude,
                    (float)$request->longitude,
                    (float)$setting->lat_kantor,
                    (float)$setting->long_kantor
                );
                
                if ($distance > $setting->radius) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar radius kantor. Jarak: ' . round($distance) . ' meter (Maks: ' . $setting->radius . ' meter)',
                        'code' => 'OUT_OF_RADIUS',
                        'data' => [
                            'distance' => round($distance),
                            'max_radius' => $setting->radius
                        ]
                    ], 400);
                }
            }
            
            // ===========================================================
            // KEMUNGKINAN : Update Data Check Out (SUKSES)
            // ===========================================================
            $presensi->update([
                'check_out' => now()->toTimeString(),
                'check_out_lat' => $request->latitude,
                'check_out_lng' => $request->longitude
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Check Out Berhasil! Istirahat yang cukup!',
                'code' => 'SUCCESS',
                'data' => [
                    'check_out_time' => $presensi->check_out,
                    'tanggal' => $presensi->tanggal,
                    'latitude' => $presensi->check_out_lat,
                    'longitude' => $presensi->check_out_lng
                ]
            ]);
            
        } catch (ValidationException $e) {
            // ===========================================================
            // KEMUNGKINAN : Validasi Gagal
            // ===========================================================
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal. Periksa kembali input Anda.',
                'code' => 'VALIDATION_ERROR',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Illuminate\Database\QueryException $e) {
            // ===========================================================
            // KEMUNGKINAN : Error Database
            // ===========================================================
            DB::rollBack();
            
            Log::error('Database error saat check out: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.',
                'code' => 'DATABASE_ERROR'
            ], 500);
            
        } catch (\Exception $e) {
            // ===========================================================
            // KEMUNGKINAN : Error Tidak Terduga
            // ===========================================================
            DB::rollBack();
            
            Log::error('Error check out: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }
    
    /**
     * ===================================================================
     * calculateDistance - Menghitung Jarak (Haversine Formula)
     * ===================================================================
     * Digunakan untuk validasi radius di server side
     * 
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2 (kantor)
     * @param float $lon2 Longitude titik 2 (kantor)
     * @return float Jarak dalam meter
     * ===================================================================
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Meter
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}