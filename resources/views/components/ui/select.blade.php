{{-- Props: name (wajib), label, hint, options (array nilai => teks), selected (nilai terpilih), placeholder (teks opsi kosong; null = tanpa opsi kosong). Atribut lain diteruskan ke <select>. --}}
@props(['name', 'label' => null, 'hint' => null, 'options' => [], 'selected' => null, 'placeholder' => null])
<div>
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-semibold text-black-soft">{{ $label }}</label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}"
            @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes->class([
                'block w-full rounded-sm border bg-gray-tint px-4 py-3 text-base text-black-soft transition-colors focus:bg-white focus:outline-none focus-visible:outline-none sm:py-2.5 sm:text-sm',
                $label ? 'mt-2' : '',
                $errors->has($name)
                    ? 'border-brand focus:border-brand focus:ring-1 focus:ring-brand'
                    : 'border-black-soft/20 hover:border-black-soft/40 focus:border-black-soft focus:ring-1 focus:ring-black-soft',
            ]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
    @if ($hint)
        <p class="mt-2 text-xs text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="mt-2 flex items-start gap-2 text-sm font-medium text-brand" role="alert">
            <span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}
        </p>
    @enderror
</div>
