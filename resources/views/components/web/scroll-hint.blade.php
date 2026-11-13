{{-- Props: href (tujuan gulir, mis. #ruang-lingkup), label (teks aksesibilitas). Petunjuk gulir hero: lingkaran kecil berpanah di tengah bawah yang melompat pelan; menyala merah saat disorot. Sembunyi di layar kecil, dan lompatan dimatikan bila pengguna memilih mengurangi gerakan. Letakkan langsung di dalam <section> hero yang berposisi relative. --}}
@props(['href', 'label' => 'Gulir ke bagian berikutnya'])
<a href="{{ $href }}" aria-label="{{ $label }}"
   class="absolute bottom-8 left-1/2 hidden size-9 -translate-x-1/2 items-center justify-center rounded-full border border-white/40 text-white transition-colors hover:border-brand-bright hover:bg-brand-bright motion-safe:animate-bounce sm:flex">
    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
</a>
