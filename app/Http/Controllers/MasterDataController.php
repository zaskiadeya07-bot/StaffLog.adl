<?php

namespace App\Http\Controllers;

use App\Models\MasterData;

class MasterDataController extends Controller
{
    public function apiSetting()
    {
        $setting = MasterData::first();

        if (!$setting) {
            return response()->json([
                'lat_kantor'         => -6.20876500,
                'long_kantor'        => 106.84559300,
                'radius'             => 100,
                'toleransi'          => 15,
                'jatah_cuti_bulanan' => 1,
            ]);
        }

        return response()->json([
            'lat_kantor'         => (float) $setting->lat_kantor,
            'long_kantor'        => (float) $setting->long_kantor,
            'radius'             => (int)   $setting->radius,
            'toleransi'          => (int)   $setting->toleransi,
            'jatah_cuti_bulanan' => (int)   $setting->jatah_cuti_bulanan,
        ]);
    }
}
