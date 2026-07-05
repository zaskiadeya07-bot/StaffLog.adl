<?php

namespace App\Http\Requests\Karyawan;

use App\Rules\CurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('pengguna_id');
    }

    public function rules(): array
    {
        $rules = [
            'nomor_hp'     => 'nullable|string|max:12',
            'alamat'       => 'nullable|string',
        ];

        if ($this->filled('password_lama') || $this->filled('password_baru')) {
            $rules['password_lama'] = ['required', new CurrentPassword($this->session()->get('pengguna_id'))];
            $rules['password_baru'] = ['required', 'min:6', 'confirmed'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nomor_hp.max'            => 'Nomor HP maksimal 12 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_lama.required'  => 'Password saat ini wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
        ];
    }
}
