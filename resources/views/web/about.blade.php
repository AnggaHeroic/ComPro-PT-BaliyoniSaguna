@extends('layouts.public', ['transparentNav' => true, 'title' => 'Tentang Kami', 'description' => $content('seo_description')])

@section('content')
    <x-web.page-header :data-cms-section="$content->sectionKey('hero')" :eyebrow="$content('hero_eyebrow')" :title="$content->rich('hero_title', 'text-brand-bright')" :image="$content->image('hero_image')">
        {{ $content('hero_description') }}
    </x-web.page-header>
    <x-web.about-profile :content="$content" />
    <x-web.vision-mission :content="$content" />
    <x-web.company-values :content="$content" />
    <x-web.cta :company="$company" variant="banner" />
@endsection
