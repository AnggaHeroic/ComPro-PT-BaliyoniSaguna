@extends('layouts.app', ['title' => 'Kategori Usaha'])

@section('content')
    @php
        $filtered = $search !== '' || $status;
        $total = $reorder ? $categories->count() : $categories->total();
    @endphp

    <div class="space-y-6">
        <x-admin.page-header eyebrow="Konten" title="Kategori Usaha" description="Kelola kategori usaha (produk dan layanan) yang tampil di website.">
            <x-ui.button :href="route('admin.categories.create')" class="!py-2.5 uppercase tracking-wider">
                <x-ui.icon name="plus" class="size-4" />
                Tambah Kategori Usaha
            </x-ui.button>
        </x-admin.page-header>

        <div class="border border-black-soft/10 bg-white" @if ($reorder) x-data="reorderList" @endif>
            @if ($reorder)
                <form x-ref="form" method="POST" action="{{ route('admin.categories.reorder') }}" class="hidden">
                    @csrf
                    @method('PUT')
                    <div x-ref="fields"></div>
                </form>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-3 border-b border-black-soft/10 bg-gray-tint px-4 py-3">
                    <x-admin.reorder-toggle :active="true" :href="route('admin.categories.index')" @click.prevent="leave($el.href)" />
                    <p class="text-sm text-muted">Seret baris atau pakai tombol panah, lalu tekan Simpan urutan.</p>
                    <span x-show="dirty" x-cloak class="text-sm font-semibold text-amber-700">Urutan belum disimpan</span>
                    <div class="ml-auto flex items-center gap-2">
                        <x-ui.button :href="route('admin.categories.index')" variant="outline" class="!py-2 uppercase tracking-wider" @click.prevent="leave($el.href)">Batal</x-ui.button>
                        <x-ui.button type="button" class="!py-2 uppercase tracking-wider" @click="save()" ::disabled="! dirty" ::class="! dirty && 'opacity-50'">Simpan urutan</x-ui.button>
                    </div>
                </div>
            @else
                <x-admin.status-tabs :current="$status" :counts="$counts" route="admin.categories.index" :params="['q' => $search]" />

                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap items-center gap-3 border-b border-black-soft/10 px-4 py-3">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <x-admin.search :value="$search" placeholder="Cari nama atau alamat URL" class="sm:w-72" />
                    @if ($filtered)
                        <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-muted transition-colors hover:text-brand">Reset</a>
                    @endif
                    <span class="text-sm text-muted">{{ $total }} kategori</span>
                    <div class="w-full sm:ml-auto sm:w-auto">
                        <x-admin.reorder-toggle
                            :href="route('admin.categories.index', ['reorder' => 1])"
                            :disabled="$filtered || $total < 2"
                            :reason="$filtered ? 'Hapus pencarian atau filter untuk mengatur urutan.' : 'Perlu minimal dua kategori.'" />
                    </div>
                </form>
            @endif

            <x-admin.table :reorder="$reorder">
                <x-slot:head>
                    @if ($reorder)
                        <th class="px-4 py-3">Pindah</th>
                    @endif
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-center">Aktivitas</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    @unless ($reorder)
                        <th class="px-4 py-3 text-right">Aksi</th>
                    @endunless
                </x-slot:head>

                @forelse ($categories as $category)
                    <tr @class(['transition-colors', 'hover:bg-gray-tint/60' => ! $reorder]) @if ($reorder) data-id="{{ $category->id }}" @endif>
                        @if ($reorder)
                            <td class="px-4 py-3"><x-admin.reorder-handle :label="$category->name" /></td>
                        @endif
                        <td class="px-4 py-3 font-semibold text-muted" data-position>{{ $category->sort_order }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex size-12 shrink-0 items-center justify-center overflow-hidden bg-gray-tint">
                                    @if ($category->image_url)
                                        <img src="{{ $category->image_url }}" alt="" class="size-full object-cover" loading="lazy" referrerpolicy="no-referrer">
                                    @else
                                        <x-web.service-icon :name="$category->icon" :image="$category->icon_image_url" class="size-7 text-black-soft" />
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold leading-snug text-black-soft">{{ $category->name }}</p>
                                    <p class="mt-0.5 font-mono text-xs text-muted">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.activities.index', ['category' => $category->id]) }}" class="inline-flex min-w-8 justify-center bg-gray-tint px-2 py-1 text-xs font-bold text-black-soft transition-colors hover:bg-black-soft hover:text-white" title="Lihat aktivitas">{{ $category->activities_count }}</a>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <x-ui.badge :variant="$category->is_published ? 'success' : 'warning'">{{ $category->is_published ? 'Terbit' : 'Draft' }}</x-ui.badge>
                        </td>
                        @unless ($reorder)
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex min-h-9 items-center justify-center bg-black-soft px-3 text-xs font-semibold uppercase tracking-wider text-white transition-colors hover:bg-brand">Ubah</a>
                                    <x-ui.confirm-delete :action="route('admin.categories.destroy', $category)" title="Hapus kategori?" :message="'Kategori '.$category->name.' akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'" />
                                </div>
                            </td>
                        @endunless
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-14 text-center text-muted">
                            @if ($search !== '')
                                Tidak ada kategori yang cocok dengan "{{ $search }}".
                            @elseif ($status)
                                Tidak ada kategori {{ $status === 'draft' ? 'draft' : 'terbit' }}.
                            @else
                                Belum ada kategori. <a href="{{ route('admin.categories.create') }}" class="font-semibold text-brand hover:underline">Tambah kategori pertama</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </x-admin.table>

            @if (! $reorder && $categories->hasPages())
                <div class="border-t border-black-soft/10 bg-gray-tint px-4 py-3">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
@endsection
