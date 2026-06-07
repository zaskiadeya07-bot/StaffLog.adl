<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('karyawan.Dashboard');
    }
}
