@extends('layouts.public', ['transparentNav' => true, 'description' => $content('seo_description')])

@section('content')
    <x-web.hero :content="$content" />
    <x-web.about :content="$content" :service-count="$serviceCount" />
    <x-web.services :content="$content" :services="$services" />
    <x-web.advantages :content="$content" :icons="$advantageIcons" />
    <x-web.cta :company="$company" />
@endsection
