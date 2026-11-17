{{-- Props: photos (koleksi GalleryPhoto terbit, sesuai urutan), reorder (true = mode atur urutan: seluruh foto bisa diseret dan ada tombol panah; false = pratinjau dengan tombol lihat/ubah/hapus). Kisi bento sama dengan halaman publik Galeri (pola ukuran dari GalleryPhoto::BENTO_SPANS). --}}
@props(['photos', 'reorder' => false])
@php
    $spans = \App\Models\GalleryPhoto::BENTO_SPANS;
    $gridClass = 'grid grid-cols-2 gap-3 sm:gap-4 lg:auto-rows-[13rem] lg:grid-flow-dense lg:grid-cols-4';
    $items = $photos->map(fn ($photo) => ['title' => $photo->title, 'description' => $photo->description, 'src' => $photo->image_url])->values();
@endphp

@if ($reorder)
    {{-- Mode atur urutan: seret foto di mana saja (mouse maupun sentuh) atau pakai tombol panah di pojok kiri atas. --}}
    <ul class="{{ $gridClass }}" x-ref="body" data-layout="grid" data-drag-anywhere data-spans='@json($spans)'
        @pointerdown="down($event)" @pointermove="drag($event)" @pointerup="drop($event)" @pointercancel="drop($event)">
        @foreach ($photos as $photo)
            @php($span = $spans[$loop->index % count($spans)])
            <li @class(['relative min-h-0 cursor-grab touch-none select-none overflow-hidden bg-black-deep active:cursor-grabbing', $span]) data-id="{{ $photo->id }}" data-span="{{ $span }}">
                <div class="relative block aspect-[4/3] size-full lg:aspect-auto">
                    <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy" referrerpolicy="no-referrer" draggable="false" class="pointer-events-none size-full object-cover">
                    <span class="pointer-events-none absolute inset-0 bg-linear-to-t from-black-deep/80 via-transparent to-transparent" aria-hidden="true"></span>
                    <span class="pointer-events-none absolute inset-x-0 bottom-0 px-4 pb-4 text-sm font-bold leading-snug text-white sm:text-base">{{ $photo->title }}</span>
                    <span class="absolute left-2 top-2"><x-admin.reorder-handle :label="$photo->title" :overlay="true" /></span>
                    <span class="pointer-events-none absolute right-3 top-3 bg-black-deep/70 px-2 py-0.5 text-[11px] font-bold text-white" data-position>{{ $loop->iteration }}</span>
                </div>
            </li>
        @endforeach
    </ul>
@else
    {{-- Pratinjau: klik foto (atau kaca pembesar di tengahnya) untuk melihat ukuran besar seperti di halaman publik. --}}
    <div x-data="galleryLightbox(@js($items))" @keydown.escape.window="close()" @keydown.arrow-right.window="isOpen && next()" @keydown.arrow-left.window="isOpen && previous()">
        <ul class="{{ $gridClass }}">
            @foreach ($photos as $photo)
                <li class="group relative min-h-0 {{ $spans[$loop->index % count($spans)] }}">
                    <button type="button" @click="open({{ $loop->index }})" aria-haspopup="dialog" aria-label="Lihat foto: {{ $photo->title }}"
                            class="relative block aspect-[4/3] size-full overflow-hidden bg-black-deep text-left lg:aspect-auto">
                        <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy" referrerpolicy="no-referrer" class="size-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <span class="absolute inset-0 bg-linear-to-t from-black-deep/80 via-transparent to-transparent transition-colors duration-300 group-hover:bg-black-deep/40" aria-hidden="true"></span>
                        <span class="absolute inset-x-0 bottom-0 px-4 pb-4 text-sm font-bold leading-snug text-white sm:text-base">{{ $photo->title }}</span>
                        {{-- Kaca pembesar saat disorot (sama seperti pratinjau galeri di PutraMajuSukses): lingkaran putih kecil di tengah dan foto menggelap tipis. --}}
                        <span class="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/0 transition-colors duration-300 group-hover:bg-black/25 group-focus-within:bg-black/25" aria-hidden="true">
                            <span class="flex size-10 scale-90 items-center justify-center rounded-full bg-white/90 text-black-soft opacity-0 shadow-sm transition-all duration-300 group-hover:scale-100 group-hover:opacity-100 group-focus-within:scale-100 group-focus-within:opacity-100">
                                <x-ui.icon name="zoom-in" class="size-5" />
                            </span>
                        </span>
                    </button>

                    <span class="pointer-events-none absolute left-3 top-3 bg-black-deep/70 px-2 py-0.5 text-[11px] font-bold text-white">{{ $loop->iteration }}</span>

                    {{-- Satu bilah aksi: ubah dan hapus. Selalu tampak di layar sentuh; di layar lebar muncul saat disorot. --}}
                    <div class="absolute right-2 top-2 flex divide-x divide-white/20 border border-white/25 bg-black-deep/55 backdrop-blur-sm transition-opacity lg:opacity-0 lg:group-focus-within:opacity-100 lg:group-hover:opacity-100">
                        <a href="{{ route('admin.gallery.edit', $photo) }}" aria-label="Ubah {{ $photo->title }}" title="Ubah" class="inline-flex size-9 items-center justify-center text-white transition-colors hover:bg-white/20"><x-ui.icon name="pencil" class="size-4" /></a>
                        <form method="POST" action="{{ route('admin.gallery.destroy', $photo) }}" data-confirm-title="Hapus foto?" data-confirm-message="Foto {{ $photo->title }} akan dihapus permanen. Tindakan ini tidak dapat dibatalkan." data-confirm-label="Ya, hapus" data-confirm-tone="danger" class="flex">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Hapus {{ $photo->title }}" title="Hapus" class="inline-flex size-9 items-center justify-center text-white transition-colors hover:bg-brand"><x-ui.icon name="trash" class="size-4" /></button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <x-web.gallery-lightbox />
    </div>
@endif
