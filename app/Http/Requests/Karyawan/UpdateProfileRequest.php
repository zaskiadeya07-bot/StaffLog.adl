<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('pengguna_id');
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:100',
            'nomor_hp'     => 'nullable|string|max:15',
            'alamat'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max'      => 'Nama lengkap maksimal 100 karakter.',
            'nomor_hp.max'          => 'Nomor HP maksimal 15 karakter.',
        ];
    }
}
