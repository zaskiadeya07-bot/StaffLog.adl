<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePengaturanRequest;
use App\Models\Devisi;
use App\Models\MasterData;

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
                'jatah_cuti_bulanan' => 1,
            ]);
        }

        $divisis = Devisi::orderBy('id_devisi')->get();

        return view('admin.PengaturanKantor', compact('setting', 'divisis'));
    }

    public function update(UpdatePengaturanRequest $request)
    {
        $data = $request->validated();
        $data['jam_masuk_std']  = date('H:i:s', strtotime($data['jam_masuk_std']));
        $data['jam_pulang_std'] = date('H:i:s', strtotime($data['jam_pulang_std']));

        $setting = MasterData::first();

        if ($setting) {
            $setting->update($data);
        } else {
            MasterData::create($data);
        }

        return redirect()->route('admin.pengaturan-kantor')
            ->with('success', 'Pengaturan kantor berhasil disimpan!');
    }
}
