{{-- Props: name (wajib; field hapus bernama remove_{name}), label, current (URL gambar saat ini, untuk halaman ubah), hint, removeLabel (teks kotak hapus), removable (false = kotak hapus disembunyikan), previewClass (ukuran kotak pratinjau), fit (object-cover|object-contain). Pratinjau gambar sebelum diunggah (Alpine). --}}
@props(['name' => 'image', 'label' => null, 'current' => null, 'hint' => null, 'previewClass' => 'aspect-[4/3] w-full max-w-[14rem]', 'fit' => 'object-cover', 'removeLabel' => 'Hapus gambar saat ini', 'removable' => true])
<div x-data="{
         preview: @js($current),
         remove: false,
         pick(event) {
             const file = event.target.files[0];
             if (file) { this.preview = URL.createObjectURL(file); this.remove = false; }
         },
     }">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-semibold text-black-soft">{{ $label }}</label>
    @endif
    <div class="{{ $label ? 'mt-2' : '' }} flex flex-col gap-4 sm:flex-row sm:items-start">
        <div class="flex {{ $previewClass }} shrink-0 items-center justify-center overflow-hidden border border-black-soft/15 bg-gray-tint">
            <img x-show="preview && ! remove" :src="preview" alt="Pratinjau gambar" class="size-full {{ $fit }}" @if (! $current) x-cloak @endif>
            <span x-show="! preview || remove" @if ($current) x-cloak @endif class="px-3 text-center text-xs text-muted">Belum ada gambar</span>
        </div>
        <div class="min-w-0 flex-1 space-y-3">
            <input type="file" id="{{ $name }}" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" @change="pick($event)"
                   {{ $attributes->class('block w-full text-sm text-muted file:mr-4 file:cursor-pointer file:border-0 file:bg-black-soft file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-brand') }}>
            @if ($hint)
                <p class="text-xs text-muted">{{ $hint }}</p>
            @endif
            @if ($current && $removable)
                <label class="flex w-fit cursor-pointer items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remove_{{ $name }}" value="1" x-model="remove" class="size-4 rounded-sm border-black-soft/30 accent-brand">
                    {{ $removeLabel }}
                </label>
            @endif
            @error($name)
                <p class="flex items-start gap-2 text-sm font-medium text-brand" role="alert">
                    <span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}
                </p>
            @enderror
        </div>
    </div>
</div>
