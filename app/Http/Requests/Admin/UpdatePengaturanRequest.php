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
            'jam_masuk_std'      => 'required',
            'jam_pulang_std'     => 'required',
            'lat_kantor'         => 'required|numeric|between:-90,90',
            'long_kantor'        => 'required|numeric|between:-180,180',
            'radius'             => 'required|integer|min:10|max:1000',
            'toleransi'          => 'required|integer|min:0|max:120',
            'jatah_cuti_tahunan' => 'required|integer|min:0|max:365',
        ];
    }
}
