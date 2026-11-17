<?php

namespace App\Http\Requests\Admin;

use App\Helpers\UploadHelper;
use App\Http\Requests\Admin\Concerns\IndonesianMessages;
use App\Services\PageContentService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UpdatePageContentRequest extends FormRequest
{
    use IndonesianMessages {
        messages as baseMessages;
    }

    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware auth di routes/admin.php
    }

    /** @return array<string, array<string, mixed>> kolom halaman yang sedang diubah, kunci => definisi */
    private function fields(): array
    {
        return app(PageContentService::class)->fields((string) $this->route('page'));
    }

    protected function prepareForValidation(): void
    {
        $content = $this->input('content');

        if (! is_array($content)) {
            return;
        }

        // Kartu yang dibiarkan kosong dibuang begitu saja.
        foreach ($this->fields() as $key => $field) {
            if (($field['type'] ?? 'text') === 'cards' && is_array($content[$key] ?? null)) {
                $content[$key] = array_values(array_filter(
                    array_map(fn ($item) => is_array($item)
                        ? ['title' => trim((string) ($item['title'] ?? '')), 'text' => trim((string) ($item['text'] ?? ''))]
                        : ['title' => '', 'text' => ''], $content[$key]),
                    fn (array $item) => $item['title'] !== '' || $item['text'] !== '',
                ));
            }
        }

        $this->merge(['content' => $content]);
    }

    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        $rules = ['content' => ['nullable', 'array']];

        foreach ($this->fields() as $key => $field) {
            $type = $field['type'] ?? 'text';

            if ($type === 'image') {
                $rules["image_{$key}"] = ['nullable', ...UploadHelper::IMAGE_RULES];

                continue;
            }

            if ($type === 'cards') {
                $rules["content.{$key}"] = ['required', 'array', 'min:'.($field['min_items'] ?? 1), 'max:'.($field['max_items'] ?? 10)];
                $rules["content.{$key}.*.title"] = ['required', 'string', 'max:'.($field['max_title'] ?? 60)];
                $rules["content.{$key}.*.text"] = ['required', 'string', 'max:'.($field['max_text'] ?? 300)];

                continue;
            }

            $rules["content.{$key}"] = [
                ($field['optional'] ?? false) ? 'nullable' : 'required',
                'string',
                'max:'.($field['max'] ?? 200),
                ...($field['rules'] ?? []),
                ...($type === 'title' ? [function (string $attribute, mixed $value, Closure $fail) {
                    // Tanda * untuk mewarnai harus berpasangan.
                    if (substr_count((string) $value, '*') % 2 !== 0) {
                        $fail('Tanda * pada :attribute harus berpasangan (awal dan akhir kata yang diwarnai).');
                    }
                }] : []),
            ];
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            ...$this->baseMessages(),
            'min.array' => ':attribute minimal :min butir.',
            'max.array' => ':attribute maksimal :max butir.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->fields() as $key => $field) {
            $item = mb_strtolower($field['item_label'] ?? 'butir');

            $attributes["content.{$key}"] = $field['label'];
            $attributes["content.{$key}.*.title"] = "Judul {$item}";
            $attributes["content.{$key}.*.text"] = "Uraian {$item}";
            $attributes["image_{$key}"] = $field['label'];
        }

        return $attributes;
    }

    /** @return array<string, mixed> isian teks dan kartu: kunci => nilai */
    public function content(): array
    {
        return $this->validated('content') ?? [];
    }

    /** @return array<string, UploadedFile> gambar baru yang diunggah: kunci => file */
    public function images(): array
    {
        $images = [];

        foreach ($this->fields() as $key => $field) {
            if (($field['type'] ?? 'text') === 'image' && $this->file("image_{$key}") instanceof UploadedFile) {
                $images[$key] = $this->file("image_{$key}");
            }
        }

        return $images;
    }

    /** @return list<string> kunci gambar yang dikembalikan ke gambar default */
    public function revertedImages(): array
    {
        return array_values(array_filter(
            array_keys($this->fields()),
            fn (string $key) => ($this->fields()[$key]['type'] ?? 'text') === 'image' && $this->boolean("remove_image_{$key}"),
        ));
    }
}
