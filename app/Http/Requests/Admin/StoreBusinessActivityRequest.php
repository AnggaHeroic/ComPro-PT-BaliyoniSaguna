<?php

namespace App\Http\Requests\Admin;

use App\Helpers\UploadHelper;
use App\Http\Requests\Admin\Concerns\IndonesianMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessActivityRequest extends FormRequest
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
            'summary' => filled($this->input('summary')) ? trim((string) $this->input('summary')) : null,
            'detail' => filled($this->input('detail')) ? trim((string) $this->input('detail')) : null,
        ]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'service_category_id' => ['required', 'integer', Rule::exists('service_categories', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'detail' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(['published', 'draft'])],
            'image' => ['nullable', ...UploadHelper::IMAGE_RULES],
            'remove_image' => ['boolean'],
            'is_published' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'service_category_id' => 'Kategori',
            'name' => 'Nama aktivitas',
            'summary' => 'Ringkasan',
            'detail' => 'Detail',
            'status' => 'Status',
            'image' => 'Gambar',
        ];
    }
}
