{{-- Props: href (tujuan saat saklar ditekan), active (mode atur urutan menyala), disabled, reason (alasan saat nonaktif). Tampil sebagai saklar. --}}
@props(['href' => '#', 'active' => false, 'disabled' => false, 'reason' => null])
@if ($disabled)
    <span role="switch" aria-checked="false" aria-disabled="true" title="{{ $reason }}" class="inline-flex cursor-not-allowed items-center gap-2 text-sm font-semibold text-muted/50">
        <span class="relative h-5 w-9 bg-black-soft/10"><span class="absolute left-0.5 top-0.5 size-4 bg-white"></span></span>
        Atur urutan
    </span>
@else
    <a {{ $attributes }} href="{{ $href }}" role="switch" aria-checked="{{ $active ? 'true' : 'false' }}"
       title="{{ $active ? 'Matikan mode atur urutan' : 'Aktifkan mode atur urutan (seret baris untuk mengubah urutan)' }}"
       class="group inline-flex items-center gap-2 text-sm font-semibold text-black-soft transition-colors hover:text-brand">
        <span @class(['relative h-5 w-9 transition-colors', 'bg-brand' => $active, 'bg-black-soft/25 group-hover:bg-black-soft/40' => ! $active])>
            <span @class(['absolute top-0.5 size-4 bg-white shadow-sm transition-all', 'left-[1.125rem]' => $active, 'left-0.5' => ! $active])></span>
        </span>
        Atur urutan
    </a>
@endif
