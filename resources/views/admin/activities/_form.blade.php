{{-- Dipakai bersama oleh create dan edit. Variabel: $activity (model; kosong saat tambah), $categories (pilihan kategori). Tag <form> ada di create/edit. --}}
@csrf
<div class="space-y-6">
    <x-ui.select name="service_category_id" label="Kategori Usaha" :options="$categories->pluck('name', 'id')->all()" :selected="old('service_category_id', $activity->service_category_id)" placeholder="Pilih kategori" required />

    <x-ui.input name="name" label="Nama aktivitas" :value="old('name', $activity->name)" required maxlength="255" placeholder="Mis. Penjualan laptop, komputer, dan printer" />

    <x-ui.textarea name="summary" label="Ringkasan" rows="2" maxlength="500" hint="Maksimal 500 karakter. Tampil di kartu aktivitas dan bagian atas halaman detail.">{{ old('summary', $activity->summary) }}</x-ui.textarea>

    <x-ui.textarea name="detail" label="Detail" rows="8" maxlength="10000" hint="Pisahkan paragraf dengan satu baris kosong. Jika dikosongkan, halaman detail menampilkan teks contoh.">{{ old('detail', $activity->detail) }}</x-ui.textarea>

    <x-ui.file-upload name="image" label="Gambar" :current="$activity->image_url" hint="Opsional. Jika kosong, halaman detail memakai gambar kategori. JPG, PNG, atau WebP, maksimal 4 MB." />

    <x-admin.status-field :selected="old('status', $activity->is_published ? 'published' : 'draft')" />
</div>

<div class="mt-8 flex flex-wrap items-center gap-3 border-t border-black-soft/10 pt-6">
    <x-ui.button type="submit" class="uppercase tracking-wider">Simpan</x-ui.button>
    <x-ui.button :href="route('admin.activities.index', array_filter(['category' => $activity->service_category_id]))" variant="outline" class="uppercase tracking-wider" data-unsaved-skip>Batal</x-ui.button>
</div>
