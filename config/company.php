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

    // Halaman Tentang Kami. DRAF teks (perlu ditinjau perusahaan); 'profile_image' = ID foto PLACEHOLDER (picsum.photos).
    'about' => [
        'profile_image' => 1031,
        'profile' => [
            'PT. Baliyoni Saguna adalah perusahaan yang telah eksis dalam industri Teknologi Informasi (TI) di Bali, didirikan pada tanggal 3 Mei 2000. Teknologi dan produk berbasis TI yang kami kembangkan telah digunakan dalam sistem pelayanan oleh pelanggan kami, dan kami terus berkarya dengan semangat kreativitas, inovasi, legalitas, optimisme, serta komitmen untuk memberikan yang terbaik.',
            'Seiring perkembangan bisnis dalam bidang pengadaan barang dan jasa pemerintahan, jangkauan usaha kami meluas dari peralatan elektronik ke furniture dan office equipment, perkakas, air conditioner, internet service provider, lisensi perangkat lunak, alat tulis kantor, penyewaan peralatan elektronik, peralatan olahraga, dan peralatan pendidikan. Kini kami memiliki 46 anak perusahaan yang berjalan dalam pengadaan barang dan jasa pemerintahan skala regional dan nasional.',
            'Kami juga mengembangkan marketplace online Balimall.id untuk memasarkan produk UMKM Bali secara online dan mendukung program Pemerintah Provinsi Bali, serta layanan purna jual melalui Divisi Service sebagai Authorized Service Point berbagai brand, seperti HP, Acer, Axioo, Asus, Epson, Panasonic, Daikin, dan Mitsubishi.',
        ],
        // Data ringkas di bawah foto profil (label kecil + isi).
        'facts' => [
            ['title' => 'Nama Perusahaan', 'text' => 'PT. Baliyoni Saguna'],
            ['title' => 'Berdiri', 'text' => '3 Mei 2000'],
            ['title' => 'Kedudukan', 'text' => 'Bali, Indonesia'],
            ['title' => 'Anak Perusahaan', 'text' => '46 anak perusahaan'],
            ['title' => 'Platform Marketplace', 'text' => 'Balimall.id'],
        ],
        // Kalimat pembuka bagian Visi dan Misi.
        'direction_tagline' => 'Arah dan komitmen yang menjadi pedoman seluruh kegiatan perusahaan.',
        // Visi, misi, dan moto resmi perusahaan (dari brosur). Misi = judul singkat + uraian.
        'vision' => 'Menjadi perusahaan penyedia barang dan jasa yang terlengkap di Indonesia, berbasis edukasi, inovasi, dan layanan purna jual.',
        'mission' => [
            ['title' => 'People', 'text' => 'Membangun tim yang sehat fisik, mental, dan memiliki karakter yang tangguh, berpikir inovatif, serta melayani dengan tulus.'],
            ['title' => 'Process', 'text' => 'Menjalankan proses pengadaan barang dan jasa secara akuntabel, efektif, efisien, dan transparan.'],
            ['title' => 'Product/Service', 'text' => 'Memastikan barang dan jasa sesuai dengan spesifikasi, tepat waktu, dan disertai jaminan purna jual.'],
            ['title' => 'Profit', 'text' => 'Memelihara profitabilitas melalui praktik bisnis yang bersih, etis, dan mengutamakan kebermanfaatan jangka panjang.'],
            ['title' => 'Purpose', 'text' => 'Mencapai keuntungan yang selaras dengan nilai-nilai keluarga, sosial, dan masyarakat.'],
        ],
        'motto' => ['One Stop Solution.', 'Solusi Tuntas, Bahagia Pantas.'],
        'values' => [
            ['title' => 'Integritas', 'text' => 'Jujur dan konsisten dalam setiap keputusan dan transaksi.'],
            ['title' => 'Profesionalisme', 'text' => 'Bekerja dengan standar yang jelas dan penuh tanggung jawab.'],
            ['title' => 'Pelayanan Prima', 'text' => 'Menempatkan kebutuhan pelanggan sebagai prioritas utama.'],
            ['title' => 'Efisiensi', 'text' => 'Proses yang ringkas, cepat, dan tepat sasaran.'],
            ['title' => 'Kolaborasi', 'text' => 'Bertumbuh bersama mitra, holding, dan outlet Baliyoni Group.'],
        ],
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
        ['label' => 'Tentang Kami', 'href' => '/tentang-kami', 'match' => 'about'],
        ['label' => 'Layanan', 'href' => '/layanan', 'match' => 'services', 'mega' => true],
        ['label' => 'Galeri', 'href' => '/galeri', 'match' => null],
    ],
];
