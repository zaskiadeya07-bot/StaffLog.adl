<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class tambah_karyawan extends Controller
{
    /**
     * Menampilkan form tambah karyawan
     */
    public function index()
    {
        // Ambil data divisi, alias id_devisi menjadi id
        $divisis = DB::table('devisi')->select('id_devisi as id', 'nama_devisi')->get();
        return view('admin.tambah-karyawan', compact('divisis'));
    }
    
    /**
     * Menyimpan data karyawan baru
     */
    public function store(Request $request)
    {
        // Validasi input
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
            // Insert data ke tabel pengguna (HAPUS created_at & updated_at)
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
                // 'created_at' => now(),  // HAPUS - kolom tidak ada
                // 'updated_at' => now(),  // HAPUS - kolom tidak ada
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
    
    /**
     * Menampilkan form edit karyawan
     */
    public function edit($id)
    {
        // Ambil data karyawan berdasarkan id_pengguna
        $karyawan = DB::table('pengguna')->where('id_pengguna', $id)->first();
        
        // Ambil data divisi
        $divisis = DB::table('devisi')->select('id_devisi as id', 'nama_devisi')->get();
        
        // Jika karyawan tidak ditemukan
        if (!$karyawan) {
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }
        
        return view('admin.edit-karyawan', compact('karyawan', 'divisis'));
    }
    
    /**
     * Update data karyawan
     */
    public function update(Request $request, $id)
    {
        // Validasi input
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
            // Data yang akan diupdate
            $updateData = [
                'nama_lengkap' => $request->nama_lengkap,
                'id_karyawan' => $request->id_karyawan,
                'username' => $request->username,
                'alamat' => $request->alamat,
                'nomor_hp' => $request->nomor_hp,
                'tgl_mulai_kerja' => $request->tgl_mulai_kerja,
                'divisi' => $request->divisi,
            ];
            
            // Jika password diisi, update password
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'min:6|confirmed'
                ]);
                $updateData['password'] = Hash::make($request->password);
            }
            
            // Update data ke database
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
    /**
 * Hapus karyawan
 */
public function destroy($id)
{
    try {
        // Cek apakah karyawan memiliki data di tabel perizinan
        $perizinan = DB::table('perizinan')
            ->where('id_pengguna_pengaju', $id)
            ->orWhere('id_admin_validator', $id)
            ->exists();
        
        if ($perizinan) {
            // Hapus data perizinan terkait terlebih dahulu
            DB::table('perizinan')
                ->where('id_pengguna_pengaju', $id)
                ->orWhere('id_admin_validator', $id)
                ->delete();
        }
        
        // Cek apakah karyawan memiliki data presensi
        $presensi = DB::table('presensi')->where('id_pengguna', $id)->exists();
        
        if ($presensi) {
            // Hapus data presensi terkait
            DB::table('presensi')->where('id_pengguna', $id)->delete();
        }
        
        // Setelah data terkait dihapus, baru hapus karyawan
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