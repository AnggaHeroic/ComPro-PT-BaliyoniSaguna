{{-- Props: tidak ada. Popup di tengah halaman untuk flash session "success" (hijau) dan "error" (merah); ukuran dan susunannya sama dengan dialog konfirmasi (x-ui.confirm-dialog). Animasi sederhana: panel naik sambil memudar, ikon membesar sebentar. Popup sukses tertutup sendiri setelah beberapa detik; popup gagal menunggu ditutup. Bisa ditutup lewat tombol, Esc, atau klik latar. Cukup dipasang sekali di layout. --}}
@php
    $type = session()->has('error') ? 'error' : (session()->has('success') ? 'success' : null);
    $isError = $type === 'error';
@endphp
@if ($type)
    <div x-data="{ show: true }" @if (! $isError) x-init="setTimeout(() => show = false, 3500)" @endif
         x-show="show" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @keydown.escape.window="show = false" data-flash-popup="{{ $type }}"
         class="fixed inset-0 z-[90] flex items-center justify-center p-4" role="alertdialog" aria-modal="true" aria-labelledby="flash-title" aria-describedby="flash-message">
        <div @click="show = false" class="absolute inset-0 bg-black-deep/60 backdrop-blur-[2px]" aria-hidden="true"></div>

        <div class="flash-panel relative w-full max-w-md overflow-hidden border-t-4 bg-white shadow-[0_24px_60px_-12px_rgb(0_0_0/0.5)] {{ $isError ? 'border-brand' : 'border-green-600' }}">
            <div class="flex gap-4 p-6 sm:p-7">
                <span class="flash-icon flex size-11 shrink-0 items-center justify-center text-white {{ $isError ? 'bg-brand' : 'bg-green-600' }}" aria-hidden="true">
                    <x-ui.icon :name="$isError ? 'warning' : 'check'" class="size-5" />
                </span>
                <div class="min-w-0 pt-0.5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] {{ $isError ? 'text-brand' : 'text-green-700' }}">{{ $isError ? 'Terjadi kendala' : 'Perubahan tersimpan' }}</p>
                    <h2 id="flash-title" class="mt-1 font-display text-xl font-black leading-tight tracking-tight text-black-soft">{{ $isError ? 'Gagal' : 'Berhasil' }}</h2>
                    <p id="flash-message" class="mt-2 text-sm leading-6 text-muted">{{ session($type) }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 border-t border-black-soft/10 bg-gray-tint px-6 py-4 sm:px-7">
                <p class="text-xs text-muted">{{ $isError ? 'Periksa isian lalu coba lagi.' : 'Menutup otomatis.' }}</p>
                <button type="button" @click="show = false"
                        class="inline-flex min-h-10 items-center justify-center px-6 text-xs font-semibold uppercase tracking-wider text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-black-soft focus-visible:ring-offset-2 {{ $isError ? 'bg-brand hover:bg-brand-dark' : 'bg-green-600 hover:bg-green-700' }}">Tutup</button>
            </div>

            @unless ($isError)
                <span class="toast-progress absolute inset-x-0 bottom-0 h-0.5 origin-left bg-green-600" style="animation-duration: 3.5s" aria-hidden="true"></span>
            @endunless
        </div>
    </div>
@endif
