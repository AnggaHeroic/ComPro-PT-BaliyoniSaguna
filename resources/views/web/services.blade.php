@extends('layouts.public', ['transparentNav' => true, 'title' => 'Layanan', 'description' => 'Produk dan layanan PT Baliyoni Saguna: dari peralatan elektronik dan kantor hingga AC, software, dan sistem parkir elektronik.'])

@section('content')
    <x-web.page-header eyebrow="Layanan" title="Produk dan Layanan" highlight="Kami" image="1048">
        Beragam kebutuhan bisnis, instansi, dan perorangan yang dapat dipenuhi dalam satu pintu.
    </x-web.page-header>
    <x-web.service-scope :services="$services" />
    <x-web.cta :company="$company" />
@endsection
