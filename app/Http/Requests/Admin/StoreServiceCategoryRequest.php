<?php

namespace App\Http\Requests\Admin;

use App\Helpers\UploadHelper;
use App\Http\Requests\Admin\Concerns\IndonesianMessages;
use App\Models\ServiceCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceCategoryRequest extends FormRequest
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
            'remove_image' => $this->boolean('remove_image'),
            'remove_icon_image' => $this->boolean('remove_icon_image'),
            'description' => filled($this->input('description')) ? trim((string) $this->input('description')) : null,
        ]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['required', 'string', Rule::in(array_keys(ServiceCategory::ICONS))],
            'status' => ['required', Rule::in(['published', 'draft'])],
            'image' => ['nullable', ...UploadHelper::IMAGE_RULES],
            'remove_image' => ['boolean'],
            // Ikon kustom (diutamakan bila ada) menggantikan ikon dari daftar.
            'icon_image' => ['nullable', ...UploadHelper::IMAGE_RULES],
            'remove_icon_image' => ['boolean'],
            'is_published' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'Nama kategori',
            'description' => 'Deskripsi',
            'icon' => 'Ikon',
            'status' => 'Status',
            'image' => 'Gambar',
            'icon_image' => 'Ikon kustom',
        ];
    }
}
