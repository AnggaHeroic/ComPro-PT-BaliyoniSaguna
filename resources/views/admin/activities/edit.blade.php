@extends('layouts.app', ['title' => 'Ubah Aktivitas'])

@section('content')
    <div class="space-y-6 min-[1472px]:pl-16">
        <x-admin.page-header eyebrow="Aktivitas Usaha" title="Ubah Aktivitas" :description="$activity->name">
            <x-ui.button :href="route('admin.activities.index', ['category' => $activity->service_category_id])" variant="outline" class="!py-2.5 uppercase tracking-wider">
                <x-ui.icon name="arrow-left" class="size-4" />
                Kembali
            </x-ui.button>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.activities.update', $activity) }}" enctype="multipart/form-data" data-unsaved-guard @if ($errors->any()) data-unsaved-dirty @endif
              class="max-w-3xl border border-black-soft/10 bg-white p-6 sm:p-8">
            @method('PUT')
            @include('admin.activities._form')
        </form>

        <x-admin.danger-zone title="Hapus aktivitas" description="Menghapus aktivitas bersifat permanen dan tidak bisa dibatalkan."
                             :action="route('admin.activities.destroy', $activity)" confirm-title="Hapus aktivitas?" :confirm-message="'Aktivitas '.$activity->name.' akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'" label="Hapus aktivitas" />

        <x-admin.last-changed :log="$lastChange" />
    </div>
@endsection
