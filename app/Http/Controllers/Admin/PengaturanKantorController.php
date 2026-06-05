<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanKantorController extends Controller
{
    /**
     * Menampilkan halaman pengaturan kantor
     */
    public function index()
    {
        // Ambil data setting dari database
        $setting = DB::table('master_data')->first();
        
        // Jika belum ada data, buat default
        if (!$setting) {
            $id = DB::table('master_data')->insertGetId([
                'jam_masuk_std' => '08:00:00',
                'jam_pulang_std' => '17:00:00',
                'lat_kantor' => -6.20876500,
                'long_kantor' => 106.84559300,
                'radius' => 100,
                'toleransi' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $setting = DB::table('master_data')->find($id);
        }
        
        return view('admin.pengaturan-kantor', compact('setting'));
    }
    
    /**
     * Menyimpan perubahan pengaturan kantor
     */
    public function update(Request $request)
    {
        // Validasi
        $request->validate([
            'jam_masuk_std' => 'required|date_format:H:i',
            'jam_pulang_std' => 'required|date_format:H:i',
            'lat_kantor' => 'required|numeric|between:-90,90',
            'long_kantor' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'toleransi' => 'required|integer|min:0|max:120',
        ]);
        
        try {
            // Cek apakah sudah ada data
            $setting = DB::table('master_data')->first();
            
            if ($setting) {
                // PERBAIKAN: HAPUS spasi di 'id_pengaturan' dan ganti $setting->id dengan $setting->id_pengaturan
                DB::table('master_data')->where('id_pengaturan', $setting->id_pengaturan)->update([
                    'jam_masuk_std' => $request->jam_masuk_std,
                    'jam_pulang_std' => $request->jam_pulang_std,
                    'lat_kantor' => $request->lat_kantor,
                    'long_kantor' => $request->long_kantor,
                    'radius' => $request->radius,
                    'toleransi' => $request->toleransi,
                    'updated_at' => now(),
                ]);
            } else {
                // Buat data baru
                DB::table('master_data')->insert([
                    'jam_masuk_std' => $request->jam_masuk_std,
                    'jam_pulang_std' => $request->jam_pulang_std,
                    'lat_kantor' => $request->lat_kantor,
                    'long_kantor' => $request->long_kantor,
                    'radius' => $request->radius,
                    'toleransi' => $request->toleransi,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            return redirect()->route('admin.pengaturan-kantor')
                ->with('success', 'Pengaturan kantor berhasil disimpan!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage())
                ->withInput();
        }
    }
}