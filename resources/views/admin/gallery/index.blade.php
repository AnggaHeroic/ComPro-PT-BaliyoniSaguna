@extends('layouts.app', ['title' => 'Foto Galeri'])

@section('content')
    <div class="space-y-6">
        <x-admin.page-header eyebrow="Konten" title="Foto Galeri" description="Unggah foto dokumentasi dan atur posisinya pada kisi bento halaman Galeri." />

        @unless ($reorder)
            <section class="border border-black-soft/10 bg-white p-6 sm:p-8" aria-labelledby="unggah-foto">
                <h2 id="unggah-foto" class="text-xs font-bold uppercase tracking-[0.14em] text-black-soft">Unggah foto</h2>
                <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <x-admin.photo-upload :max="$maxPerUpload" />
                    <div class="mt-5">
                        <x-ui.button type="submit" class="uppercase tracking-wider">
                            <x-ui.icon name="upload" class="size-4" />
                            Unggah foto
                        </x-ui.button>
                    </div>
                </form>
            </section>
        @endunless

        <section class="border border-black-soft/10 bg-white" aria-label="Foto galeri" @if ($reorder) x-data="reorderList" @endif>
            @if ($reorder)
                <form x-ref="form" method="POST" action="{{ route('admin.gallery.reorder') }}" class="hidden">
                    @csrf
                    @method('PUT')
                    <div x-ref="fields"></div>
                </form>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-3 border-b border-black-soft/10 bg-gray-tint px-4 py-3 sm:px-6">
                    <x-admin.reorder-toggle :active="true" :href="route('admin.gallery.index')" @click.prevent="leave($el.href)" />
                    <p class="text-sm text-muted">Seret foto ke posisi baru, atau pakai tombol panah di pojok foto, lalu tekan Simpan urutan.</p>
                    <span x-show="dirty" x-cloak class="text-sm font-semibold text-amber-700">Urutan belum disimpan</span>
                    <div class="ml-auto flex items-center gap-2">
                        <x-ui.button :href="route('admin.gallery.index')" variant="outline" class="!py-2 uppercase tracking-wider" @click.prevent="leave($el.href)">Batal</x-ui.button>
                        <x-ui.button type="button" class="!py-2 uppercase tracking-wider" @click="save()" ::disabled="! dirty" ::class="! dirty && 'opacity-50'">Simpan urutan</x-ui.button>
                    </div>
                </div>
            @else
                {{-- Tab status: Terbit (pratinjau kisi bento) dan Draft (daftar). --}}
                <nav class="flex flex-wrap items-center gap-1 border-b border-black-soft/10 px-3" aria-label="Status foto">
                    @foreach (['published' => ['Terbit', $counts['published']], 'draft' => ['Draft', $counts['draft']]] as $key => [$label, $count])
                        <a href="{{ route('admin.gallery.index', $key === 'draft' ? ['status' => 'draft'] : []) }}" @if ($status === $key) aria-current="page" @endif
                           @class([
                               '-mb-px inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-3 py-3 text-sm transition-colors',
                               'border-brand font-bold text-black-soft' => $status === $key,
                               'border-transparent font-semibold text-muted hover:text-black-soft' => $status !== $key,
                           ])>
                            {{ $label }}
                            <span @class(['px-1.5 py-0.5 text-[11px] font-bold', 'bg-black-soft text-white' => $status === $key, 'bg-gray-tint text-muted' => $status !== $key])>{{ $count }}</span>
                        </a>
                    @endforeach

                    @if ($status === 'published')
                        <div class="ml-auto py-2 pr-3">
                            <x-admin.reorder-toggle :href="route('admin.gallery.index', ['reorder' => 1])" :disabled="$counts['published'] < 2" reason="Perlu minimal dua foto terbit." />
                        </div>
                    @endif
                </nav>
            @endif

            @if ($status === 'published')
                <div class="p-4 sm:p-6">
                    @if ($photos->isEmpty())
                        <p class="py-14 text-center text-muted">Belum ada foto terbit. Unggah foto lewat formulir di atas.</p>
                    @else
                        <x-admin.gallery-bento :photos="$photos" :reorder="$reorder" />
                        @unless ($reorder)
                            <p class="mt-4 text-xs text-muted">Susunan dan ukuran kotak sama dengan halaman publik Galeri. Klik foto untuk melihat ukuran besar.</p>
                        @endunless
                    @endif
                </div>
            @else
                @if ($drafts->isEmpty())
                    <p class="px-4 py-14 text-center text-sm text-muted sm:px-6">Tidak ada foto draft. Foto yang statusnya diubah menjadi Draft akan tercantum di sini.</p>
                @else
                    <x-admin.table>
                        <x-slot:head>
                            <th class="px-4 py-3">Foto</th>
                            <th class="px-4 py-3">Diperbarui</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </x-slot:head>

                        @foreach ($drafts as $draft)
                            <tr class="transition-colors hover:bg-gray-tint/60">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="size-12 shrink-0 overflow-hidden bg-gray-tint">
                                            <img src="{{ $draft->image_url }}" alt="" loading="lazy" referrerpolicy="no-referrer" class="size-full object-cover">
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-semibold leading-snug text-black-soft">{{ $draft->title }}</p>
                                            @if ($draft->description)
                                                <p class="mt-0.5 line-clamp-1 text-xs text-muted">{{ $draft->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-muted">{{ $draft->updated_at->locale('id')->translatedFormat('j M Y, H:i') }}</td>
                                <td class="px-4 py-3 text-center"><x-ui.badge variant="warning">Draft</x-ui.badge></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.gallery.edit', $draft) }}" class="inline-flex min-h-9 items-center justify-center bg-black-soft px-3 text-xs font-semibold uppercase tracking-wider text-white transition-colors hover:bg-brand">Ubah</a>
                                        <x-ui.confirm-delete :action="route('admin.gallery.destroy', $draft)" title="Hapus foto?" :message="'Foto '.$draft->title.' akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-admin.table>
                    <p class="border-t border-black-soft/10 bg-gray-tint px-4 py-3 text-xs text-muted sm:px-6">Foto draft tidak tampil di website. Ubah statusnya menjadi Terbit lewat tombol Ubah.</p>
                @endif
            @endif
        </section>
    </div>
@endsection
