{{-- Props: label, value (angka), hint (keterangan kecil), icon (nama x-ui.icon) --}}
@props(['label', 'value', 'hint' => null, 'icon' => 'dashboard'])
<div class="relative overflow-hidden border border-black-soft/10 bg-white p-5 sm:p-6">
    <span class="absolute left-0 top-0 h-full w-0.5 bg-brand" aria-hidden="true"></span>
    <div class="flex items-start justify-between gap-4">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-muted">{{ $label }}</p>
        <x-ui.icon :name="$icon" class="size-5 text-black-soft/30" />
    </div>
    <p class="mt-4 font-display text-4xl font-black leading-none tracking-tight text-black-soft">{{ $value }}</p>
    @if ($hint)
        <p class="mt-3 text-xs text-muted">{{ $hint }}</p>
    @endif
</div>
