<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class AbsenMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('pengguna_id');
    }

    public function rules(): array
    {
        return [
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'jam_masuk' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required'  => 'Latitude wajib diisi.',
            'latitude.numeric'   => 'Latitude harus berupa angka.',
            'latitude.between'   => 'Latitude harus antara -90 dan 90.',
            'longitude.required' => 'Longitude wajib diisi.',
            'longitude.numeric'  => 'Longitude harus berupa angka.',
            'longitude.between'  => 'Longitude harus antara -180 dan 180.',
        ];
    }
}
