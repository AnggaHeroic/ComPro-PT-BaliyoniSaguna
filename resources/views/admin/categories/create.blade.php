@extends('layouts.app', ['title' => 'Tambah Kategori Usaha'])

@section('content')
    <div class="space-y-6">
        <x-admin.page-header eyebrow="Kategori Usaha" title="Tambah Kategori Usaha" description="Kategori baru akan tampil di urutan terakhir.">
            <x-ui.button :href="route('admin.categories.index')" variant="outline" class="!py-2.5 uppercase tracking-wider">
                <x-ui.icon name="arrow-left" class="size-4" />
                Kembali
            </x-ui.button>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" data-unsaved-guard @if ($errors->any()) data-unsaved-dirty @endif
              class="max-w-3xl border border-black-soft/10 bg-white p-6 sm:p-8">
            @include('admin.categories._form')
        </form>
    </div>
@endsection
