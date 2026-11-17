{{-- Props: name (mis. content[kunci]), items (daftar title + text), itemLabel, maxTitle, maxText, minItems, maxItems. Daftar kartu yang bisa ditambah, dihapus, dan diurutkan; kartu kosong dibuang saat disimpan. --}}
@props(['name', 'items' => [], 'itemLabel' => 'Butir', 'maxTitle' => 60, 'maxText' => 300, 'minItems' => 1, 'maxItems' => 10])
@php($fieldClass = 'block w-full rounded-sm border border-black-soft/20 bg-white px-3 py-2 text-sm text-black-soft transition-colors hover:border-black-soft/40 focus:border-black-soft focus:outline-none focus:ring-1 focus:ring-black-soft')
<div x-data="cardList({ items: @js(array_values((array) $items)), min: {{ $minItems }}, max: {{ $maxItems }} })" class="space-y-3">
    <template x-for="(item, index) in items" :key="index">
        <div class="flex items-start gap-3 border border-black-soft/10 bg-gray-tint p-3">
            <span class="mt-2 w-5 shrink-0 text-center text-xs font-bold text-muted" x-text="index + 1"></span>

            <div class="min-w-0 flex-1 space-y-2">
                <input type="text" :name="`{{ $name }}[${index}][title]`" x-model="item.title" maxlength="{{ $maxTitle }}" placeholder="Judul" aria-label="Judul {{ mb_strtolower($itemLabel) }}" class="{{ $fieldClass }} font-semibold">
                <textarea :name="`{{ $name }}[${index}][text]`" x-model="item.text" rows="3" maxlength="{{ $maxText }}" placeholder="Uraian" aria-label="Uraian {{ mb_strtolower($itemLabel) }}" class="{{ $fieldClass }}"></textarea>
            </div>

            <div class="flex shrink-0 flex-col gap-1">
                <div class="flex gap-1">
                    <button type="button" @click="move(index, index - 1)" :disabled="index === 0" aria-label="Naikkan" class="border border-black-soft/20 bg-white p-1.5 text-muted transition-colors hover:text-black-soft disabled:opacity-30">
                        <x-ui.icon name="chevron-up" class="size-4" />
                    </button>
                    <button type="button" @click="move(index, index + 1)" :disabled="index === items.length - 1" aria-label="Turunkan" class="border border-black-soft/20 bg-white p-1.5 text-muted transition-colors hover:text-black-soft disabled:opacity-30">
                        <x-ui.icon name="chevron-down" class="size-4" />
                    </button>
                </div>
                <button type="button" @click="remove(index)" :disabled="items.length <= min" aria-label="Hapus {{ mb_strtolower($itemLabel) }}" class="inline-flex items-center justify-center gap-1 border border-brand/30 bg-white px-1.5 py-1.5 text-xs font-semibold text-brand transition-colors hover:bg-brand hover:text-white disabled:opacity-30">
                    <x-ui.icon name="trash" class="size-3.5" />
                    Hapus
                </button>
            </div>
        </div>
    </template>

    <x-ui.button variant="outline" @click="add()" x-bind:disabled="items.length >= max" class="!min-h-10 !py-2 disabled:opacity-40">
        <x-ui.icon name="plus" class="size-4" />
        Tambah {{ mb_strtolower($itemLabel) }}
    </x-ui.button>
    <p class="text-xs text-muted">Maksimal {{ $maxItems }} {{ mb_strtolower($itemLabel) }}. {{ $itemLabel }} yang dikosongkan tidak disimpan.</p>
</div>
