{{-- Props: id, name, value, max, rows. Kolom judul dengan kata berwarna: blok kata lalu tekan "Warnai merah"; disimpan sebagai teks dengan penanda *kata*. --}}
@props(['id', 'name', 'value' => '', 'max' => 200, 'rows' => 2])
<div x-data="titleEditor({ text: @js((string) $value) })">
    <div class="mb-2 flex flex-wrap items-center gap-x-3 gap-y-1">
        <button type="button" @click="toggle()" @mousedown.prevent
                class="inline-flex items-center gap-2 border border-black-soft/20 bg-white px-3 py-1.5 text-xs font-semibold text-brand transition-colors hover:border-brand hover:bg-brand/5">
            <span class="size-3 bg-brand" aria-hidden="true"></span>
            Warnai merah
        </button>
        <span class="text-xs text-muted">Blok kata lalu klik tombol. Klik lagi pada kata berwarna untuk melepas.</span>
    </div>

    <textarea x-ref="field" id="{{ $id }}" name="{{ $name }}" x-model="text" rows="{{ $rows }}" maxlength="{{ $max }}" required
              {{ $attributes->class('block w-full rounded-sm border border-black-soft/20 bg-gray-tint px-4 py-3 text-base text-black-soft transition-colors hover:border-black-soft/40 focus:border-black-soft focus:bg-white focus:outline-none focus:ring-1 focus:ring-black-soft sm:text-sm') }}></textarea>

    <p x-show="needsSelection" x-cloak class="mt-2 text-xs text-amber-700">Blok dulu kata yang ingin diwarnai.</p>
    <p x-show="! balanced" x-cloak class="mt-2 text-xs font-medium text-brand">Tanda * harus berpasangan.</p>

    <div class="mt-3 border border-dashed border-black-soft/20 bg-white px-4 py-3">
        <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider text-muted">Hasil judul</p>
        <p class="font-display text-lg font-bold leading-tight text-black-soft" x-html="preview"></p>
    </div>
</div>
