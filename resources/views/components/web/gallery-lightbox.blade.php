{{-- Props: tidak ada. Pratinjau foto layar penuh (zoom, geser, panah). Harus berada di dalam elemen ber-x-data="galleryLightbox(...)" yang menyediakan isOpen, current, open(), close(), next(), dll. Dipakai halaman publik Galeri dan pratinjau galeri di admin. --}}
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
