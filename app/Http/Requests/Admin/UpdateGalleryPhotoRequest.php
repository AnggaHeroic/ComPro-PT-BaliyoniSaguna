<?php

namespace App\Http\Requests\Admin;

use App\Helpers\UploadHelper;
use App\Http\Requests\Admin\Concerns\IndonesianMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGalleryPhotoRequest extends FormRequest
{
    use IndonesianMessages;

    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware auth di routes/admin.php
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->input('status') === 'published',
            'description' => filled($this->input('description')) ? trim((string) $this->input('description')) : null,
        ]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['published', 'draft'])],
            'image' => ['nullable', ...UploadHelper::IMAGE_RULES],
            'is_published' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'title' => 'Judul',
            'description' => 'Keterangan',
            'status' => 'Status',
            'image' => 'Foto',
        ];
    }
}
