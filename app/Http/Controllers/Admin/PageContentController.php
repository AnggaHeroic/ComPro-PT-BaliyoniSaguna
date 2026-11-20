<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PreviewPageContentRequest;
use App\Http\Requests\Admin\UpdatePageContentRequest;
use App\Services\ActivityLogService;
use App\Services\CompanyService;
use App\Services\PageContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class PageContentController extends Controller
{
    public function __construct(
        private PageContentService $pageContentService,
        private CompanyService $companyService,
        private ActivityLogService $logService,
    ) {}

    public function edit(Request $request, string $page): View
    {
        abort_unless($this->pageContentService->has($page), 404);

        $record = $this->pageContentService->find($page);
        $definition = $this->pageContentService->definition($page);
        $sections = array_keys($definition['sections']);
        $requested = $request->query('bagian');

        return view('admin.pages.edit', [
            'page' => $page,
            'record' => $record,
            'definition' => $definition,
            'content' => $this->pageContentService->resolve($page),
            'sections' => $sections,
            // Bagian yang memuat galat dibuka lebih dulu; selain itu bagian dari ?bagian= atau yang pertama.
            'initialSection' => $this->pageContentService->sectionWithErrors($page, session('errors')?->getBag('default')->keys() ?? [])
                ?? (in_array($requested, $sections, true) ? $requested : $sections[0]),
            'lastChange' => $this->logService->lastChange($record),
        ]);
    }

    public function update(UpdatePageContentRequest $request, string $page): RedirectResponse
    {
        abort_unless($this->pageContentService->has($page), 404);

        try {
            $this->pageContentService->update(
                $this->pageContentService->find($page),
                $request->content(),
                $request->images(),
                $request->revertedImages(),
            );
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal menyimpan isi halaman.');
        }

        // Tetap di bagian yang sedang diedit.
        $section = $request->input('bagian');

        return redirect()
            ->route('admin.pages.edit', array_filter(['page' => $page, 'bagian' => is_string($section) ? $section : null]))
            ->with('success', 'Isi halaman berhasil disimpan.');
    }

    /**
     * Pratinjau satu bagian halaman dengan isian form yang belum disimpan. Halaman publik dirender
     * biasa, lalu skrip kecil menyembunyikan semua bagian lain. Tidak ada yang disimpan.
     */
    public function preview(PreviewPageContentRequest $request, string $page, string $section): Response
    {
        abort_unless(in_array($section, $this->pageContentService->previewTargets($page), true), 404);

        $html = $this->pageContentService->previewing($page, $request->validated('content') ?? [], fn () => view('web.'.$page, [
            ...$this->companyService->pageData($page),
            'content' => $this->pageContentService->resolve($page),
        ])->render());

        $isolate = view('admin.pages.preview-isolate', ['section' => $section])->render();
        $html = str_contains($html, '</body>')
            ? substr_replace($html, $isolate, strrpos($html, '</body>'), 0)
            : $html.$isolate;

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8')->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function reset(string $page): RedirectResponse
    {
        return $this->change($page, fn ($record) => $this->pageContentService->reset($record), 'Semua teks dan gambar dikembalikan ke setelan default.', 'Gagal mengembalikan isi halaman.');
    }

    public function setDefault(string $page): RedirectResponse
    {
        return $this->change($page, fn ($record) => $this->pageContentService->setDefault($record), 'Isi tersimpan saat ini dijadikan setelan default.', 'Gagal menetapkan setelan default.');
    }

    public function clearDefault(string $page): RedirectResponse
    {
        return $this->change($page, fn ($record) => $this->pageContentService->clearDefault($record), 'Setelan default kustom dihapus. Tampilan website tidak berubah.', 'Gagal menghapus setelan default.');
    }

    /** Jalankan satu aksi tulis pada halaman, dengan penanganan galat yang sama untuk semuanya. */
    private function change(string $page, callable $action, string $success, string $failure): RedirectResponse
    {
        abort_unless($this->pageContentService->has($page), 404);

        try {
            $action($this->pageContentService->find($page));
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', $failure);
        }

        return redirect()->route('admin.pages.edit', $page)->with('success', $success);
    }
}
