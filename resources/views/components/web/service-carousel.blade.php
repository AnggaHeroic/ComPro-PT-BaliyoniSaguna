{{-- Props: services (daftar layanan; tiap item berisi name, icon, image), speed (kecepatan geser otomatis dalam px/detik, default 35). Perilaku ada di resources/js/carousel.js. --}}
@props(['services', 'speed' => 35])
<div x-data="carousel({{ $speed }})"
     @keydown.arrow-left.prevent="go(-1)" @keydown.arrow-right.prevent="go(1)"
     @focusin="onFocusIn($event)" @focusout="onFocusOut()"
     @resize.window.debounce.150ms="rebuild()"
     role="region" aria-roledescription="carousel" aria-label="Produk dan layanan perusahaan" tabindex="-1">
    {{-- Jalur: bergeser pelan ke kiri secara berulang; berhenti saat kursor di atasnya, dan bisa digeser dengan scroll mouse. --}}
    <ol x-ref="track" class="no-scrollbar flex gap-4 overflow-x-auto" tabindex="0" aria-label="Daftar layanan, geser untuk melihat lainnya"
        @scroll.passive="onScroll()" @wheel="onWheel($event)"
        @pointerenter="onEnter($event)" @pointerleave="onLeave()"
        @touchstart.passive="touch()" @pointerdown.passive="touch()">
        @foreach ($services as $service)
            <x-web.service-card :service="$service" />
        @endforeach
    </ol>

    {{-- Kontrol manual --}}
    <div class="mt-8 flex justify-center gap-2">
        <button type="button" @click="go(-1)" aria-label="Layanan sebelumnya"
                class="inline-flex size-11 items-center justify-center border border-black-soft/25 bg-white text-black-soft transition-colors hover:bg-black-soft hover:text-white">
            <x-ui.icon name="arrow-right" class="size-5 rotate-180" />
        </button>
        <button type="button" @click="go(1)" aria-label="Layanan berikutnya"
                class="inline-flex size-11 items-center justify-center border border-black-soft/25 bg-white text-black-soft transition-colors hover:bg-black-soft hover:text-white">
            <x-ui.icon name="arrow-right" class="size-5" />
        </button>
    </div>
</div>
