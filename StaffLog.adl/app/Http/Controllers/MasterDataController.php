<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData;

class MasterDataController extends Controller
{
    /** Tampilkan halaman pengaturan kantor (admin) */
    public function index()
    {
        $setting = MasterData::first();
        return view('admin.pengaturan-kantor', compact('setting'));
    }

    /** Simpan / update pengaturan kantor */
    public function update(Request $request)
    {
        $data = $request->validate([
            'jam_masuk_std'  => ['required'],
            'jam_pulang_std' => ['required'],
            'lat_kantor'     => ['required', 'numeric'],
            'long_kantor'    => ['required', 'numeric'],
            'radius'         => ['required', 'integer', 'min:10'],
            'toleransi'      => ['required', 'integer', 'min:0'],
        ]);

        $setting = MasterData::first();
        if ($setting) {
            $setting->update($data);
        } else {
            MasterData::create($data);
        }

        return back()->with('success', 'Pengaturan kantor berhasil disimpan!');
    }

    /** API: kembalikan data pengaturan sebagai JSON (dipakai oleh view karyawan) */
    public function apiSetting()
    {
        $setting = MasterData::first();
        if (!$setting) {
            return response()->json([
                'lat_kantor'  => -6.20876500,
                'long_kantor' => 106.84559300,
                'radius'      => 100,
                'toleransi'   => 15,
            ]);
        }
        return response()->json([
            'lat_kantor'  => (float) $setting->lat_kantor,
            'long_kantor' => (float) $setting->long_kantor,
            'radius'      => (int)   $setting->radius,
            'toleransi'   => (int)   $setting->toleransi,
        ]);
    }
}
