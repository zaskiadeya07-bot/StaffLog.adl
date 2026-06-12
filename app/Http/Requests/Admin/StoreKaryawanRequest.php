<?php

namespace App\Http\Requests\Admin;

class StoreKaryawanRequest extends BaseKaryawanRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'username' => ['required', 'string', 'min:5', 'max:30', 'regex:/^\S+$/', 'unique:pengguna,username'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
    }

    public function messages(): array
    {
        return array_merge($this->commonMessages(), [
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);
    }
}
