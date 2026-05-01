<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Versi menggunakan variabel individu dan compact()
        $nama = "Zaskia";
        $pekerjaan = "programmer";

        return view('home', compact('nama', 'pekerjaan'));
    }

    public function contact()
    {
        return view('contact');
    }
}