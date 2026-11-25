{{-- Props: tidak ada. Sidebar CMS: tetap di kiri pada desktop, laci geser pada ponsel (dibuka lewat event window "toggle-sidebar" dari topbar). Item tanpa href ("Segera") belum aktif. --}}
@php
    $groups = [
        'Utama' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
        ],
        'Konten' => [
            ['label' => 'Kategori Usaha', 'icon' => 'layers', 'href' => route('admin.categories.index'), 'active' => request()->routeIs('admin.categories.*')],
            ['label' => 'Aktivitas Usaha', 'icon' => 'briefcase', 'href' => route('admin.activities.index'), 'active' => request()->routeIs('admin.activities.*')],
            ['label' => 'Foto Galeri', 'icon' => 'image', 'href' => route('admin.gallery.index'), 'active' => request()->routeIs('admin.gallery.*')],
        ],
        // Pengelolaan teks dan gambar halaman publik, berurutan seperti navbar website (Umum = data bersama semua halaman).
        'Isi Halaman' => [
            ['label' => 'Beranda', 'icon' => 'home', 'href' => route('admin.pages.edit', 'home'), 'active' => request()->is('admin/pages/home')],
            ['label' => 'Tentang Kami', 'icon' => 'info', 'href' => route('admin.pages.edit', 'about'), 'active' => request()->is('admin/pages/about')],
            ['label' => 'Layanan', 'icon' => 'grid', 'href' => route('admin.pages.edit', 'services'), 'active' => request()->is('admin/pages/services')],
            ['label' => 'Galeri', 'icon' => 'image', 'href' => route('admin.pages.edit', 'gallery'), 'active' => request()->is('admin/pages/gallery')],
            ['label' => 'Kontak', 'icon' => 'phone', 'href' => route('admin.pages.edit', 'contact'), 'active' => request()->is('admin/pages/contact')],
            ['label' => 'Umum', 'icon' => 'settings', 'href' => route('admin.pages.edit', 'general'), 'active' => request()->is('admin/pages/general')],
        ],
        // Paling bawah, terpisah dari menu pengelolaan konten.
        'Sistem' => [
            ['label' => 'Riwayat Aktivitas', 'icon' => 'history', 'href' => route('admin.activity-logs.index'), 'active' => request()->routeIs('admin.activity-logs.*')],
        ],
    ];
@endphp
<div x-data="{ open: false }" @toggle-sidebar.window="open = ! open" @keydown.escape.window="open = false"
     x-effect="document.body.classList.toggle('max-lg:overflow-hidden', open)">
    <div x-show="open" x-cloak x-transition.opacity.duration.200ms @click="open = false" class="fixed inset-0 z-40 bg-black-deep/70 backdrop-blur-[2px] lg:hidden" aria-hidden="true"></div>

    <aside id="sidebar-admin"
           :class="open ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-t-4 border-brand bg-black-soft text-white transition-transform duration-300 ease-out lg:w-64 lg:translate-x-0">
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3" aria-label="Dashboard">
                <x-web.logo size="size-9" />
                <x-web.wordmark tone="light" class="!text-[0.95rem]" />
            </a>
            <button type="button" @click="open = false" class="-mr-2 inline-flex size-10 items-center justify-center text-white/60 transition-colors hover:text-white lg:hidden">
                <span class="sr-only">Tutup menu</span>
                <x-ui.icon name="close" class="size-5" />
            </button>
        </div>

        <nav class="scrollbar-dark flex-1 overflow-y-auto overscroll-contain py-6" aria-label="Menu admin">
            @foreach ($groups as $title => $items)
                <p @class(['px-5 text-[11px] font-bold uppercase tracking-[0.25em]', 'mt-8' => ! $loop->first, 'text-brand-bright'])>{{ $title }}</p>
                <ul class="mt-3">
                    @foreach ($items as $item)
                        <li>
                            @if ($item['href'])
                                <a href="{{ $item['href'] }}" @if ($item['active']) aria-current="page" @endif
                                   class="group flex items-center gap-3 border-l-2 border-transparent px-5 py-3 text-sm font-semibold text-white/70 transition-colors hover:text-white aria-[current=page]:border-brand-bright aria-[current=page]:bg-white/[0.06] aria-[current=page]:text-white">
                                    <x-ui.icon :name="$item['icon']" class="size-[1.125rem] group-aria-[current=page]:text-brand-bright" />
                                    {{ $item['label'] }}
                                </a>
                            @else
                                <span class="flex cursor-not-allowed items-center gap-3 border-l-2 border-transparent px-5 py-3 text-sm font-semibold text-white/30" aria-disabled="true">
                                    <x-ui.icon :name="$item['icon']" class="size-[1.125rem]" />
                                    {{ $item['label'] }}
                                    <span class="ml-auto border border-white/15 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-white/40">Segera</span>
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-white/10 p-5">
            <div class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center bg-brand font-display text-sm font-black uppercase" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-white/50">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="flex min-h-10 w-full items-center justify-center gap-2 border border-white/20 text-xs font-semibold uppercase tracking-wider text-white/80 transition-colors hover:border-brand-bright hover:bg-brand hover:text-white">
                    <x-ui.icon name="logout" class="size-4" />
                    Keluar
                </button>
            </form>
        </div>
    </aside>
</div>
