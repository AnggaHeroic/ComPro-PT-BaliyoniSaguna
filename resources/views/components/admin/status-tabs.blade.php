{{-- Props: current (status aktif: published|draft|null), counts (array total/published/draft), route (nama route daftar), params (parameter query yang dibawa, mis. q atau category) --}}
@props(['current' => null, 'counts', 'route', 'params' => []])
@php
    $tabs = [
        ['key' => null, 'label' => 'Semua', 'count' => $counts['total']],
        ['key' => 'published', 'label' => 'Terbit', 'count' => $counts['published']],
        ['key' => 'draft', 'label' => 'Draft', 'count' => $counts['draft']],
    ];
@endphp
<nav class="flex flex-wrap gap-1 border-b border-black-soft/10 bg-white px-3" aria-label="Filter status">
    @foreach ($tabs as $tab)
        @php($active = $current === $tab['key'])
        <a href="{{ route($route, array_filter($params + ['status' => $tab['key']], fn ($value) => filled($value))) }}"
           @if ($active) aria-current="page" @endif
           @class([
               '-mb-px inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-3 py-3 text-sm transition-colors',
               'border-brand font-bold text-black-soft' => $active,
               'border-transparent font-semibold text-muted hover:text-black-soft' => ! $active,
           ])>
            {{ $tab['label'] }}
            <span @class(['px-1.5 py-0.5 text-[11px] font-bold', 'bg-black-soft text-white' => $active, 'bg-gray-tint text-muted' => ! $active])>{{ $tab['count'] }}</span>
        </a>
    @endforeach
</nav>
