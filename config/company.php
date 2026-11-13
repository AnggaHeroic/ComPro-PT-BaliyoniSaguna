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
    // 'image' = ID foto PLACEHOLDER dari Unsplash (bagian setelah 'photo-' pada URL images.unsplash.com; null = latar polos), ganti dengan foto resmi nanti. 'description' = deskripsi singkat (draf, perlu ditinjau). Ganti href ke route() saat halamannya ada.
    // 'activities' = aktivitas usaha per kategori (halaman Layanan), tiap butir berisi 'name', 'detail', dan (opsional) 'summary' (ringkasan 1-2 baris di card). PLACEHOLDER: nama disusun dari
    // nama kategori, BUKAN data resmi; ganti dengan aktivitas usaha perusahaan. 'detail' (penjelasan di halaman detail; teks, atau daftar paragraf) null = tampil
    // 3 paragraf contoh dari 'activity_placeholder' di bawah. Kode KBLI sengaja belum ditampilkan sampai datanya diberikan perusahaan.
    'services' => [
        ['name' => 'Peralatan Elektronik', 'icon' => 'electronics', 'image' => '1519389950473-47ba0277781c', 'description' => 'Perangkat elektronik untuk rumah, kantor, dan instansi.', 'activities' => [
            ['name' => 'Penjualan laptop, komputer, dan printer', 'detail' => null],
            ['name' => 'Penjualan perangkat jaringan dan CCTV', 'detail' => null],
            ['name' => 'Penjualan proyektor dan layar presentasi', 'detail' => null],
            ['name' => 'Pengadaan perangkat elektronik untuk instansi', 'detail' => null],
            ['name' => 'Garansi dan layanan purna jual', 'detail' => null],
        ]],
        ['name' => 'Perlengkapan Alat Tulis', 'icon' => 'stationery', 'image' => '1456735190827-d1262f71b8a3', 'description' => 'Alat tulis dan perlengkapan menulis sehari-hari.', 'activities' => [
            ['name' => 'Penjualan alat tulis kantor', 'detail' => null],
            ['name' => 'Pengadaan kertas dan buku tulis', 'detail' => null],
            ['name' => 'Penjualan perlengkapan arsip', 'detail' => null],
            ['name' => 'Pengadaan alat tulis untuk sekolah', 'detail' => null],
            ['name' => 'Penyediaan paket alat tulis instansi', 'detail' => null],
        ]],
        ['name' => 'Peralatan Olahraga', 'icon' => 'sports', 'image' => '1584735935682-2f2b69dff9d2', 'description' => 'Perlengkapan olahraga untuk latihan dan kompetisi.', 'activities' => [
            ['name' => 'Penjualan perlengkapan olahraga tim', 'detail' => null],
            ['name' => 'Pengadaan seragam dan sepatu olahraga', 'detail' => null],
            ['name' => 'Penjualan peralatan kebugaran', 'detail' => null],
            ['name' => 'Pengadaan perlengkapan event olahraga', 'detail' => null],
            ['name' => 'Penyediaan peralatan olahraga sekolah', 'detail' => null],
        ]],
        ['name' => 'Peralatan Kantor', 'icon' => 'office', 'image' => '1556761175-4b46a572b786', 'description' => 'Perlengkapan penunjang operasional kantor.', 'activities' => [
            ['name' => 'Penjualan furnitur kantor', 'detail' => null],
            ['name' => 'Pengadaan mesin fotokopi dan pemindai', 'detail' => null],
            ['name' => 'Penjualan perlengkapan ruang rapat', 'detail' => null],
            ['name' => 'Pengadaan peralatan penunjang administrasi', 'detail' => null],
            ['name' => 'Penyediaan perlengkapan kebersihan kantor', 'detail' => null],
        ]],
        ['name' => 'Peralatan Pendidikan', 'icon' => 'education', 'image' => '1580582932707-520aed937b7b', 'description' => 'Alat bantu belajar untuk sekolah dan lembaga pendidikan.', 'activities' => [
            ['name' => 'Pengadaan alat peraga edukatif', 'detail' => null],
            ['name' => 'Pengadaan buku dan media belajar', 'detail' => null],
            ['name' => 'Penjualan perangkat pembelajaran digital', 'detail' => null],
            ['name' => 'Pengadaan mebel sekolah', 'detail' => null],
            ['name' => 'Penyediaan perlengkapan kelas', 'detail' => null],
        ]],
        ['name' => 'Hadiah & Souvenir', 'icon' => 'gift', 'image' => '1549465220-1a8b9238cd48', 'description' => 'Hadiah dan souvenir untuk acara dan apresiasi.', 'activities' => [
            ['name' => 'Pembuatan souvenir sesuai pesanan', 'detail' => null],
            ['name' => 'Pengadaan hadiah untuk perusahaan', 'detail' => null],
            ['name' => 'Penjualan merchandise berlogo', 'detail' => null],
            ['name' => 'Penyediaan parsel dan hampers', 'detail' => null],
            ['name' => 'Pengadaan cendera mata acara', 'detail' => null],
        ]],
        ['name' => 'Makanan dan Minuman', 'icon' => 'food', 'image' => '1555939594-58d7cb561ad1', 'description' => 'Beragam produk makanan dan minuman.', 'activities' => [
            ['name' => 'Penjualan produk makanan kemasan', 'detail' => null],
            ['name' => 'Penyediaan minuman kemasan', 'detail' => null],
            ['name' => 'Pengadaan konsumsi acara', 'detail' => null],
            ['name' => 'Penjualan produk UMKM Bali', 'detail' => null],
            ['name' => 'Pengadaan bahan pangan untuk instansi', 'detail' => null],
        ]],
        ['name' => 'Perkakas', 'icon' => 'tools', 'image' => '1581783898377-1c85bf937427', 'description' => 'Perkakas dan alat kerja untuk pekerjaan teknis.', 'activities' => [
            ['name' => 'Penjualan perkakas tangan', 'detail' => null],
            ['name' => 'Penjualan perkakas listrik', 'detail' => null],
            ['name' => 'Pengadaan alat kerja teknis', 'detail' => null],
            ['name' => 'Penyediaan perlengkapan keselamatan kerja', 'detail' => null],
            ['name' => 'Pengadaan peralatan bengkel', 'detail' => null],
        ]],
        ['name' => 'Pakaian dan Aksesoris', 'icon' => 'apparel', 'image' => '1441984904996-e0b6ba687e04', 'description' => 'Pakaian dan aksesoris untuk seragam maupun harian.', 'activities' => [
            ['name' => 'Pembuatan seragam kerja', 'detail' => null],
            ['name' => 'Pengadaan seragam sekolah dan instansi', 'detail' => null],
            ['name' => 'Penjualan kaos dan jaket', 'detail' => null],
            ['name' => 'Penyediaan topi dan tas', 'detail' => null],
            ['name' => 'Pengadaan atribut dan aksesoris', 'detail' => null],
        ]],
        ['name' => 'AC Split Wall', 'icon' => 'ac-wall', 'image' => '1761330440311-16e160cad236', 'description' => 'Pendingin udara split wall untuk rumah dan kantor.', 'activities' => [
            ['name' => 'Penjualan AC split wall', 'detail' => null],
            ['name' => 'Pemasangan unit baru', 'detail' => null],
            ['name' => 'Servis dan perawatan berkala', 'detail' => null],
            ['name' => 'Pembersihan dan pengisian freon', 'detail' => null],
            ['name' => 'Penggantian suku cadang', 'detail' => null],
        ]],
        ['name' => 'Ceiling Cassette', 'icon' => 'cassette', 'image' => '1667983453881-4992fe86ab1b', 'description' => 'Pendingin udara ceiling cassette untuk ruang luas.', 'activities' => [
            ['name' => 'Penjualan AC ceiling cassette', 'detail' => null],
            ['name' => 'Survei dan perencanaan kapasitas ruangan', 'detail' => null],
            ['name' => 'Pemasangan unit dan instalasi pipa', 'detail' => null],
            ['name' => 'Servis dan perawatan berkala', 'detail' => null],
            ['name' => 'Garansi dan layanan purna jual', 'detail' => null],
        ]],
        ['name' => 'Jasa Sewa', 'icon' => 'rental', 'image' => '1603814744450-36f978490b11', 'description' => 'Layanan sewa untuk kebutuhan sementara.', 'activities' => [
            ['name' => 'Sewa komputer dan laptop', 'detail' => null],
            ['name' => 'Sewa proyektor dan perlengkapan presentasi', 'detail' => null],
            ['name' => 'Sewa peralatan acara', 'detail' => null],
            ['name' => 'Sewa perangkat jaringan', 'detail' => null],
            ['name' => 'Sewa peralatan kantor sementara', 'detail' => null],
        ]],
        ['name' => 'VRF', 'icon' => 'vrf', 'image' => '1776860154052-d91c90b25c59', 'description' => 'Sistem pendingin VRF untuk gedung multi-ruangan.', 'activities' => [
            ['name' => 'Penjualan sistem pendingin VRF', 'detail' => null],
            ['name' => 'Perencanaan dan desain sistem', 'detail' => null],
            ['name' => 'Instalasi untuk gedung bertingkat', 'detail' => null],
            ['name' => 'Perawatan berkala dan inspeksi', 'detail' => null],
            ['name' => 'Perbaikan dan penggantian suku cadang', 'detail' => null],
        ]],
        ['name' => 'Alat Lab', 'icon' => 'lab', 'image' => '1602052577122-f73b9710adba', 'description' => 'Peralatan laboratorium untuk sekolah dan kampus.', 'activities' => [
            ['name' => 'Pengadaan peralatan laboratorium sekolah', 'detail' => null],
            ['name' => 'Penjualan alat ukur dan instrumen', 'detail' => null],
            ['name' => 'Pengadaan bahan praktikum', 'detail' => null],
            ['name' => 'Penyediaan perabot laboratorium', 'detail' => null],
            ['name' => 'Kalibrasi dan perawatan alat', 'detail' => null],
        ]],
        ['name' => 'Software', 'icon' => 'software', 'image' => '1461749280684-dccba630e2f6', 'description' => 'Perangkat lunak penunjang operasional bisnis.', 'activities' => [
            ['name' => 'Pengembangan aplikasi sesuai kebutuhan', 'detail' => null],
            ['name' => 'Penyediaan sistem informasi manajemen', 'detail' => null],
            ['name' => 'Penjualan lisensi perangkat lunak', 'detail' => null],
            ['name' => 'Integrasi sistem dan data', 'detail' => null],
            ['name' => 'Dukungan teknis dan pemeliharaan', 'detail' => null],
        ]],
        ['name' => 'Sistem Parkir Elektronik', 'icon' => 'parking', 'image' => '1640957517545-efee2e9c9570', 'description' => 'Sistem parkir otomatis untuk area parkir yang tertib.', 'activities' => [
            ['name' => 'Penjualan sistem parkir elektronik', 'detail' => null],
            ['name' => 'Pemasangan palang pintu otomatis', 'detail' => null],
            ['name' => 'Penyediaan perangkat tiket dan kartu', 'detail' => null],
            ['name' => 'Integrasi aplikasi pengelolaan parkir', 'detail' => null],
            ['name' => 'Pemeliharaan dan dukungan teknis', 'detail' => null],
        ]],
    ],

    // Deskripsi CONTOH (3 paragraf) untuk aktivitas yang 'detail'-nya masih null. :activity dan :category diganti nama aktivitas dan kategorinya.
    // PLACEHOLDER sementara, bukan teks resmi; hapus bila semua aktivitas sudah punya 'detail'.
    'activity_placeholder' => [
        ':activity merupakan salah satu aktivitas usaha PT Baliyoni Saguna pada kategori :category. Melalui aktivitas ini, kami melayani kebutuhan pelanggan perorangan, bisnis, maupun instansi yang membutuhkan produk dan layanan yang sesuai.',
        'Setiap permintaan kami tangani dengan memperhatikan kebutuhan pelanggan, mulai dari konsultasi pemilihan, penyediaan, hingga pendampingan setelahnya. Dengan begitu, pelanggan memperoleh solusi yang tepat tanpa harus mencari ke banyak tempat.',
        'Untuk informasi spesifikasi, ketersediaan, dan penawaran, silakan hubungi tim kami. Kami siap membantu menyesuaikan kebutuhan Anda.',
    ],

    // Halaman Tentang Kami. DRAF teks (perlu ditinjau perusahaan); 'profile_image' = ID foto PLACEHOLDER dari Unsplash.
    'about' => [
        'profile_image' => '1497366811353-6870744d04b2',
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
        ['label' => 'Layanan', 'href' => '/layanan', 'match' => 'services*', 'mega' => true],
        ['label' => 'Galeri', 'href' => '/galeri', 'match' => 'gallery'],
    ],

    // Galeri foto (halaman Galeri). PLACEHOLDER: 'image' = ID foto Unsplash (bagian setelah 'photo-'), 'title' = judul foto (selalu tampil), 'description' = keterangan singkat (tampil saat foto disorot).
    // Ganti dengan dokumentasi resmi perusahaan.
    'gallery' => [
        ['title' => 'Gedung perkantoran', 'description' => 'Gambaran lingkungan perkantoran modern.', 'image' => '1486406146926-c627a92ad1ab'],
        ['title' => 'Ruang kerja', 'description' => 'Ruang kerja yang tertata untuk kegiatan operasional.', 'image' => '1497366811353-6870744d04b2'],
        ['title' => 'Area kantor', 'description' => 'Area kerja bersama untuk tim dan pelanggan.', 'image' => '1556761175-4b46a572b786'],
        ['title' => 'Peralatan elektronik', 'description' => 'Laptop dan perangkat elektronik penunjang kerja.', 'image' => '1519389950473-47ba0277781c'],
        ['title' => 'Perlengkapan alat tulis', 'description' => 'Alat tulis dan kebutuhan administrasi harian.', 'image' => '1456735190827-d1262f71b8a3'],
        ['title' => 'Peralatan olahraga', 'description' => 'Perlengkapan latihan dan kebugaran.', 'image' => '1584735935682-2f2b69dff9d2'],
        ['title' => 'Peralatan pendidikan', 'description' => 'Perlengkapan untuk kegiatan belajar di sekolah.', 'image' => '1580582932707-520aed937b7b'],
        ['title' => 'Perkakas', 'description' => 'Perkakas tangan untuk pekerjaan teknis.', 'image' => '1581783898377-1c85bf937427'],
        ['title' => 'Pendingin udara', 'description' => 'Unit pendingin udara untuk rumah dan kantor.', 'image' => '1761330440311-16e160cad236'],
        ['title' => 'Peralatan laboratorium', 'description' => 'Peralatan penunjang praktikum dan penelitian.', 'image' => '1602052577122-f73b9710adba'],
        ['title' => 'Perangkat lunak', 'description' => 'Perangkat lunak penunjang operasional bisnis.', 'image' => '1461749280684-dccba630e2f6'],
        ['title' => 'Sistem parkir elektronik', 'description' => 'Palang dan sistem pengelolaan area parkir.', 'image' => '1640957517545-efee2e9c9570'],
    ],
];
