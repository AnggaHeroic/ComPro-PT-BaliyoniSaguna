@extends('layouts.app', ['title' => 'Dashboard'])

{{-- Variabel: $summary (today, stats berisi route dan params), $company, $recentLogs (ActivityLog terbaru), $pages (slug => label halaman yang bisa diubah, termasuk Umum). --}}
@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <x-admin.page-header eyebrow="{{ $summary['today'] }}" title="Selamat datang, {{ auth()->user()->name }}" description="Ringkasan konten website {{ $company['name'] }}." />

        <section aria-label="Ringkasan" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($summary['stats'] as $stat)
                <x-admin.stat-card :label="$stat['label']" :value="$stat['value']" :hint="$stat['hint']" :icon="$stat['icon']" :href="route($stat['route'], $stat['params'])" />
            @endforeach
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="flex flex-col border border-black-soft/10 bg-white lg:col-span-2" aria-labelledby="aktivitas-terbaru">
                <div class="flex h-14 items-center justify-between border-b border-black-soft/10 px-5 sm:px-6">
                    <h2 id="aktivitas-terbaru" class="text-xs font-bold uppercase tracking-[0.14em] text-black-soft">Aktivitas Terbaru</h2>
                    <a href="{{ route('admin.activity-logs.index') }}" class="text-xs font-semibold text-muted transition-colors hover:text-brand">Lihat semua</a>
                </div>
                <ul class="flex flex-1 flex-col divide-y divide-black-soft/10">
                    @forelse ($recentLogs as $log)
                        @php([$main] = $log->detailLines())
                        <li class="flex flex-1 items-center gap-4 px-5 py-3.5 transition-colors hover:bg-gray-tint/60 sm:px-6">
                            <span class="mt-0.5 inline-flex w-16 shrink-0 justify-center px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider {{ $log->actionClass() }}">{{ $log->actionLabel() }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-black-soft">{{ $main }}</p>
                                <p class="mt-0.5 text-xs text-muted">{{ $log->user?->name ?? 'Sistem' }} &middot; {{ \App\Helpers\DateHelper::local($log->created_at)->diffForHumans() }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="flex flex-1 items-center justify-center px-5 py-10 text-center text-sm text-muted sm:px-6">Belum ada aktivitas yang tercatat.</li>
                    @endforelse
                </ul>
                <p class="flex h-12 items-center border-t border-black-soft/10 bg-gray-tint px-5 text-xs text-muted sm:px-6">Menampilkan {{ $recentLogs->count() }} perubahan terakhir yang dilakukan admin.</p>
            </section>

            <section class="flex flex-col border border-black-soft/10 bg-white" aria-labelledby="pintasan">
                <div class="flex h-14 items-center border-b border-black-soft/10 px-5 sm:px-6">
                    <h2 id="pintasan" class="text-xs font-bold uppercase tracking-[0.14em] text-black-soft">Pintasan Pengelolaan</h2>
                </div>
                <ul class="flex flex-1 flex-col divide-y divide-black-soft/10">
                    @foreach ($pages as $slug => $label)
                        <li class="flex flex-1">
                            <a href="{{ route('admin.pages.edit', $slug) }}" class="group flex w-full items-center justify-between px-5 py-3.5 text-sm font-semibold text-black-soft transition-colors hover:bg-gray-tint hover:text-brand sm:px-6">
                                {{ $label }}
                                <x-ui.icon name="arrow-right" class="size-4 text-black-soft/30 transition-all group-hover:translate-x-1 group-hover:text-brand" />
                            </a>
                        </li>
                    @endforeach
                </ul>
                <p class="flex h-12 items-center border-t border-black-soft/10 bg-gray-tint px-5 text-xs text-muted sm:px-6">Ubah teks, foto, logo, dan kontak halaman publik.</p>
            </section>
        </div>
    </div>
@endsection
