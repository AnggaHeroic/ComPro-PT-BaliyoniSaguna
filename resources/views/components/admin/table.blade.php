{{-- Props: reorder (true = mode atur urutan; <tbody> dipasangi penangan seret dari Alpine reorderList). Slot bernama "head" = isi <tr> header (<th>), slot utama = baris <tr> tabel. --}}
@props(['reorder' => false])
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-black-soft/10 text-sm">
        <thead class="bg-gray-tint text-left text-[11px] font-bold uppercase tracking-[0.14em] text-muted">
            <tr>{{ $head }}</tr>
        </thead>
        <tbody {{ $attributes->class(['divide-y divide-black-soft/10', 'select-none' => $reorder]) }}
               @if ($reorder) x-ref="body" @pointerdown="down($event)" @pointermove="drag($event)" @pointerup="drop($event)" @pointercancel="drop($event)" @endif>{{ $slot }}</tbody>
    </table>
</div>
