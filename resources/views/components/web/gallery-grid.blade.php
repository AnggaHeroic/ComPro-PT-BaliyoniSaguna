{{-- Props: gallery (daftar foto; memakai title, description, image = ID foto Unsplash), spans (pola ukuran kotak yang diulang per foto; hanya berlaku di layar lebar). Kisi bento: di layar lebar 4 kolom dengan kotak besar, tinggi, dan lebar yang berselang-seling; di bawah itu 2 kolom seragam. Judul selalu tampil di foto; saat foto disorot, panel keterangan naik dan menampilkan deskripsi. Klik foto membuka pratinjau (lightbox, tata letak dan logika zoom diadaptasi dari PT Putra Maju Sukses); perilaku ada di resources/js/gallery-lightbox.js. --}}
{{-- Pola 6 foto mengisi blok 4 kolom x 3 baris tanpa lubang: besar (2x2), kecil, tinggi (1x2), kecil, lebar (2x1), lebar. Blok kedua dicerminkan agar tidak terlihat berulang. Setiap kelipatan 6 foto membentuk blok utuh. --}}
@props(['gallery', 'spans' => [
    'lg:col-span-2 lg:row-span-2', '', 'lg:row-span-2', '', 'lg:col-span-2', 'lg:col-span-2',
    'lg:row-span-2', '', 'lg:col-span-2 lg:row-span-2', '', 'lg:col-span-2', 'lg:col-span-2',
]])
<div x-data="galleryLightbox(@js(array_map(fn (array $photo) => [
    'title' => $photo['title'],
    'description' => $photo['description'] ?? null,
    'src' => 'https://images.unsplash.com/photo-'.$photo['image'].'?auto=format&fit=max&w=1600&q=80',
], $gallery)))"
     @keydown.escape.window="close()" @keydown.arrow-right.window="isOpen && next()" @keydown.arrow-left.window="isOpen && previous()">
    <ul data-reveal-stagger class="grid grid-cols-2 gap-3 sm:gap-4 lg:auto-rows-[13rem] lg:grid-flow-dense lg:grid-cols-4">
        @foreach ($gallery as $photo)
            <li class="min-h-0 {{ $spans[$loop->index % count($spans)] }}">
                <button type="button" @click="open({{ $loop->index }})" aria-haspopup="dialog" aria-label="Lihat foto: {{ $photo['title'] }}"
                        class="group relative block aspect-[4/3] size-full overflow-hidden bg-black-deep text-left lg:aspect-auto">
                    <img src="https://images.unsplash.com/photo-{{ $photo['image'] }}?auto=format&fit=crop&w=900&h=700&q=75" alt="{{ $photo['title'] }}" loading="lazy" referrerpolicy="no-referrer"
                         class="size-full object-cover transition-transform duration-500 group-hover:scale-105">
                    {{-- Gradasi dasar agar judul terbaca; menebal saat disorot. --}}
                    <span class="absolute inset-0 bg-linear-to-t from-black-deep/80 via-transparent to-transparent transition-colors duration-300 group-hover:bg-black-deep/40 group-focus-visible:bg-black-deep/40" aria-hidden="true"></span>
                    {{-- Panel keterangan: judul selalu terlihat; saat disorot atau difokus (keyboard) panel naik karena deskripsi terbuka di bawah judul. --}}
                    <span class="absolute inset-x-0 bottom-0 px-4 pb-4 text-white">
                        <span class="block text-sm font-bold leading-snug sm:text-base">{{ $photo['title'] }}</span>
                        @if (! empty($photo['description']))
                            <span class="grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 group-hover:grid-rows-[1fr] group-focus-visible:grid-rows-[1fr]">
                                <span class="block min-h-0 translate-y-3 overflow-hidden text-xs leading-5 text-white/85 opacity-0 transition-all duration-300 group-hover:mt-1.5 group-hover:translate-y-0 group-hover:opacity-100 group-focus-visible:mt-1.5 group-focus-visible:translate-y-0 group-focus-visible:opacity-100">{{ $photo['description'] }}</span>
                            </span>
                        @endif
                    </span>
                    <span class="absolute inset-x-0 bottom-0 h-0.5 origin-left scale-x-0 bg-brand transition-transform duration-500 group-hover:scale-x-100" aria-hidden="true"></span>
                </button>
            </li>
        @endforeach
    </ul>

    {{-- Pratinjau: latar gelap layar penuh. Roda mouse, ketuk dua kali, tombol, atau cubit untuk zoom; geser saat diperbesar; panah (tombol atau keyboard) untuk berpindah; Esc atau klik latar untuk menutup. --}}
    <div x-show="isOpen" x-cloak x-transition.opacity
         x-effect="document.body.classList.toggle('overflow-hidden', isOpen)"
         role="dialog" aria-modal="true" aria-label="Pratinjau foto galeri"
         class="fixed inset-0 z-[70] flex items-center justify-center bg-black-deep/90 p-4 sm:p-8"
         @click.self="close()">
        <button type="button" @click="close()" aria-label="Tutup pratinjau"
                class="absolute right-5 top-5 z-10 inline-flex size-10 items-center justify-center text-white/70 transition-colors hover:text-white sm:right-8 sm:top-8">
            <x-ui.icon name="close" class="size-6" />
        </button>

        <button type="button" @click.stop="previous()" aria-label="Foto sebelumnya"
                class="absolute left-3 top-1/2 z-10 inline-flex size-11 -translate-y-1/2 items-center justify-center text-white/70 transition-colors hover:text-white sm:left-6">
            <x-ui.icon name="arrow-left" class="size-6" />
        </button>

        <div class="flex max-h-full max-w-[90vw] flex-col items-center" @click.stop>
            {{-- Foto yang bisa diperbesar dan digeser. --}}
            <div class="relative flex max-h-[70vh] max-w-[90vw] items-center justify-center overflow-hidden"
                 @wheel.prevent="handleWheel($event)" @pointerdown="handlePointerDown($event)" @pointermove="handlePointerMove($event)"
                 @pointerup="handlePointerUp($event)" @pointercancel="handlePointerUp($event)" @pointerleave="handlePointerUp($event)"
                 @dblclick.prevent.stop="toggleZoom()">
                <img x-ref="previewImage" :src="current?.src" :alt="current?.title" draggable="false" referrerpolicy="no-referrer"
                     class="max-h-[70vh] max-w-[90vw] select-none object-contain"
                     :class="zoom > 1 ? 'cursor-grab active:cursor-grabbing' : 'cursor-zoom-in'"
                     :style="`transform: translate(${translateX}px, ${translateY}px) scale(${zoom}); transition: ${isDragging ? 'none' : 'transform 200ms ease-out'}; touch-action: none; transform-origin: center center;`">
            </div>

            {{-- Keterangan foto. --}}
            <div class="mt-4 text-center text-white">
                <p class="font-display text-lg font-black sm:text-xl" x-text="current?.title"></p>
                <p x-show="current?.description" class="mx-auto mt-1 max-w-xl text-sm leading-6 text-white/70" x-text="current?.description"></p>
            </div>

            {{-- Kontrol zoom dan penghitung. --}}
            <div class="mt-4 flex items-center gap-4">
                <button type="button" @click="zoomOut()" :disabled="zoom <= minZoom" aria-label="Perkecil foto"
                        class="inline-flex size-9 items-center justify-center text-white/70 transition-colors hover:text-white disabled:pointer-events-none disabled:opacity-30">
                    <x-ui.icon name="zoom-out" class="size-5" />
                </button>
                <button type="button" @click="toggleZoom()" :aria-label="zoom > 1 ? 'Reset zoom' : 'Perbesar foto'"
                        class="min-w-[42px] font-display text-xs font-medium tracking-[0.12em] text-white/70 transition-colors hover:text-white">
                    <span x-text="`${Math.round(zoom * 100)}%`"></span>
                </button>
                <button type="button" @click="zoomIn()" :disabled="zoom >= maxZoom" aria-label="Perbesar foto"
                        class="inline-flex size-9 items-center justify-center text-white/70 transition-colors hover:text-white disabled:pointer-events-none disabled:opacity-30">
                    <x-ui.icon name="zoom-in" class="size-5" />
                </button>
                <span class="h-4 w-px bg-white/20" aria-hidden="true"></span>
                <p class="font-display text-xs font-medium tracking-[0.15em] text-white/60" aria-live="polite" x-text="counter"></p>
            </div>
        </div>

        <button type="button" @click.stop="next()" aria-label="Foto berikutnya"
                class="absolute right-3 top-1/2 z-10 inline-flex size-11 -translate-y-1/2 items-center justify-center text-white/70 transition-colors hover:text-white sm:right-6">
            <x-ui.icon name="arrow-right" class="size-6" />
        </button>
    </div>
</div>
