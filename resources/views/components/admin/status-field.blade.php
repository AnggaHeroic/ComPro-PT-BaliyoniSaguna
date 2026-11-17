{{-- Props: selected (published|draft), name (default status). Dua kartu pilihan: Terbit (hijau) dan Draft (kuning). --}}
@props(['selected' => 'published', 'name' => 'status'])
<fieldset>
    <legend class="text-sm font-semibold text-black-soft">Status</legend>

    <div class="mt-2 grid gap-3 sm:grid-cols-2">
        <label class="cursor-pointer">
            <input type="radio" name="{{ $name }}" value="published" class="peer sr-only" @checked($selected === 'published')>
            <span class="flex h-full flex-col border border-black-soft/20 bg-white p-4 transition-colors hover:border-green-600 hover:bg-green-50 peer-checked:border-green-600 peer-checked:bg-green-50 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-green-600">
                <span class="flex items-center gap-2 text-sm font-bold text-black-soft"><span class="size-2 bg-green-600" aria-hidden="true"></span>Terbit</span>
                <span class="mt-1 text-xs leading-relaxed text-muted">Tampil di website.</span>
            </span>
        </label>

        <label class="cursor-pointer">
            <input type="radio" name="{{ $name }}" value="draft" class="peer sr-only" @checked($selected === 'draft')>
            <span class="flex h-full flex-col border border-black-soft/20 bg-white p-4 transition-colors hover:border-amber-500 hover:bg-amber-50 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-amber-500">
                <span class="flex items-center gap-2 text-sm font-bold text-black-soft"><span class="size-2 bg-amber-500" aria-hidden="true"></span>Draft</span>
                <span class="mt-1 text-xs leading-relaxed text-muted">Disembunyikan dari website. Pakai bila belum siap tayang atau ragu untuk menghapus.</span>
            </span>
        </label>
    </div>

    @error($name)
        <p class="mt-2 flex items-start gap-2 text-sm font-medium text-brand" role="alert">
            <span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}
        </p>
    @enderror
</fieldset>
