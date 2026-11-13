@extends('layouts.public', ['transparentNav' => true, 'title' => 'Layanan', 'description' => 'Produk dan layanan PT Baliyoni Saguna: dari peralatan elektronik dan kantor hingga AC, software, dan sistem parkir elektronik.'])

@section('content')
    <x-web.page-header variant="center" title="Solusi Lengkap untuk" highlight="Setiap Kebutuhan" image="1470075801209-17f9ec0cada6">
        <x-slot:label>Layanan <span class="text-brand-bright">&amp;</span> Produk</x-slot:label>
        <x-slot:scrollTo>#ruang-lingkup</x-slot:scrollTo>
        Dari peralatan elektronik dan kantor hingga AC, software, dan sistem parkir elektronik, semua dalam satu pintu.
    </x-web.page-header>
    <x-web.service-scope :services="$services" />
    <x-web.cta :company="$company" />
@endsection
