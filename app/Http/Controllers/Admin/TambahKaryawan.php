<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\Pengguna;
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
            $last = Pengguna::where('id_karyawan', 'like', 'EMP-%')
                ->orderByRaw('CAST(SUBSTRING(id_karyawan, 5) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            $nextNumber = $last ? (int) substr($last->id_karyawan, 4) + 1 : 1;
            $id_karyawan = 'EMP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            Pengguna::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'id_karyawan' => $id_karyawan,
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
                ->with('success', 'Data karyawan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui karyawan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = Pengguna::findOrFail($id);
            $karyawan->update(['status' => 'nonaktif']);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan ' . $karyawan->nama_lengkap . ' berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menonaktifkan karyawan. Silakan coba lagi.');
        }
    }

    public function activate($id)
    {
        try {
            $karyawan = Pengguna::findOrFail($id);
            $karyawan->update(['status' => 'aktif']);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan ' . $karyawan->nama_lengkap . ' berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengaktifkan karyawan. Silakan coba lagi.');
        }
    }
}
