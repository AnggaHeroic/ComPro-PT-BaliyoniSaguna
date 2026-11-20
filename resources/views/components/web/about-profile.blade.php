{{-- Props: content (PageContentSet halaman Tentang Kami: label, judul, paragraf, foto, dan data ringkas diatur admin). Profil lengkap perusahaan; susunan diadaptasi dari halaman Tentang Kami PT Putra Maju Sukses. --}}
@props(['content'])
<section{{ $content->mark('profile') }} id="profil" class="scroll-mt-20 bg-white">
    <div class="container-site">
        <div data-reveal-stagger class="grid gap-12 border-b border-black-soft/10 py-16 sm:py-20 lg:grid-cols-12 lg:gap-20 lg:py-24">
            {{-- Deskripsi perusahaan (kiri): garis merah vertikal menaungi label dan judul. --}}
            <div class="lg:col-span-7">
                <div class="relative pl-7 sm:pl-8">
                    <span class="absolute inset-y-0 left-0 w-0.5 bg-brand" aria-hidden="true"></span>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">{{ $content('profile_label') }}</p>
                    <h2 class="mt-4 max-w-2xl text-balance text-[1.625rem] font-semibold leading-[1.15] tracking-[-0.025em] text-black-soft sm:mt-6 sm:text-4xl sm:leading-[1.08] lg:text-[2.75rem]">
                        {{ $content->rich('profile_title') }}
                    </h2>
                </div>

                <div class="mt-8 max-w-2xl space-y-5 pl-7 text-justify sm:space-y-6 text-sm leading-7 text-muted sm:pl-8 sm:text-base sm:leading-8">
                    @foreach (['profile_paragraph_1', 'profile_paragraph_2', 'profile_paragraph_3'] as $key)
                        @if (filled($content($key)))
                            <p>{{ $content($key) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Foto dan data ringkas (kanan). Foto bawaan masih PLACEHOLDER; admin menggantinya lewat menu Isi Halaman. --}}
            <div class="lg:col-span-5">
                <div class="overflow-hidden bg-gray-tint">
                    <img src="{{ $content->image('profile_image') }}" alt="{{ $content('profile_image_alt') }}" class="h-full min-h-[280px] w-full object-cover" loading="lazy" referrerpolicy="no-referrer">
                </div>
                <dl class="mt-6 divide-y divide-black-soft/10 border border-black-soft/15 bg-white px-7 sm:px-8">
                    @foreach ($content->cards('facts_items') as $fact)
                        <div class="py-5">
                            <dt class="text-[10px] font-semibold uppercase tracking-[0.16em] text-muted">{{ $fact['title'] }}</dt>
                            <dd class="mt-2 text-sm leading-6 text-black-soft">{{ $fact['text'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
</section>
