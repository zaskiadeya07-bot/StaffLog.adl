<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListKaryawanController extends Controller
{
    public function index()
    {
        return view('list_karyawan');
    }
}
