<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function commonRules(): array
    {
        return [
            'nama_lengkap'    => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'alamat'          => ['nullable', 'string', 'max:255'],
            'nomor_hp'        => ['required', 'digits_between:10,15'],
            'tgl_mulai_kerja' => ['required', 'date', 'before_or_equal:today'],
            'divisi'          => ['required', 'exists:devisi,id_devisi'],
        ];
    }

    protected function commonMessages(): array
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
            'tgl_mulai_kerja.date'       => 'Format tanggal mulai kerja tidak valid.',
            'tgl_mulai_kerja.before_or_equal' => 'Tanggal mulai kerja tidak boleh lebih dari hari ini.',
            'alamat.string'              => 'Format alamat tidak valid.',
            'alamat.max'                 => 'Alamat maksimal 255 karakter.',
            'divisi.required'            => 'Divisi wajib dipilih.',
            'divisi.exists'              => 'Divisi yang dipilih tidak valid.',
        ];
    }
}
