<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;

class RekapAbsenController extends Controller
{
    public function index()
    {
        return view('karyawan.RekapAbsen');
    }
}
