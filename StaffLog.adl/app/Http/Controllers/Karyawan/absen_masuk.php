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

class absen_masuk extends Controller
{
    /**
     * ===================================================================
     * INDEX - Menampilkan Halaman Check In
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
        
        return view('karyawan.check-in', compact('setting', 'pengguna'));
    }
    
    /**
     * ===================================================================
     * STATUS - Cek Status Check In Hari Ini
     * ===================================================================
     */
    public function status()
    {
        try {
            // Kemungkinan : Session tidak ada
            if (!session()->has('pengguna_id')) {
                return response()->json([
                    'hasCheckedIn' => false,
                    'error' => 'Session tidak valid'
                ], 401);
            }
            
            $penggunaId = session('pengguna_id');
            
            // Kemungkinan : Query database
            $todayCheckIn = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();
            
            return response()->json([
                'hasCheckedIn' => !is_null($todayCheckIn),
                'data' => $todayCheckIn ? [
                    'check_in_time' => $todayCheckIn->check_in,
                    'tanggal' => $todayCheckIn->tanggal
                ] : null
            ]);
            
        } catch (\Exception $e) {
            // Kemungkinan : Error database/koneksi
            Log::error('Error cek status check in: ' . $e->getMessage());
            
            return response()->json([
                'hasCheckedIn' => false,
                'error' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
    
    /**
     * ===================================================================
     * STORE - Menyimpan Data Check In
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
            // KEMUNGKINAN : Sudah Check In Hari Ini
            // ===========================================================
            $existingPresensi = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();
            
            if ($existingPresensi) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check in hari ini pada pukul ' . $existingPresensi->check_in,
                    'code' => 'ALREADY_CHECKED_IN'
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
            // KEMUNGKINAN : Simpan Data Check In (SUKSES)
            // ===========================================================
            $presensi = Presensi::create([
                'id_pengguna' => $penggunaId,
                'tanggal' => today()->toDateString(),
                'check_in' => now()->toTimeString(),
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'status' => 'hadir'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Check In Berhasil! Selamat bekerja!',
                'code' => 'SUCCESS',
                'data' => [
                    'check_in_time' => $presensi->check_in,
                    'tanggal' => $presensi->tanggal,
                    'latitude' => $presensi->check_in_lat,
                    'longitude' => $presensi->check_in_lng
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
            // KEMUNGKINAN : Error Database (Duplicate, Constraint, dll)
            // ===========================================================
            DB::rollBack();
            
            Log::error('Database error saat check in: ' . $e->getMessage());
            
            $errorCode = $e->errorInfo[1] ?? 0;
            
            // Duplicate entry (sudah ada data)
            if ($errorCode == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check in hari ini.',
                    'code' => 'DUPLICATE_ENTRY'
                ], 400);
            }
            
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
            
            // Log error untuk debugging (hanya di development)
            Log::error('Error check in: ' . $e->getMessage());
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
     * @param float $lat1 Latitude titik 1 (karyawan)
     * @param float $lon1 Longitude titik 1 (karyawan)
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