{{-- Props: values (daftar nilai perusahaan; tiap item berisi title dan text) --}}
@props(['values'])
<section id="nilai" class="scroll-mt-20 bg-white py-16 sm:py-20 lg:py-28">
    <div class="container-site">
        <x-web.section-title eyebrow="Nilai Perusahaan" title="Prinsip yang Kami Pegang" center>
            Nilai yang menjadi landasan kami dalam bekerja dan melayani.
        </x-web.section-title>

        <div data-reveal-stagger class="mt-10 grid sm:mt-14 sm:grid-cols-2 sm:gap-10 lg:grid-cols-5 lg:gap-8">
            @foreach ($values as $value)
                <div class="relative ml-[1.125rem] border-l-2 border-black-soft/15 pb-9 pl-8 last:border-transparent last:pb-0 sm:ml-0 sm:border-0 sm:border-t-2 sm:border-t-black-soft sm:p-0 sm:pt-6 sm:last:border-t-black-soft">
                    <span class="absolute -left-[1.125rem] top-0 flex size-9 items-center justify-center bg-black-soft font-display text-xs font-black text-white sm:hidden" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <p class="hidden font-display text-sm font-black text-brand sm:block">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                    <h3 class="font-display text-lg font-bold leading-9 text-black-soft sm:mt-3 sm:leading-snug">{{ $value['title'] }}</h3>
                    <p class="mt-1 text-sm leading-7 text-muted sm:mt-3">{{ $value['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
