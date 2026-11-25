{{-- Props: url (rute pratinjau), section (nama target pratinjau), tab (kunci tab/bagian form yang memuatnya; default = section), maxHeight (batas tinggi kotak dalam px; isi yang lebih panjang dipotong). Pratinjau satu bagian halaman publik dengan isian form yang belum disimpan; dikirim lewat form ke iframe, bukan fetch. --}}
@props(['url', 'section', 'tab' => null, 'maxHeight' => null])
<div x-data="sectionPreview({ url: @js($url), section: @js($section), tab: @js($tab ?? $section), maxHeight: @js($maxHeight) })"
     x-effect="active === @js($tab ?? $section) && ! requested && schedule(0)"
     class="mb-8 border border-black-soft/10 bg-white">
    <div class="flex flex-col gap-3 border-b border-black-soft/10 bg-gray-tint px-4 py-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <h3 class="text-sm font-bold text-black-soft">Pratinjau di website</h3>
                <span x-show="dirty" x-cloak class="inline-flex items-center gap-1.5 bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">
                    <span class="size-1.5 rounded-full bg-amber-500" aria-hidden="true"></span>
                    Belum disimpan
                </span>
            </div>
            <p class="mt-0.5 text-xs leading-5 text-muted">Begini tampilan bagian ini di halaman publik. Pratinjau ikut berubah saat Anda mengetik, tetapi baru tampil di website setelah Simpan.</p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <button type="button" @click="schedule(0)" class="border border-black-soft/20 bg-white px-3 py-1.5 text-xs font-semibold text-black-soft transition-colors hover:bg-gray-tint">Muat ulang</button>
            <button type="button" @click="open = ! open" :aria-expanded="open" class="border border-black-soft/20 bg-white px-3 py-1.5 text-xs font-semibold text-black-soft transition-colors hover:bg-gray-tint" x-text="open ? 'Sembunyikan' : 'Tampilkan'"></button>
        </div>
    </div>

    <div x-show="open" x-ref="box" class="group/preview relative overflow-hidden bg-gray-tint" :style="{ height: boxHeight + 'px' }">
        <iframe x-ref="frame" name="preview-{{ $tab ?? $section }}-{{ $section }}" title="Pratinjau bagian" tabindex="-1" @load="measure()"
                class="pointer-events-none absolute left-0 top-0 w-[1280px] origin-top-left border-0 bg-white" :style="{ transform: `scale(${scale})` }"></iframe>

        <div x-show="cropped" x-cloak class="pointer-events-none absolute inset-x-0 top-0 flex h-14 items-start justify-center bg-linear-to-b from-black-soft/70 to-transparent pt-2.5 text-[11px] font-semibold text-white opacity-0 transition-opacity duration-200 group-hover/preview:opacity-100">Pratinjau dipotong agar tidak terlalu panjang</div>

        <div x-show="loading" x-cloak class="absolute inset-0 flex items-center justify-center bg-white/70 text-xs font-semibold text-muted">Menyiapkan pratinjau…</div>
    </div>

    <p x-show="open" class="border-t border-black-soft/10 bg-gray-tint px-4 py-2.5 text-[11px] leading-4 text-muted">Foto yang baru Anda pilih tampil di website setelah Anda menekan Simpan.</p>
</div>
