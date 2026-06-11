<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\MasterData;
use Illuminate\Http\Request;

class PengaturanKantorController extends Controller
{
    public function index()
    {
        $setting = MasterData::first();

        if (!$setting) {
            $setting = MasterData::create([
                'jam_masuk_std' => '08:00:00',
                'jam_pulang_std' => '17:00:00',
                'lat_kantor' => -6.20876500,
                'long_kantor' => 106.84559300,
                'radius' => 100,
                'toleransi' => 15,
                'jatah_cuti_tahunan' => 12,
            ]);
        }

        $divisis = Devisi::orderBy('id_devisi')->get();

        return view('admin.PengaturanKantor', compact('setting', 'divisis'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'jam_masuk_std' => 'required|date_format:H:i',
            'jam_pulang_std' => 'required|date_format:H:i',
            'lat_kantor' => 'required|numeric|between:-90,90',
            'long_kantor' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'toleransi' => 'required|integer|min:0|max:120',
            'jatah_cuti_tahunan' => 'required|integer|min:0|max:365',
        ]);

        try {
            $setting = MasterData::first();

            if ($setting) {
                $setting->update($validated);
            } else {
                MasterData::create($validated);
            }

            return redirect()->route('admin.pengaturan-kantor')
                ->with('success', 'Pengaturan kantor berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan pengaturan. Silakan coba lagi.')
                ->withInput();
        }
    }
}
