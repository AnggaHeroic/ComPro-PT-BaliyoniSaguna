<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\IndonesianMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReorderRequest extends FormRequest
{
    use IndonesianMessages;

    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware auth di routes/admin.php
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['integer', 'distinct'],
            // Hanya untuk aktivitas: urutan berlaku di dalam satu kategori.
            'category' => ['nullable', 'integer', Rule::exists('service_categories', 'id')],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'order' => 'Urutan',
            'order.*' => 'Urutan',
            'category' => 'Kategori',
        ];
    }
}
