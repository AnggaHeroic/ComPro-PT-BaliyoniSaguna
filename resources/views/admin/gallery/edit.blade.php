@extends('layouts.app', ['title' => 'Ubah Foto'])

@section('content')
    <div class="space-y-6">
        <x-admin.page-header eyebrow="Foto Galeri" title="Ubah Foto" :description="$photo->title">
            <x-ui.button :href="route('admin.gallery.index')" variant="outline" class="!py-2.5 uppercase tracking-wider">
                <x-ui.icon name="arrow-left" class="size-4" />
                Kembali
            </x-ui.button>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.gallery.update', $photo) }}" enctype="multipart/form-data" data-unsaved-guard @if ($errors->any()) data-unsaved-dirty @endif
              class="max-w-3xl border border-black-soft/10 bg-white p-6 sm:p-8">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <x-ui.input name="title" label="Judul" :value="old('title', $photo->title)" required maxlength="120" />

                <x-ui.textarea name="description" label="Keterangan" rows="3" maxlength="255" hint="Opsional. Tampil saat foto disorot di halaman Galeri.">{{ old('description', $photo->description) }}</x-ui.textarea>

                <x-ui.file-upload name="image" label="Foto" :current="$photo->image_url" hint="Pilih file baru untuk mengganti foto. JPG, PNG, atau WebP, maksimal 4 MB." />

                <x-admin.status-field :selected="old('status', $photo->is_published ? 'published' : 'draft')" />
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-black-soft/10 pt-6">
                <x-ui.button type="submit" class="uppercase tracking-wider">Simpan</x-ui.button>
                <x-ui.button :href="route('admin.gallery.index')" variant="outline" class="uppercase tracking-wider" data-unsaved-skip>Batal</x-ui.button>
            </div>
        </form>

        <x-admin.danger-zone title="Hapus foto" description="Menghapus foto bersifat permanen dan tidak bisa dibatalkan."
                             :action="route('admin.gallery.destroy', $photo)" confirm-title="Hapus foto?" :confirm-message="'Foto '.$photo->title.' akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'" label="Hapus foto" />

        <x-admin.last-changed :log="$lastChange" />
    </div>
@endsection
