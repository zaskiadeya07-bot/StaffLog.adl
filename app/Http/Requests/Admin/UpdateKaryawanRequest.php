<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateKaryawanRequest extends BaseKaryawanRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'username' => ['required', 'string', 'min:5', 'max:30', 'regex:/^\S+$/', Rule::unique('pengguna', 'username')->ignore($this->route('id'), 'id_pengguna')],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);
    }

    public function messages(): array
    {
        return array_merge($this->commonMessages(), [
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('password')) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }
    }
}
