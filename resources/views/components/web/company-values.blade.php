{{-- Props: values (daftar nilai perusahaan; tiap item berisi title dan text) --}}
@props(['values'])
<section id="nilai" class="scroll-mt-20 bg-white py-20 lg:py-28">
    <div class="container-site">
        <x-web.section-title eyebrow="Nilai Perusahaan" title="Prinsip yang Kami Pegang" center>
            Nilai yang menjadi landasan kami dalam bekerja dan melayani.
        </x-web.section-title>

        <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-5 lg:gap-8">
            @foreach ($values as $value)
                <div class="border-t-2 border-black-soft pt-6">
                    <p class="font-display text-sm font-black text-brand">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                    <h3 class="mt-3 font-display text-lg font-bold leading-snug text-black-soft">{{ $value['title'] }}</h3>
                    <p class="mt-3 text-sm leading-7 text-muted">{{ $value['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
