@extends('layouts.app', ['title' => 'Riwayat Aktivitas'])

@section('content')
    @php
        $filtered = filled($filters['q'] ?? null) || filled($filters['menu'] ?? null) || filled($filters['action'] ?? null) || filled($filters['dari'] ?? null) || filled($filters['sampai'] ?? null);
    @endphp

    <div class="space-y-6">
        <x-admin.page-header eyebrow="Utama" title="Riwayat Aktivitas" description="Catatan perubahan konten dan akses akun admin di dashboard, dari yang terbaru." />

        <div class="border border-black-soft/10 bg-white">
            {{-- Jenis catatan: semua, perubahan konten, atau akses akun. Tab membawa pencarian dan tanggal; menu dan aksi dikosongkan karena pilihannya beda per jenis. --}}
            <nav class="flex flex-wrap gap-1 border-b border-black-soft/10 px-3" aria-label="Jenis catatan">
                @foreach ($groups as $key => $item)
                    <a href="{{ route('admin.activity-logs.index', array_filter(['jenis' => $key === $defaultGroup ? null : $key, 'q' => $filters['q'] ?? null, 'dari' => $filters['dari'] ?? null, 'sampai' => $filters['sampai'] ?? null], fn ($value) => filled($value))) }}"
                       @if ($group === $key) aria-current="page" @endif
                       @class([
                           '-mb-px inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-3 py-3 text-sm transition-colors',
                           'border-brand font-semibold text-black-soft' => $group === $key,
                           'border-transparent font-medium text-muted hover:text-black-soft' => $group !== $key,
                       ])>
                        {{ $item['label'] }}
                        <span class="bg-gray-tint px-1.5 py-0.5 text-[11px] font-semibold text-muted">{{ $counts[$key] }}</span>
                    </a>
                @endforeach
            </nav>

            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap items-center gap-3 border-b border-black-soft/10 px-4 py-3">
                @if ($group !== $defaultGroup)
                    <input type="hidden" name="jenis" value="{{ $group }}">
                @endif
                @if ($menus !== [])
                <div class="w-full sm:w-44">
                    <x-ui.select name="menu" :options="array_combine($menus, $menus)" :selected="$filters['menu'] ?? null" placeholder="Semua menu" class="!py-2.5" onchange="this.form.submit()" aria-label="Filter menu" />
                </div>
                @endif
                <div class="w-full sm:w-40">
                    <x-ui.select name="action" :options="$actions" :selected="$filters['action'] ?? null" placeholder="Semua aksi" class="!py-2.5" onchange="this.form.submit()" aria-label="Filter aksi" />
                </div>
                <x-admin.search :value="$filters['q'] ?? ''" placeholder="Cari keterangan atau nama pengguna" />

                <x-admin.date-filter :from="$filters['dari'] ?? null" :to="$filters['sampai'] ?? null" :presets="$datePresets" :max="now()->toDateString()" :label="$rangeLabel" />

                <div class="flex items-center gap-3 sm:ml-auto">
                    @if ($filtered)
                        <a href="{{ route('admin.activity-logs.index', $group === $defaultGroup ? [] : ['jenis' => $group]) }}" class="text-sm font-semibold text-muted transition-colors hover:text-brand">Reset</a>
                    @endif
                    <span class="text-sm text-muted">{{ $logs->total() }} catatan</span>
                </div>
            </form>

            <x-admin.table>
                <x-slot:head>
                    <th class="px-4 py-3 text-center">Tanggal &amp; jam</th>
                    <th class="px-4 py-3 text-center">Oleh</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                    <th class="px-4 py-3 text-center">Menu</th>
                    <th class="px-4 py-3 text-center">Keterangan</th>
                </x-slot:head>

                @forelse ($logs as $log)
                    @php($time = \App\Helpers\DateHelper::local($log->created_at))
                    @php([$main, $note, $detail] = $log->detailLines())
                    <tr class="transition-colors hover:bg-gray-tint/60">
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <p class="font-semibold text-black-soft">{{ $time->translatedFormat('d M Y') }}</p>
                            <p class="mt-0.5 text-xs tabular-nums text-muted">{{ $time->format('H:i:s') }}</p>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center font-semibold text-black-soft">{{ $log->user?->name ?? 'Sistem' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider {{ $log->actionClass() }}">
                                <svg class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $log->actionIcon() }}"/></svg>
                                {{ $log->actionLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex bg-gray-tint px-2 py-0.5 text-xs font-semibold text-muted">{{ $log->subjectName() }}</span>
                        </td>
                        <td class="w-full min-w-64 max-w-0 px-4 py-3 text-center">
                            <p class="truncate text-black-soft" title="{{ $main }}">{{ $main }}</p>
                            @if ($note)
                                <p class="mt-0.5 truncate text-xs text-muted" title="{{ $note }}">{{ $note }}</p>
                            @endif
                            @if ($detail)
                                <x-admin.log-detail :detail="$detail" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-14 text-center text-muted">
                            {{ $filtered ? 'Tidak ada catatan yang cocok dengan penyaringan ini.' : 'Belum ada aktivitas yang tercatat.' }}
                        </td>
                    </tr>
                @endforelse
            </x-admin.table>

            @if ($logs->hasPages())
                <div class="border-t border-black-soft/10 bg-gray-tint px-4 py-3">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
@endsection
