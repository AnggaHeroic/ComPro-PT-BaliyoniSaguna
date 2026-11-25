<?php

namespace Tests\Feature\Admin;

use App\Helpers\RedirectHelper;
use App\Models\ActivityLog;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTuningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_button_text_fields_are_limited_to_thirty_characters(): void
    {
        $buttons = collect(config('page_contents'))
            ->flatMap(fn ($page) => collect($page['sections'] ?? [])->flatMap(fn ($section) => $section['fields'] ?? []))
            ->filter(fn ($field) => str_starts_with($field['label'] ?? '', 'Tombol'));

        $this->assertGreaterThanOrEqual(10, $buttons->count());
        $buttons->each(fn ($field) => $this->assertSame(30, $field['max']));
    }

    public function test_redirect_helper_sets_flash_messages(): void
    {
        $this->assertSame('Ok.', RedirectHelper::success('admin.dashboard', 'Ok.')->getSession()?->get('success') ?? session('success'));
        $this->assertSame('Gagal.', RedirectHelper::failureTo('admin.dashboard', 'Gagal.')->getSession()?->get('error') ?? session('error'));
        $this->assertSame('Gagal.', RedirectHelper::failure('Gagal.', true)->getSession()?->get('error') ?? session('error'));
    }

    public function test_long_names_are_truncated_with_full_text_on_hover(): void
    {
        $name = str_repeat('Panjang', 30);
        ServiceCategory::create(['name' => $name, 'slug' => 'panjang', 'icon' => 'tools', 'sort_order' => 1, 'is_published' => true]);

        $this->get(route('admin.categories.index'))->assertSee('truncate', false)->assertSee('title="'.$name.'"', false);
    }

    public function test_update_log_keeps_old_values_and_shows_detail_row(): void
    {
        $category = ServiceCategory::create(['name' => 'test category', 'slug' => 'test-category', 'icon' => 'tools', 'sort_order' => 1, 'is_published' => true]);
        $category->update(['name' => 'test category 1', 'is_published' => false]);

        $log = ActivityLog::where('action', ActivityLog::ACTION_UPDATED)->latest('id')->firstOrFail();
        $this->assertSame(['name' => 'test category', 'is_published' => true], $log->previous);

        [$main, $note, $detail] = $log->detailLines();
        $this->assertSame('Mengubah kategori usaha "test category"', $main);
        $this->assertSame('Yang diubah: Nama, Status', $note);
        $this->assertSame('Nama: "test category" → "test category 1" · Status: Terbit → Draft', $detail);

        $this->get(route('admin.activity-logs.index'))->assertSee('aria-expanded', false)->assertSee('Status: Terbit → Draft');
    }
}
