<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\Pengguna;
use App\Models\Perizinan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TambahKaryawan extends Controller
{
    public function index()
    {
        $divisis = Devisi::select('id_devisi as id', 'nama_devisi')->get();
        return view('admin.TambahKaryawan', compact('divisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'    => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'id_karyawan'     => ['required', 'string', 'max:20', 'regex:/^EMP-\d+$/u', 'unique:pengguna,id_karyawan'],
            'username'        => ['required', 'string', 'min:5', 'max:30', 'regex:/^\S+$/', 'unique:pengguna,username'],
            'alamat'          => ['nullable', 'string', 'max:255'],
            'nomor_hp'        => ['required', 'digits_between:10,15'],
            'tgl_mulai_kerja' => ['required', 'date', 'before_or_equal:today'],
            'divisi'          => ['required', 'exists:devisi,id_devisi'],
            'password'        => ['required', 'min:8', 'confirmed'],
        ], [
            'nama_lengkap.required'    => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min'         => 'Nama lengkap minimal 3 karakter.',
            'nama_lengkap.max'         => 'Nama lengkap maksimal 100 karakter.',
            'nama_lengkap.regex'       => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'id_karyawan.required'     => 'ID karyawan wajib diisi.',
            'id_karyawan.regex'        => 'Format ID karyawan harus EMP-001 (EMP- diikuti angka).',
            'id_karyawan.unique'       => 'ID karyawan sudah digunakan.',
            'id_karyawan.max'          => 'ID karyawan maksimal 20 karakter.',
            'username.required'        => 'Username wajib diisi.',
            'username.min'             => 'Username minimal 5 karakter.',
            'username.max'             => 'Username maksimal 30 karakter.',
            'username.regex'           => 'Username tidak boleh mengandung spasi.',
            'username.unique'          => 'Username sudah digunakan.',
            'nomor_hp.required'        => 'Nomor HP wajib diisi.',
            'nomor_hp.digits_between'  => 'Nomor HP harus 10-15 digit angka.',
            'tgl_mulai_kerja.required'  => 'Tanggal mulai kerja wajib diisi.',
            'tgl_mulai_kerja.before_or_equal' => 'Tanggal mulai kerja tidak boleh lebih dari hari ini.',
            'divisi.required'          => 'Divisi wajib dipilih.',
            'divisi.exists'            => 'Divisi yang dipilih tidak valid.',
            'password.required'        => 'Password wajib diisi.',
            'password.min'             => 'Password minimal 8 karakter.',
            'password.confirmed'       => 'Konfirmasi password tidak sesuai.',
        ]);

        try {
            Pengguna::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'id_karyawan' => $validated['id_karyawan'],
                'username' => $validated['username'],
                'alamat' => $validated['alamat'],
                'nomor_hp' => $validated['nomor_hp'],
                'tgl_mulai_kerja' => $validated['tgl_mulai_kerja'],
                'divisi' => $validated['divisi'],
                'password' => Hash::make($validated['password']),
                'role' => 'karyawan',
            ]);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan ' . $validated['nama_lengkap'] . ' berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan karyawan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function edit($id)
    {
        $karyawan = Pengguna::find($id);

        if (!$karyawan) {
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }

        $divisis = Devisi::select('id_devisi as id', 'nama_devisi')->get();
        return view('admin.EditKaryawan', compact('karyawan', 'divisis'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Pengguna::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap'    => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'id_karyawan'     => ['required', 'string', 'max:20', 'regex:/^EMP-\d+$/u', 'unique:pengguna,id_karyawan,' . $id . ',id_pengguna'],
            'username'        => ['required', 'string', 'min:5', 'max:30', 'regex:/^\S+$/', 'unique:pengguna,username,' . $id . ',id_pengguna'],
            'alamat'          => ['nullable', 'string', 'max:255'],
            'nomor_hp'        => ['required', 'digits_between:10,15'],
            'tgl_mulai_kerja' => ['required', 'date', 'before_or_equal:today'],
            'divisi'          => ['required', 'exists:devisi,id_devisi'],
        ], [
            'nama_lengkap.required'    => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min'         => 'Nama lengkap minimal 3 karakter.',
            'nama_lengkap.max'         => 'Nama lengkap maksimal 100 karakter.',
            'nama_lengkap.regex'       => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'id_karyawan.required'     => 'ID karyawan wajib diisi.',
            'id_karyawan.regex'        => 'Format ID karyawan harus EMP-001 (EMP- diikuti angka).',
            'id_karyawan.unique'       => 'ID karyawan sudah digunakan.',
            'id_karyawan.max'          => 'ID karyawan maksimal 20 karakter.',
            'username.required'        => 'Username wajib diisi.',
            'username.min'             => 'Username minimal 5 karakter.',
            'username.max'             => 'Username maksimal 30 karakter.',
            'username.regex'           => 'Username tidak boleh mengandung spasi.',
            'username.unique'          => 'Username sudah digunakan.',
            'nomor_hp.required'        => 'Nomor HP wajib diisi.',
            'nomor_hp.digits_between'  => 'Nomor HP harus 10-15 digit angka.',
            'tgl_mulai_kerja.required'  => 'Tanggal mulai kerja wajib diisi.',
            'tgl_mulai_kerja.before_or_equal' => 'Tanggal mulai kerja tidak boleh lebih dari hari ini.',
            'divisi.required'          => 'Divisi wajib dipilih.',
            'divisi.exists'            => 'Divisi yang dipilih tidak valid.',
        ]);

        try {
            $updateData = [
                'nama_lengkap' => $validated['nama_lengkap'],
                'id_karyawan' => $validated['id_karyawan'],
                'username' => $validated['username'],
                'alamat' => $validated['alamat'],
                'nomor_hp' => $validated['nomor_hp'],
                'tgl_mulai_kerja' => $validated['tgl_mulai_kerja'],
                'divisi' => $validated['divisi'],
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $updateData['password'] = Hash::make($request->password);
            }

            $karyawan->update($updateData);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Data karyawan berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengupdate karyawan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = Pengguna::findOrFail($id);

            Perizinan::where('id_pengguna_pengaju', $id)
                ->orWhere('id_admin_validator', $id)
                ->delete();

            Presensi::where('id_pengguna', $id)->delete();

            $karyawan->delete();

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus karyawan. Silakan coba lagi.');
        }
    }
}
