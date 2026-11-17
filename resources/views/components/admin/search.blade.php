{{-- Props: name (default q), value, placeholder. Kolom pencarian + tombol Cari; pencarian dijalankan saat tombol atau Enter ditekan. Tanda x di kolom mengosongkan teks dan langsung mereset pencarian. Wajib di dalam <form method="GET">. Class tambahan (mis. lebar) diteruskan ke kolom input. --}}
@props(['name' => 'q', 'value' => '', 'placeholder' => 'Cari...'])
<div class="flex min-w-0 flex-1 items-center gap-3 sm:flex-none">
    <div x-data @search="if (! $event.target.value && $event.target.defaultValue) $el.closest('form').requestSubmit()" {{ $attributes->class('min-w-0 flex-1 sm:w-64 sm:flex-none') }}>
        <x-ui.input :name="$name" type="search" icon="search" :value="$value" :placeholder="$placeholder" autocomplete="off" aria-label="Cari" class="!py-2.5" />
    </div>
    <x-ui.button type="submit" variant="dark" class="!py-2.5">Cari</x-ui.button>
</div>
