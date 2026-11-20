<?php

namespace App\Http\Requests\Admin\Concerns;

trait IndonesianMessages
{
    /**
     * Pesan validasi berbahasa Indonesia untuk aturan yang dipakai modul admin.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max.string' => ':attribute maksimal :max karakter.',
            'max.file' => ':attribute maksimal :max KB.',
            'in' => ':attribute yang dipilih tidak valid.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus berformat JPG, PNG, atau WebP.',
            'uploaded' => ':attribute gagal diunggah. Periksa ukuran file.',
            'integer' => ':attribute tidak valid.',
            'distinct' => ':attribute berisi data ganda.',
            'array' => ':attribute tidak valid.',
            'email' => ':attribute harus berupa alamat email yang valid.',
            'regex' => 'Format :attribute tidak valid.',
            'date' => ':attribute bukan tanggal yang valid.',
        ];
    }
}
