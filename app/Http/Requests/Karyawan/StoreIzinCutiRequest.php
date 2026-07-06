<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class StoreIzinCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('pengguna_id');
    }

    public function rules(): array
    {
        return [
            'jenis_izin'  => 'required|string',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan'  => 'required|string|min:5',
            'file_surat'  => ($this->input('jenis_izin') === 'Sakit')
                ? 'required|file|mimes:pdf,png,doc,docx|max:5120'
                : 'nullable|file|mimes:pdf,png,doc,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_izin.required'  => 'Jenis permohonan wajib diisi.',
            'tgl_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tgl_mulai.date'       => 'Format tanggal mulai tidak valid.',
            'tgl_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tgl_selesai.date'     => 'Format tanggal selesai tidak valid.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'keterangan.required'  => 'Alasan wajib diisi.',
            'keterangan.min'       => 'Alasan minimal 5 karakter.',
            'file_surat.required'  => 'Lampiran dokumen wajib diisi untuk permohonan Sakit.',
            'file_surat.mimes'     => 'Format file harus PDF, PNG, atau DOC.',
            'file_surat.max'       => 'Ukuran file maksimal 5MB.',
        ];
    }
}
