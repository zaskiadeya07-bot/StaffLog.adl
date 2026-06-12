<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_devisi' => ['required', 'string', 'max:50', Rule::unique('devisi', 'nama_devisi')->ignore($this->route('id'), 'id_devisi')],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_devisi.required' => 'Nama divisi wajib diisi.',
            'nama_devisi.max'      => 'Nama divisi maksimal 50 karakter.',
            'nama_devisi.unique'   => 'Divisi "' . ($this->nama_devisi ?? '') . '" sudah ada.',
        ];
    }
}
