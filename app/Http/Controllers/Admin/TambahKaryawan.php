<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TambahKaryawan extends Controller
{
    public function index()
    {
        $divisis = DB::table('devisi')->select('id_devisi as id', 'nama_devisi')->get();
        return view('admin.TambahKaryawan', compact('divisis'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'id_karyawan' => 'required|string|max:20|unique:pengguna,id_karyawan',
            'username' => 'required|string|max:50|unique:pengguna,username',
            'alamat' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:15',
            'tgl_mulai_kerja' => 'nullable|date',
            'divisi' => 'required|exists:devisi,id_devisi',
            'password' => 'required|min:6|confirmed',
        ]);
        
        try {
            DB::table('pengguna')->insert([
                'nama_lengkap' => $request->nama_lengkap,
                'id_karyawan' => $request->id_karyawan,
                'username' => $request->username,
                'alamat' => $request->alamat,
                'nomor_hp' => $request->nomor_hp,
                'tgl_mulai_kerja' => $request->tgl_mulai_kerja,
                'divisi' => $request->divisi,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
            ]);
            
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan ' . $request->nama_lengkap . ' berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function edit($id)
    {
        $karyawan = DB::table('pengguna')->where('id_pengguna', $id)->first();
        $divisis = DB::table('devisi')->select('id_devisi as id', 'nama_devisi')->get();
        
        if (!$karyawan) {
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }
        
        return view('admin.EditKaryawan', compact('karyawan', 'divisis'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'id_karyawan' => 'required|string|max:20|unique:pengguna,id_karyawan,' . $id . ',id_pengguna',
            'username' => 'required|string|max:50|unique:pengguna,username,' . $id . ',id_pengguna',
            'alamat' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:15',
            'tgl_mulai_kerja' => 'nullable|date',
            'divisi' => 'required|exists:devisi,id_devisi',
        ]);
        
        try {
            $updateData = [
                'nama_lengkap' => $request->nama_lengkap,
                'id_karyawan' => $request->id_karyawan,
                'username' => $request->username,
                'alamat' => $request->alamat,
                'nomor_hp' => $request->nomor_hp,
                'tgl_mulai_kerja' => $request->tgl_mulai_kerja,
                'divisi' => $request->divisi,
            ];
            
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'min:6|confirmed'
                ]);
                $updateData['password'] = Hash::make($request->password);
            }
            
            DB::table('pengguna')->where('id_pengguna', $id)->update($updateData);
            
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Data karyawan berhasil diupdate!');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengupdate karyawan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function destroy($id)
    {
        try {
            $perizinan = DB::table('perizinan')
                ->where('id_pengguna_pengaju', $id)
                ->orWhere('id_admin_validator', $id)
                ->exists();
            
            if ($perizinan) {
                DB::table('perizinan')
                    ->where('id_pengguna_pengaju', $id)
                    ->orWhere('id_admin_validator', $id)
                    ->delete();
            }
            
            $presensi = DB::table('presensi')->where('id_pengguna', $id)->exists();
            
            if ($presensi) {
                DB::table('presensi')->where('id_pengguna', $id)->delete();
            }
            
            DB::table('pengguna')->where('id_pengguna', $id)->delete();
            
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }
}
