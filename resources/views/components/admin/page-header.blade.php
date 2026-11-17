{{-- Props: title (wajib), description (opsional), eyebrow (teks kecil merah di atas judul). Slot = tombol aksi di kanan. --}}
@props(['title', 'description' => null, 'eyebrow' => null])
<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        @if ($eyebrow)
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand">{{ $eyebrow }}</p>
        @endif
        <h1 class="{{ $eyebrow ? 'mt-3' : '' }} text-2xl font-black leading-tight tracking-tight text-black-soft sm:text-3xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 text-sm leading-relaxed text-muted">{{ $description }}</p>
        @endif
    </div>
    @if (! $slot->isEmpty())
        <div class="flex shrink-0 flex-wrap gap-3">{{ $slot }}</div>
    @endif
</div>
