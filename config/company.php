<?php

/*
 * Data perusahaan: satu sumber untuk navbar, footer, dan meta halaman.
 * Field kontak yang bernilai null tidak akan ditampilkan di footer.
 */
return [
    'name' => 'PT Baliyoni Saguna',
    'short_name' => 'Baliyoni Saguna',
    'tagline' => 'Holding company Baliyoni Group.',
    'city' => 'Bali, Indonesia',

    // Deskripsi singkat perusahaan (footer).
    'description' => 'Perusahaan induk dari berbagai holding dan outlet Baliyoni Group, menghadirkan beragam produk dan layanan dalam satu pintu.',

    // Kontak resmi perusahaan. 'phone' = telepon kantor, 'mobile' = nomor WhatsApp; *_href = format internasional (untuk tel: dan wa.me).
    'email' => 'info@baliyoni.co.id',
    'phone' => '(0361) 257055',
    'phone_href' => '+62361257055',
    'mobile' => '0822 7226 9888',
    'mobile_href' => '+6282272269888',
    'address' => 'Jl. Tukad Batanghari No.1C, Panjer, Denpasar Selatan, Kota Denpasar, Bali',

    // Kategori produk dan layanan (dropdown navbar dan carousel Layanan). 'icon' = nama ikon di components/web/service-icon,
    // 'image' = ID foto PLACEHOLDER (picsum.photos; null = latar polos), ganti dengan foto resmi nanti. 'description' = deskripsi singkat (draf, perlu ditinjau). Ganti href ke route() saat halamannya ada.
    'services' => [
        ['name' => 'Peralatan Elektronik', 'icon' => 'electronics', 'image' => 1076, 'description' => 'Perangkat elektronik untuk rumah, kantor, dan instansi.'],
        ['name' => 'Perlengkapan Alat Tulis', 'icon' => 'stationery', 'image' => 20, 'description' => 'Alat tulis dan perlengkapan menulis sehari-hari.'],
        ['name' => 'Peralatan Olahraga', 'icon' => 'sports', 'image' => 1073, 'description' => 'Perlengkapan olahraga untuk latihan dan kompetisi.'],
        ['name' => 'Peralatan Kantor', 'icon' => 'office', 'image' => 1031, 'description' => 'Perlengkapan penunjang operasional kantor.'],
        ['name' => 'Peralatan Pendidikan', 'icon' => 'education', 'image' => 24, 'description' => 'Alat bantu belajar untuk sekolah dan lembaga pendidikan.'],
        ['name' => 'Hadiah & Souvenir', 'icon' => 'gift', 'image' => 1062, 'description' => 'Hadiah dan souvenir untuk acara dan apresiasi.'],
        ['name' => 'Makanan dan Minuman', 'icon' => 'food', 'image' => 1078, 'description' => 'Beragam produk makanan dan minuman.'],
        ['name' => 'Perkakas', 'icon' => 'tools', 'image' => 250, 'description' => 'Perkakas dan alat kerja untuk pekerjaan teknis.'],
        ['name' => 'Pakaian dan Aksesoris', 'icon' => 'apparel', 'image' => 60, 'description' => 'Pakaian dan aksesoris untuk seragam maupun harian.'],
        ['name' => 'AC Split Wall', 'icon' => 'ac-wall', 'image' => 201, 'description' => 'Pendingin udara split wall untuk rumah dan kantor.'],
        ['name' => 'Ceiling Cassette', 'icon' => 'cassette', 'image' => 1018, 'description' => 'Pendingin udara ceiling cassette untuk ruang luas.'],
        ['name' => 'Jasa Sewa', 'icon' => 'rental', 'image' => 1015, 'description' => 'Layanan sewa untuk kebutuhan sementara.'],
        ['name' => 'VRF', 'icon' => 'vrf', 'image' => 1016, 'description' => 'Sistem pendingin VRF untuk gedung multi-ruangan.'],
        ['name' => 'Alat Lab', 'icon' => 'lab', 'image' => 1011, 'description' => 'Peralatan laboratorium untuk sekolah dan kampus.'],
        ['name' => 'Software', 'icon' => 'software', 'image' => 1043, 'description' => 'Perangkat lunak penunjang operasional bisnis.'],
        ['name' => 'Sistem Parkir Elektronik', 'icon' => 'parking', 'image' => 1040, 'description' => 'Sistem parkir otomatis untuk area parkir yang tertib.'],
    ],

    // Keunggulan (section "Mengapa Kami"). 'icon' = nama ikon di components/web/advantage-icon. Draf teks, perlu ditinjau perusahaan.
    'advantages' => [
        ['icon' => 'system', 'title' => 'Semua dalam Satu Pintu', 'text' => 'Dari peralatan elektronik, perkantoran, dan pendidikan hingga pendingin udara dan sistem parkir elektronik, semuanya tersedia di satu tempat tanpa repot berpindah penyedia.'],
        ['icon' => 'efficiency', 'title' => 'Kecepatan & Efisiensi Layanan', 'text' => 'Setiap permintaan ditangani dengan cepat dan terkoordinasi, sehingga pengadaan kebutuhan Anda berjalan lancar tanpa penundaan.'],
        ['icon' => 'security', 'title' => 'Transaksi Aman dan Jelas', 'text' => 'Setiap pemesanan dan pembayaran dilakukan secara jelas dan aman, sehingga Anda dapat bertransaksi dengan tenang.'],
        ['icon' => 'complete', 'title' => 'Layanan Lengkap Sesuai Kebutuhan Anda', 'text' => 'Dari pengadaan produk, jasa sewa, hingga perangkat lunak dan sistem, semuanya tersedia dalam satu grup.'],
        ['icon' => 'support', 'title' => 'Pelayanan Pelanggan Terbaik', 'text' => 'Setiap pertanyaan dan keluhan Anda ditangani dengan cepat dan profesional oleh tim kami.'],
    ],

    // Tautan navigasi utama. Item dengan 'mega' => true menampilkan dropdown kategori layanan.
    'nav' => [
        ['label' => 'Beranda', 'href' => '/', 'match' => 'home'],
        ['label' => 'Tentang Kami', 'href' => '/#tentang', 'match' => null],
        ['label' => 'Layanan', 'href' => '/#layanan', 'match' => null, 'mega' => true],
        ['label' => 'Galeri', 'href' => '/galeri', 'match' => null],
    ],
];
