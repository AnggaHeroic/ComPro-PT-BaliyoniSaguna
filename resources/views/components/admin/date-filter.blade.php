{{-- Props: from, to (tanggal Y-m-d aktif atau null), presets (daftar ['label','from','to']), max (tanggal maksimum, hari ini), label (teks rentang aktif untuk tombol). Wajib di dalam <form method="GET">; mengirim dua input hidden: dari dan sampai. --}}
@props(['from' => null, 'to' => null, 'presets' => [], 'max' => null, 'label' => null])
<div class="relative" x-data="dateFilter({ from: @js($from ?? ''), to: @js($to ?? ''), presets: @js($presets), max: @js($max) })"
     @keydown.escape="open = false" @click.outside="open = false">
    <input type="hidden" name="dari" :value="from">
    <input type="hidden" name="sampai" :value="to">

    <button type="button" @click="open = ! open" :aria-expanded="open" aria-haspopup="dialog" title="Filter tanggal" aria-label="Filter tanggal{{ $label ? ': '.$label : '' }}"
            @class([
                'inline-flex min-h-[2.75rem] items-center gap-2 border px-3 text-sm font-semibold transition-colors',
                'border-brand bg-red-50 text-brand' => $label,
                'border-black-soft/20 bg-white text-black-soft hover:bg-gray-tint' => ! $label,
            ])>
        <x-ui.icon name="calendar" class="size-[1.125rem]" />
        @if ($label)
            <span class="whitespace-nowrap">{{ $label }}</span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition.opacity.duration.100ms role="dialog" aria-label="Filter tanggal"
         class="absolute left-0 top-full z-30 mt-2 w-[19.5rem] max-w-[calc(100vw-2rem)] border border-black-soft/15 bg-white p-4 shadow-[0_16px_40px_-12px_rgb(0_0_0/0.35)]">
        <p class="text-sm font-bold text-black-soft">Filter tanggal</p>

        <div class="mt-3 flex flex-wrap gap-1.5">
            <template x-for="item in presets" :key="item.label">
                <button type="button" @click="preset(item)"
                        :class="from === item.from && to === item.to ? 'border-black-soft bg-black-soft text-white' : 'border-black-soft/20 bg-white text-muted hover:bg-gray-tint'"
                        class="border px-2.5 py-1.5 text-xs font-semibold transition-colors" x-text="item.label"></button>
            </template>
        </div>

        <div class="mt-4 inline-flex border border-black-soft/20 p-0.5 text-xs font-semibold" role="tablist" aria-label="Jenis filter">
            <button type="button" role="tab" :aria-selected="mode === 'range'" @click="mode = 'range'" :class="mode === 'range' ? 'bg-black-soft text-white' : 'text-muted hover:bg-gray-tint'" class="px-3 py-1.5 transition-colors">Rentang tanggal</button>
            <button type="button" role="tab" :aria-selected="mode === 'day'" @click="mode = 'day'" :class="mode === 'day' ? 'bg-black-soft text-white' : 'text-muted hover:bg-gray-tint'" class="px-3 py-1.5 transition-colors">1 tanggal</button>
        </div>

        <div class="mt-3 grid gap-3" :class="mode === 'range' && 'grid-cols-2'">
            <label class="block text-xs font-semibold text-muted">
                <span x-text="mode === 'day' ? 'Tanggal' : 'Dari'"></span>
                <input type="date" x-model="from" :max="max" class="mt-1 block w-full border border-black-soft/20 bg-white px-2.5 py-2 text-sm text-black-soft focus:border-black-soft focus:outline-none focus:ring-1 focus:ring-black-soft">
            </label>
            <label class="block text-xs font-semibold text-muted" x-show="mode === 'range'">
                Sampai
                <input type="date" x-model="to" :max="max" class="mt-1 block w-full border border-black-soft/20 bg-white px-2.5 py-2 text-sm text-black-soft focus:border-black-soft focus:outline-none focus:ring-1 focus:ring-black-soft">
            </label>
        </div>

        <div class="mt-4 flex items-center justify-between gap-2">
            <button type="button" x-show="from || to" @click="clear()" class="text-sm font-semibold text-muted transition-colors hover:text-brand">Hapus filter</button>
            <span x-show="! (from || to)"></span>
            <button type="button" @click="apply()" :disabled="! from && ! to"
                    class="bg-brand px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-dark disabled:cursor-not-allowed disabled:bg-black-soft/20">Terapkan</button>
        </div>
    </div>
</div>
