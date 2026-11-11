@extends('layouts.public', ['transparentNav' => true])

@section('content')
    <x-web.hero />
    <x-web.about :company="$company" :service-count="$serviceCount" />
    <x-web.services :services="$services" />
    <x-web.advantages :advantages="$advantages" />
    <x-web.cta :company="$company" />
@endsection
