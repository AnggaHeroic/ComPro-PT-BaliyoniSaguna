@extends('layouts.app', ['title' => 'Tambah Aktivitas'])

@section('content')
    <div class="space-y-6 min-[1472px]:pl-16">
        <x-admin.page-header eyebrow="Aktivitas Usaha" title="Tambah Aktivitas" description="Aktivitas baru akan tampil di urutan terakhir pada kategorinya.">
            <x-ui.button :href="route('admin.activities.index', array_filter(['category' => $activity->service_category_id]))" variant="outline" class="!py-2.5 uppercase tracking-wider">
                <x-ui.icon name="arrow-left" class="size-4" />
                Kembali
            </x-ui.button>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.activities.store') }}" enctype="multipart/form-data" data-unsaved-guard @if ($errors->any()) data-unsaved-dirty @endif
              class="max-w-3xl border border-black-soft/10 bg-white p-6 sm:p-8">
            @include('admin.activities._form')
        </form>
    </div>
@endsection
