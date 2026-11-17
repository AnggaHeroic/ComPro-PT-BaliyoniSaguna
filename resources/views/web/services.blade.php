@extends('layouts.public', ['transparentNav' => true, 'title' => 'Layanan', 'description' => $content('seo_description')])

@section('content')
    <x-web.page-header variant="center" :data-cms-section="$content->sectionKey('hero')" :title="$content->rich('hero_title', 'text-brand-bright')" :image="$content->image('hero_image')">
        <x-slot:label>{{ $content->rich('hero_label', 'text-brand-bright') }}</x-slot:label>
        <x-slot:scrollTo>#ruang-lingkup</x-slot:scrollTo>
        {{ $content('hero_description') }}
    </x-web.page-header>
    <x-web.service-scope :content="$content" :services="$services" />
    <x-web.cta :company="$company" />
@endsection
