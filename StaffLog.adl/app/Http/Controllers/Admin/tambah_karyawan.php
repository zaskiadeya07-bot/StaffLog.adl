<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class tambah_karyawan extends Controller
{
    public function index()
    {
        $divisis = Devisi::all();
        return view('admin.tambah-karyawan', compact('divisis'));
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'idKaryawan' => 'required|string|unique:pengguna,id_karyawan',
                'username' => 'required|string|unique:pengguna,username',
                'password' => 'required|string|min:6',
                'divisi' => 'required|exists:devisi,id_devisi',
                'alamat' => 'nullable|string',
                'phone' => 'nullable|string|max:15',
                'tanggalMulai' => 'nullable|date'
            ]);
            
            $pengguna = Pengguna::create([
                'id_karyawan' => $request->idKaryawan,
                'nama_lengkap' => $request->nama,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
                'divisi' => $request->divisi,
                'nomor_hp' => $request->phone,
                'tgl_mulai_kerja' => $request->tanggalMulai,
                'alamat' => $request->alamat,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan!',
                'data' => $pengguna
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
            ], 500);
        }
    }
}