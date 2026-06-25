<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengaturanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jam_masuk_std'      => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value >= $this->jam_pulang_std) {
                        $fail('Jam pulang tidak boleh mendahului jam masuk.');
                    }
                },
            ],
            'jam_pulang_std'     => 'required',
            'lat_kantor'         => 'required|numeric|between:-90,90',
            'long_kantor'        => 'required|numeric|between:-180,180',
            'radius'             => 'required|integer|min:10|max:1000',
            'toleransi'          => 'required|integer|min:0|max:120',
            'jatah_cuti_bulanan' => 'required|integer|min:0|max:31',
        ];
    }

    public function messages(): array
    {
        return [
            'radius.max' => 'Radius tidak boleh lebih dari 1000 meter.',
        ];
    }
}
