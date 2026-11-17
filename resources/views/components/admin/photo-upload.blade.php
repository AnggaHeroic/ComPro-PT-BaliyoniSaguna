{{-- Props: max (jumlah foto maksimum per unggahan). Area unggah banyak foto (klik atau seret) dengan pratinjau; input file bernama images[]. Wajib di dalam form multipart. --}}
@props(['max' => 10])
<div x-data="photoUpload({ max: {{ $max }} })">
    <label for="images" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="drop($event)"
           :class="dragging ? 'border-brand bg-red-50' : 'border-black-soft/25 bg-gray-tint hover:border-black-soft/50'"
           class="flex cursor-pointer flex-col items-center justify-center gap-2 border-2 border-dashed px-6 py-10 text-center transition-colors">
        <span class="flex size-12 items-center justify-center bg-black-soft text-white"><x-ui.icon name="upload" class="size-6" /></span>
        <span class="text-sm font-bold text-black-soft">Klik untuk memilih foto, atau seret ke sini</span>
        <span class="text-xs text-muted">JPG, PNG, atau WebP. Maksimal {{ $max }} foto per unggahan, 4 MB per foto. Otomatis dikompres menjadi WebP.</span>
    </label>
    <input x-ref="input" type="file" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" @change="pick($event)">

    <div x-show="previews.length" x-cloak class="mt-4">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm font-semibold text-black-soft"><span x-text="previews.length"></span> foto siap diunggah</p>
            <button type="button" @click="clear()" class="text-sm font-semibold text-muted transition-colors hover:text-brand">Kosongkan</button>
        </div>
        <ul class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-5 lg:grid-cols-8">
            <template x-for="item in previews" :key="item.url">
                <li class="aspect-square overflow-hidden bg-gray-tint"><img :src="item.url" :alt="item.name" class="size-full object-cover"></li>
            </template>
        </ul>
    </div>

    @error('images')
        <p class="mt-3 flex items-start gap-2 text-sm font-medium text-brand" role="alert"><span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}</p>
    @enderror
    @error('images.*')
        <p class="mt-3 flex items-start gap-2 text-sm font-medium text-brand" role="alert"><span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}</p>
    @enderror
</div>
