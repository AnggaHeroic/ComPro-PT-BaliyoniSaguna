<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PreviewPageContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware auth di routes/admin.php
    }

    /**
     * Pratinjau hanya merender isian sementara; kelayakan tiap isian dirapikan oleh PageContentService::previewValues().
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return ['content' => ['nullable', 'array']];
    }
}
