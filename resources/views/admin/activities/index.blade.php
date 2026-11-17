@extends('layouts.app', ['title' => 'Aktivitas Usaha'])

@section('content')
    @php
        $filtered = $search !== '' || $status;
        $total = $counts['total'];
        $categoryName = $categoryId ? ($categories->firstWhere('id', $categoryId)?->name) : null;
    @endphp

    <div class="space-y-6">
        <x-admin.page-header eyebrow="Konten" title="Aktivitas Usaha" :description="$categoryName ? 'Kategori: '.$categoryName : 'Kelola aktivitas usaha di setiap kategori.'">
            <x-ui.button :href="route('admin.activities.create', array_filter(['category' => $categoryId]))" class="!py-2.5 uppercase tracking-wider">
                <x-ui.icon name="plus" class="size-4" />
                Tambah Aktivitas
            </x-ui.button>
        </x-admin.page-header>

        @if ($reorder)
            {{-- Mode atur urutan: daftar dipecah per kategori usaha; aktivitas hanya bisa dipindah di dalam kategorinya sendiri. --}}
            <div class="space-y-6" x-data="reorderList">
                <form x-ref="form" method="POST" action="{{ route('admin.activities.reorder') }}" class="hidden">
                    @csrf
                    @method('PUT')
                    <div x-ref="fields"></div>
                </form>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-3 border border-black-soft/10 bg-gray-tint px-4 py-3">
                    <x-admin.reorder-toggle :active="true" :href="route('admin.activities.index', array_filter(['category' => $categoryId]))" @click.prevent="leave($el.href)" />
                    <p class="text-sm text-muted">Seret baris atau pakai tombol panah. Urutan hanya bisa diubah di dalam kategorinya masing-masing, lalu tekan Simpan urutan.</p>
                    <span x-show="dirty" x-cloak class="text-sm font-semibold text-amber-700">Urutan belum disimpan</span>
                    <div class="ml-auto flex items-center gap-2">
                        <x-ui.button :href="route('admin.activities.index', array_filter(['category' => $categoryId]))" variant="outline" class="!py-2 uppercase tracking-wider" @click.prevent="leave($el.href)">Batal</x-ui.button>
                        <x-ui.button type="button" class="!py-2 uppercase tracking-wider" @click="save()" ::disabled="! dirty" ::class="! dirty && 'opacity-50'">Simpan urutan</x-ui.button>
                    </div>
                </div>

                <div x-ref="body" class="select-none space-y-6" @pointerdown="down($event)" @pointermove="drag($event)" @pointerup="drop($event)" @pointercancel="drop($event)">
                    @foreach ($groups as $group)
                        <section class="border border-black-soft/10 bg-white" aria-label="Kategori {{ $group->name }}">
                            <div class="flex items-center gap-3 border-b border-black-soft/10 px-4 py-3">
                                <x-web.service-icon :name="$group->icon" :image="$group->icon_image_url" class="size-6 text-black-soft" />
                                <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-black-soft">{{ $group->name }}</h2>
                                <span class="bg-gray-tint px-2 py-0.5 text-[11px] font-bold text-muted">{{ $group->activities->count() }}</span>
                            </div>

                            <x-admin.table>
                                <x-slot:head>
                                    <th class="px-4 py-3">Pindah</th>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Aktivitas</th>
                                    <th class="px-4 py-3">KBLI</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </x-slot:head>

                                @foreach ($group->activities as $activity)
                                    <tr data-id="{{ $activity->id }}" data-group="{{ $group->id }}">
                                        <td class="px-4 py-3"><x-admin.reorder-handle :label="$activity->name" /></td>
                                        <td class="px-4 py-3 font-semibold text-muted" data-position>{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3">
                                            <p class="font-semibold leading-snug text-black-soft">{{ $activity->name }}</p>
                                            <p class="mt-0.5 font-mono text-xs text-muted">{{ $activity->slug }}</p>
                                        </td>
                                        <td class="px-4 py-3 font-mono text-xs text-muted">{{ $activity->kbli_code ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <x-ui.badge :variant="$activity->is_published ? 'success' : 'warning'">{{ $activity->is_published ? 'Terbit' : 'Draft' }}</x-ui.badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-admin.table>
                        </section>
                    @endforeach
                </div>
            </div>
        @else
            <div class="border border-black-soft/10 bg-white">
                <x-admin.status-tabs :current="$status" :counts="$counts" route="admin.activities.index" :params="['q' => $search, 'category' => $categoryId]" />

                <form method="GET" action="{{ route('admin.activities.index') }}" class="flex flex-wrap items-center gap-3 border-b border-black-soft/10 px-4 py-3">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <div class="w-full sm:w-56">
                        <x-ui.select name="category" :options="$categories->pluck('name', 'id')->all()" :selected="$categoryId" placeholder="Semua kategori" class="!py-2.5" onchange="this.form.submit()" aria-label="Filter kategori usaha" />
                    </div>
                    <x-admin.search :value="$search" placeholder="Cari nama atau KBLI" />
                    @if ($filtered || $categoryId)
                        <a href="{{ route('admin.activities.index') }}" class="text-sm font-semibold text-muted transition-colors hover:text-brand">Reset</a>
                    @endif
                    <span class="text-sm text-muted">{{ $activities->total() }} aktivitas</span>
                    <div class="w-full sm:ml-auto sm:w-auto">
                        <x-admin.reorder-toggle
                            :href="route('admin.activities.index', array_filter(['category' => $categoryId, 'reorder' => 1]))"
                            :disabled="$filtered || $total < 2"
                            :reason="$filtered ? 'Hapus pencarian atau filter status untuk mengatur urutan.' : 'Perlu minimal dua aktivitas.'" />
                    </div>
                </form>

                <x-admin.table>
                    <x-slot:head>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Aktivitas</th>
                        <th class="px-4 py-3">Kategori Usaha</th>
                        <th class="px-4 py-3">KBLI</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </x-slot:head>

                    @forelse ($activities as $activity)
                        <tr class="transition-colors hover:bg-gray-tint/60">
                            <td class="px-4 py-3 font-semibold text-muted">{{ $activity->sort_order }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold leading-snug text-black-soft">{{ $activity->name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-muted">{{ $activity->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $activity->category->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-muted">{{ $activity->kbli_code ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-ui.badge :variant="$activity->is_published ? 'success' : 'warning'">{{ $activity->is_published ? 'Terbit' : 'Draft' }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.activities.edit', $activity) }}" class="inline-flex min-h-9 items-center justify-center bg-black-soft px-3 text-xs font-semibold uppercase tracking-wider text-white transition-colors hover:bg-brand">Ubah</a>
                                    <x-ui.confirm-delete :action="route('admin.activities.destroy', $activity)" title="Hapus aktivitas?" :message="'Aktivitas '.$activity->name.' akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-14 text-center text-muted">
                                @if ($search !== '')
                                    Tidak ada aktivitas yang cocok dengan "{{ $search }}".
                                @elseif ($status)
                                    Tidak ada aktivitas {{ $status === 'draft' ? 'draft' : 'terbit' }}.
                                @else
                                    Belum ada aktivitas. <a href="{{ route('admin.activities.create', array_filter(['category' => $categoryId])) }}" class="font-semibold text-brand hover:underline">Tambah aktivitas pertama</a>.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </x-admin.table>

                @if ($activities->hasPages())
                    <div class="border-t border-black-soft/10 bg-gray-tint px-4 py-3">{{ $activities->links() }}</div>
                @endif
            </div>
        @endif
    </div>
@endsection
