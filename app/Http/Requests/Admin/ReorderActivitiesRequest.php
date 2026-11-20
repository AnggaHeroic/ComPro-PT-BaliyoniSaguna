<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\IndonesianMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Urutan baru aktivitas, dikelompokkan per kategori usaha: orders[id_kategori][] = id aktivitas sesuai urutan baru.
 */
class ReorderActivitiesRequest extends FormRequest
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
            'orders' => ['required', 'array', 'min:1'],
            'orders.*' => ['required', 'array', 'min:1'],
            'orders.*.*' => ['integer', 'distinct'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'orders' => 'Urutan',
            'orders.*' => 'Urutan',
            'orders.*.*' => 'Urutan',
        ];
    }
}
