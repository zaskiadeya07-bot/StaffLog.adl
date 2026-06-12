<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap'    => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'username'        => ['required', 'string', 'min:5', 'max:30', 'regex:/^\S+$/', 'unique:pengguna,username'],
            'alamat'          => ['nullable', 'string', 'max:255'],
            'nomor_hp'        => ['required', 'digits_between:10,15'],
            'tgl_mulai_kerja' => ['required', 'date', 'before_or_equal:today'],
            'divisi'          => ['required', 'exists:devisi,id_devisi'],
            'password'        => ['required', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required'      => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min'           => 'Nama lengkap minimal 3 karakter.',
            'nama_lengkap.max'           => 'Nama lengkap maksimal 100 karakter.',
            'nama_lengkap.regex'         => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'username.required'          => 'Username wajib diisi.',
            'username.min'               => 'Username minimal 5 karakter.',
            'username.max'               => 'Username maksimal 30 karakter.',
            'username.regex'             => 'Username tidak boleh mengandung spasi.',
            'username.unique'            => 'Username sudah digunakan.',
            'nomor_hp.required'          => 'Nomor HP wajib diisi.',
            'nomor_hp.digits_between'    => 'Nomor HP harus 10-15 digit angka.',
            'tgl_mulai_kerja.required'   => 'Tanggal mulai kerja wajib diisi.',
            'tgl_mulai_kerja.before_or_equal' => 'Tanggal mulai kerja tidak boleh lebih dari hari ini.',
            'divisi.required'            => 'Divisi wajib dipilih.',
            'divisi.exists'              => 'Divisi yang dipilih tidak valid.',
            'password.required'          => 'Password wajib diisi.',
            'password.min'               => 'Password minimal 8 karakter.',
            'password.confirmed'         => 'Konfirmasi password tidak sesuai.',
        ];
    }
}
