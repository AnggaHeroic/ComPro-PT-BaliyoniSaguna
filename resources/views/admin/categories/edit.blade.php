@extends('layouts.app', ['title' => 'Ubah Kategori Usaha'])

@section('content')
    <div class="space-y-6">
        <x-admin.page-header eyebrow="Kategori Usaha" title="Ubah Kategori Usaha" :description="$category->name">
            <x-ui.button :href="route('admin.categories.index')" variant="outline" class="!py-2.5 uppercase tracking-wider">
                <x-ui.icon name="arrow-left" class="size-4" />
                Kembali
            </x-ui.button>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" data-unsaved-guard @if ($errors->any()) data-unsaved-dirty @endif
              class="max-w-3xl border border-black-soft/10 bg-white p-6 sm:p-8">
            @method('PUT')
            @include('admin.categories._form')
        </form>

        <x-admin.danger-zone title="Hapus kategori" description="Menghapus kategori bersifat permanen. Kategori yang masih memiliki aktivitas tidak bisa dihapus."
                             :action="route('admin.categories.destroy', $category)" confirm-title="Hapus kategori?" :confirm-message="'Kategori '.$category->name.' akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'" label="Hapus kategori" />

        <x-admin.last-changed :log="$lastChange" />
    </div>
@endsection
