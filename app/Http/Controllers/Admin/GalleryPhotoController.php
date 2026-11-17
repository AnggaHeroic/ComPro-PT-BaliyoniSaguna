<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListFilterRequest;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Requests\Admin\StoreGalleryPhotosRequest;
use App\Http\Requests\Admin\UpdateGalleryPhotoRequest;
use App\Models\GalleryPhoto;
use App\Services\ActivityLogService;
use App\Services\GalleryPhotoService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class GalleryPhotoController extends Controller
{
    public function __construct(
        private GalleryPhotoService $photoService,
        private ActivityLogService $logService,
    ) {}

    public function index(ListFilterRequest $request): View
    {
        // Tab Terbit menampilkan pratinjau kisi bento; tab Draft menampilkan daftar.
        $status = $request->validated('status') === 'draft' ? 'draft' : 'published';
        $photos = $status === 'published' ? $this->photoService->published() : new Collection;

        return view('admin.gallery.index', [
            'status' => $status,
            'photos' => $photos,
            'drafts' => $status === 'draft' ? $this->photoService->drafts() : new Collection,
            'counts' => $this->photoService->statusCounts(),
            'reorder' => $request->boolean('reorder') && $status === 'published' && $photos->count() > 1,
            'maxPerUpload' => StoreGalleryPhotosRequest::MAX_PER_UPLOAD,
        ]);
    }

    public function store(StoreGalleryPhotosRequest $request): RedirectResponse
    {
        try {
            $photos = $this->photoService->storeMany($request->file('images'));
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal mengunggah foto. Tidak ada foto yang tersimpan.');
        }

        return redirect()->route('admin.gallery.index')
            ->with('success', $photos->count().' foto berhasil diunggah. Ubah judul dan keterangannya lewat tombol pensil pada foto.');
    }

    public function edit(GalleryPhoto $photo): View
    {
        return view('admin.gallery.edit', [
            'photo' => $photo,
            'lastChange' => $this->logService->lastChange($photo),
        ]);
    }

    public function update(UpdateGalleryPhotoRequest $request, GalleryPhoto $photo): RedirectResponse
    {
        try {
            $this->photoService->update($photo, $request->safe()->only(['title', 'description', 'is_published']), $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal memperbarui foto.');
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Foto berhasil diperbarui.');
    }

    public function destroy(GalleryPhoto $photo): RedirectResponse
    {
        try {
            $this->photoService->delete($photo);
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus foto.');
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Foto berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request): RedirectResponse
    {
        try {
            $this->photoService->reorder($request->validated('order'));
        } catch (ValidationException $e) {
            return redirect()->route('admin.gallery.index')->with('error', $e->validator->errors()->first('order'));
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menyimpan urutan foto.');
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Urutan foto disimpan.');
    }
}
