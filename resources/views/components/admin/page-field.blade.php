{{-- Props: fieldKey (kunci kolom), field (definisi dari config/page_contents.php), content (PageContentSet). Satu kolom form isi halaman sesuai jenisnya: text, textarea, title, image, atau cards. --}}
@props(['fieldKey', 'field', 'content'])
@php
    $type = $field['type'] ?? 'text';
    $max = $field['max'] ?? 200;
    $optional = $field['optional'] ?? false;
    $inputName = 'content['.$fieldKey.']';
    $inputId = $inputName; // sama dengan id bawaan x-ui.input dan x-ui.textarea (id = name)
    // Galat validasi memakai kunci bertitik (content.kunci), bukan nama kolom berkurung.
    $messages = collect($errors->getMessages())
        ->filter(fn ($list, $errorKey) => in_array($errorKey, ['content.'.$fieldKey, 'image_'.$fieldKey], true) || str_starts_with($errorKey, 'content.'.$fieldKey.'.'))
        ->flatten()->unique()->all();
    $errorClass = $messages ? '!border-brand' : '';
@endphp

@if ($type === 'image')
    <x-ui.file-upload :name="'image_'.$fieldKey" :label="$field['label']" :current="$content->image($fieldKey)" :removable="$content->customImagePath($fieldKey) !== null"
                      remove-label="Kembalikan ke foto default" :hint="$field['hint'] ?? null" :preview-class="$field['preview_class'] ?? 'aspect-[16/9] w-full max-w-xs'" :fit="$field['fit'] ?? 'object-cover'" />
@elseif ($type === 'cards')
    <div>
        <p class="mb-2 text-sm font-semibold text-black-soft">{{ $field['label'] }}</p>
        <x-admin.card-list :name="$inputName" :items="old('content.'.$fieldKey, $content->cards($fieldKey))" :item-label="$field['item_label'] ?? 'Butir'"
                           :max-title="$field['max_title'] ?? 60" :max-text="$field['max_text'] ?? 300" :min-items="$field['min_items'] ?? 1" :max-items="$field['max_items'] ?? 10" />
        @if (! empty($field['hint']))
            <p class="mt-2 text-xs text-muted">{{ $field['hint'] }}</p>
        @endif
    </div>
@else
    @php($value = old('content.'.$fieldKey, $content($fieldKey)))
    <div x-data="{ n: {{ mb_strlen((string) $value) }} }" @input="n = $event.target.value.length">
        <div class="mb-2 flex items-baseline justify-between gap-3">
            <label for="{{ $inputId }}" class="text-sm font-semibold text-black-soft">
                {{ $field['label'] }}
                @unless ($optional)<span class="text-brand" title="Wajib diisi" aria-hidden="true">*</span>@endunless
            </label>
            <span class="text-xs tabular-nums text-muted" :class="n > {{ (int) ($max * 0.9) }} && 'font-semibold text-amber-600'" x-text="n + '/{{ $max }}'"></span>
        </div>

        @if ($type === 'title')
            <x-admin.title-editor :id="$inputId" :name="$inputName" :value="$value" :max="$max" :rows="$field['rows'] ?? 2" :class="$errorClass" />
        @elseif ($type === 'textarea')
            <x-ui.textarea :name="$inputName" rows="4" maxlength="{{ $max }}" :required="! $optional" :class="$errorClass">{{ $value }}</x-ui.textarea>
        @else
            <x-ui.input :name="$inputName" type="text" :value="$value" maxlength="{{ $max }}" :required="! $optional" :class="$errorClass" />
        @endif

        @if (! empty($field['hint']))
            <p class="mt-2 text-xs text-muted">{{ $field['hint'] }}</p>
        @endif
    </div>
@endif

@foreach ($messages as $message)
    <p class="mt-2 flex items-start gap-2 text-sm font-medium text-brand" role="alert">
        <span class="mt-[0.4rem] h-1.5 w-1.5 shrink-0 bg-brand" aria-hidden="true"></span>{{ $message }}
    </p>
@endforeach
