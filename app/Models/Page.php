<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Isi halaman publik yang diubah admin. Teks bawaan ada di config/page_contents.php;
 * baris ini hanya menyimpan selisihnya (content) dan setelan default kustom (defaults).
 */
#[Fillable(['slug', 'content', 'defaults', 'defaults_set_at'])]
class Page extends Model
{
    use LogsActivity;

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'defaults' => 'array',
            'defaults_set_at' => 'datetime',
        ];
    }

    public function activityLabel(): string
    {
        return (string) config("page_contents.{$this->slug}.label", $this->slug);
    }

    /**
     * Riwayat mencatat nama bagian halaman yang berubah (bukan isi lengkapnya).
     *
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    public function activityChanges(array $changes): array
    {
        $sections = [];

        foreach (config("page_contents.{$this->slug}.sections", []) as $section) {
            foreach (array_keys($section['fields']) as $key) {
                if (array_key_exists('content', $changes) && ($this->getOriginal('content')[$key] ?? null) !== ($this->content[$key] ?? null)) {
                    $sections[$section['label']] = true;
                }
            }
        }

        if (array_key_exists('defaults', $changes)) {
            $sections['Setelan default'] = true;
        }

        return $sections;
    }
}
