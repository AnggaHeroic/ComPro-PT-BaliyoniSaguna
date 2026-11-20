<?php

namespace App\Http\Requests\Admin;

use App\Models\ActivityLog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Parameter query string untuk halaman daftar admin (pencarian, filter, mode atur urutan).
 */
class ListFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware auth di routes/admin.php
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['reorder' => $this->boolean('reorder')]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['published', 'draft'])],
            'category' => ['nullable', 'integer'],
            'action' => ['nullable', Rule::in(array_keys(ActivityLog::actions()))],
            'jenis' => ['nullable', Rule::in(array_keys(ActivityLog::groups()))],
            'menu' => ['nullable', Rule::in(ActivityLog::menus())],
            'dari' => ['nullable', 'date_format:Y-m-d'],
            'sampai' => ['nullable', 'date_format:Y-m-d'],
            'reorder' => ['boolean'],
        ];
    }
}
