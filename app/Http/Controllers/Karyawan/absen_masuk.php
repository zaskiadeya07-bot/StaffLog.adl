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
        
        // Ambil data setting kantor
        $setting = MasterData::first();
        
        // Ambil data pengguna
        $pengguna = Pengguna::find(session('pengguna_id'));
        
        if (!$pengguna) {
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Akun tidak ditemukan. Silakan login kembali.');
        }
        
        return view('karyawan.check-in', compact('setting', 'pengguna'));
    }
    
    /**
     * ===================================================================
     * STATUS - Cek Status Check In Hari Ini (SESUAI DENGAN VIEW)
     * ===================================================================
     */
    public function status()
    {
        try {
            if (!session()->has('pengguna_id')) {
                return response()->json([
                    'sudah_check_in' => false,
                    'error' => 'Session tidak valid'
                ], 401);
            }
            
            $penggunaId = session('pengguna_id');
            
            // PERBAIKAN: Sesuaikan nama kolom dengan tabel (check_in atau jam_masuk?)
            $todayCheckIn = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();
            
            // PERBAIKAN: Response menggunakan 'sudah_check_in' (sesuai view)
            return response()->json([
                'sudah_check_in' => !is_null($todayCheckIn),
                'jam_masuk' => $todayCheckIn ? ($todayCheckIn->jam_masuk ?? $todayCheckIn->check_in) : null,
                'tanggal' => $todayCheckIn ? $todayCheckIn->tanggal : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error cek status check in: ' . $e->getMessage());
            
            return response()->json([
                'sudah_check_in' => false,
                'error' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
    
    /**
     * ===================================================================
     * STORE - Menyimpan Data Check In (SESUAI DENGAN VIEW)
     * ===================================================================
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // PERBAIKAN: Validasi dengan jam_masuk (dikirim dari view)
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'jam_masuk' => 'nullable|string'  // ← TERIMA jam_masuk dari view
            ]);
            
            if (!session()->has('pengguna_id')) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda telah berakhir. Silakan login kembali.'
                ], 401);
            }
            
            $penggunaId = session('pengguna_id');
            
            $pengguna = Pengguna::find($penggunaId);
            
            if (!$pengguna) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data pengguna tidak ditemukan.'
                ], 404);
            }
            
            // Cek sudah check in hari ini
            $existingPresensi = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();
            
            if ($existingPresensi) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check in hari ini!'
                ], 400);
            }
            
            // Validasi radius kantor
            $setting = MasterData::first();
            $jarak = null;
            
            if ($setting) {
                $jarak = $this->calculateDistance(
                    (float)$request->latitude,
                    (float)$request->longitude,
                    (float)$setting->lat_kantor,
                    (float)$setting->long_kantor
                );
                
                if ($jarak > $setting->radius) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar radius kantor! Jarak: ' . round($jarak) . ' meter'
                    ], 400);
                }
            }
            
            // Hitung keterlambatan jika ada jam_masuk dan setting
            $menitTerlambat = 0;
            if ($setting && $request->jam_masuk) {
                $jamStandar = strtotime($setting->jam_masuk_std);
                $jamMasuk = strtotime($request->jam_masuk);
                $menitTerlambat = max(0, ($jamMasuk - $jamStandar) / 60);
                $menitTerlambat = max(0, $menitTerlambat - ($setting->toleransi ?? 0));
            }
            
            // PERBAIKAN: Simpan dengan kolom yang sesuai
            $presensi = Presensi::create([
                'id_pengguna' => $penggunaId,
                'tanggal' => today()->toDateString(),
                'jam_masuk' => $request->jam_masuk ?? now()->toTimeString(),
                'lat_masuk' => $request->latitude,
                'long_masuk' => $request->longitude,
                'status_kehadiran' => $menitTerlambat > 0 ? 'terlambat' : 'hadir',
                'menit_terlambat' => $menitTerlambat,
                'catatan_keterlambatan' => $menitTerlambat > 0 ? "Terlambat {$menitTerlambat} menit" : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            // PERBAIKAN: Response sesuai yang diharapkan view
            return response()->json([
                'success' => true,
                'message' => '✅ Check In Berhasil! Selamat bekerja!',
                'data' => [
                    'jam_masuk' => $presensi->jam_masuk,
                    'jarak' => round($jarak),
                    'menit_terlambat' => $menitTerlambat
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors())
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error check in: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * ===================================================================
     * calculateDistance - Menghitung Jarak (Haversine Formula)
     * ===================================================================
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}