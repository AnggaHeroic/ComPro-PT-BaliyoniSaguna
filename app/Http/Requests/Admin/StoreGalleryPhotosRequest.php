<?php

namespace App\Http\Requests\Admin;

use App\Helpers\UploadHelper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryPhotosRequest extends FormRequest
{
    /** Jumlah foto maksimum per sekali unggah. */
    public const MAX_PER_UPLOAD = 10;

    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware auth di routes/admin.php
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1', 'max:'.self::MAX_PER_UPLOAD],
            'images.*' => ['required', ...UploadHelper::IMAGE_RULES],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'images' => 'Foto',
            'images.*' => 'Foto',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'images.required' => 'Pilih minimal satu foto untuk diunggah.',
            'images.max' => 'Maksimal '.self::MAX_PER_UPLOAD.' foto per unggahan.',
            'required' => ':attribute wajib diisi.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus berformat JPG, PNG, atau WebP.',
            'uploaded' => ':attribute gagal diunggah. Periksa ukuran file (maksimal 4 MB).',
            'max.file' => ':attribute maksimal :max KB.',
            'array' => ':attribute tidak valid.',
        ];
    }
}
