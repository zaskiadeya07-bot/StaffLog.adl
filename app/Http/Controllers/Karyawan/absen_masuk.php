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
    public function index()
    {
        if (!session()->has('pengguna_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        $setting = MasterData::first();
        $pengguna = Pengguna::find(session('pengguna_id'));
        
        if (!$pengguna) {
            session()->flush();
            return redirect()->route('login')->with('error', 'Akun tidak ditemukan.');
        }
        
        return view('karyawan.check-in', compact('setting', 'pengguna'));
    }
    
    public function status()
    {
        try {
            if (!session()->has('pengguna_id')) {
                return response()->json(['sudah_check_in' => false, 'error' => 'Session tidak valid'], 401);
            }
            
            $penggunaId = session('pengguna_id');
            $todayCheckIn = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();
            
            return response()->json([
                'sudah_check_in' => !is_null($todayCheckIn),
                'jam_masuk' => $todayCheckIn ? ($todayCheckIn->check_in ?? null) : null,
                'tanggal' => $todayCheckIn ? $todayCheckIn->tanggal : null
            ]);
            
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
            
            if (!session()->has('pengguna_id')) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Sesi login Anda telah berakhir.'], 401);
            }
            
            $penggunaId = session('pengguna_id');
            $pengguna = Pengguna::find($penggunaId);
            
            if (!$pengguna) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Data pengguna tidak ditemukan.'], 404);
            }
            
            // Cek sudah check in hari ini
            $existingPresensi = Presensi::where('id_pengguna', $penggunaId)
                ->whereDate('tanggal', today())
                ->first();
            
            if ($existingPresensi) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan check in hari ini!'], 400);
            }
            
            // Validasi radius
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
                    return response()->json(['success' => false, 'message' => 'Anda berada di luar radius kantor! Jarak: ' . round($jarak) . ' meter'], 400);
                }
            }
            
            // PERBAIKAN: Format waktu yang benar (HH:MM:SS) bukan HH.MM.SS
            $jamMasukRaw = $request->jam_masuk ?? now()->format('H:i:s');
            // Bersihkan format: ganti titik menjadi titik dua
            $jamMasuk = str_replace('.', ':', $jamMasukRaw);
            
            // Hitung keterlambatan
            $menitTerlambat = 0;
            if ($setting && $jamMasuk) {
                $jamStandar = strtotime($setting->jam_masuk_std);
                $jamMasukTime = strtotime($jamMasuk);
                $menitTerlambat = max(0, ($jamMasukTime - $jamStandar) / 60);
                $menitTerlambat = max(0, $menitTerlambat - ($setting->toleransi ?? 0));
            }
            
            // Simpan dengan format yang benar
            $presensi = Presensi::create([
                'id_pengguna' => $penggunaId,
                'tanggal' => today()->toDateString(),
                'check_in' => $jamMasuk,  // Sekarang formatnya 19:06:54 (bukan 19.06.54)
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'status' => $menitTerlambat > 0 ? 'terlambat' : 'hadir',
                'menit_terlambat' => round($menitTerlambat, 1),
                'catatan_keterlambatan' => $menitTerlambat > 0 ? "Terlambat " . round($menitTerlambat, 1) . " menit" : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Selamat Bekerja!',
                'data' => [
                    'check_in' => $presensi->check_in,
                    'jarak' => round($jarak),
                    'menit_terlambat' => round($menitTerlambat, 1)
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validasi gagal: ' . implode(', ', $e->errors())], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error check in: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
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