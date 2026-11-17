{{-- Dipakai bersama oleh create dan edit. Variabel: $category (model; kosong saat tambah). Tag <form> ada di create/edit. --}}
@csrf
<div class="space-y-6">
    <x-ui.input name="name" label="Nama kategori" :value="old('name', $category->name)" required maxlength="120" placeholder="Mis. Peralatan Elektronik" />

    <x-ui.textarea name="description" label="Deskripsi singkat" rows="3" maxlength="2000" hint="Tampil di kartu kategori dan menu Layanan.">{{ old('description', $category->description) }}</x-ui.textarea>

    <div class="grid gap-6 sm:grid-cols-2">
        <x-ui.file-upload name="icon_image" label="Ikon kustom (opsional)" :current="$category->icon_image_url" previewClass="size-24" fit="object-contain"
                          hint="Jika diunggah, menggantikan ikon dari daftar. Pakai gambar persegi dengan latar transparan (PNG/WebP)." />
        <x-admin.icon-picker name="icon" :selected="old('icon', $category->icon ?? array_key_first(\App\Models\ServiceCategory::ICONS))" />
    </div>

    <x-ui.file-upload name="image" label="Gambar" :current="$category->image_url" hint="JPG, PNG, atau WebP, maksimal 4 MB. Otomatis dikompres menjadi WebP." />

    <x-admin.status-field :selected="old('status', $category->is_published ? 'published' : 'draft')" />
</div>

<div class="mt-8 flex flex-wrap items-center gap-3 border-t border-black-soft/10 pt-6">
    <x-ui.button type="submit" class="uppercase tracking-wider">Simpan</x-ui.button>
    <x-ui.button :href="route('admin.categories.index')" variant="outline" class="uppercase tracking-wider" data-unsaved-skip>Batal</x-ui.button>
</div>
