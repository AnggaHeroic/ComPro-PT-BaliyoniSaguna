<?php

/*
 * Teks halaman publik yang boleh diubah admin lewat menu "Isi Halaman", beserta teks bawaannya.
 * Tata letak tetap di file Blade; admin hanya mengganti isi teks dan gambar.
 *
 * Struktur: halaman => label, description, route (halaman publik untuk tombol "Lihat halaman"),
 * sections => bagian => label, description, preview (false = tidak tampil di halaman), fields => kunci => kolom.
 *
 * Kolom: label, default, type (text | textarea | title | image | cards), max (panjang), optional, hint.
 *  - title: teks dengan penanda warna *kata* (dirender berwarna oleh PageContentSet::rich()).
 *  - cards: daftar kartu berisi title dan text; juga min_items, max_items, max_title, max_text, item_label.
 * 'preview' => nama bagian lain (mis. 'profile') memakai pratinjau bagian itu, karena tampil di bagian halaman yang sama.
 * Bagian dengan 'preview' => false tidak menampilkan pratinjau; 'note' = keterangan singkat penggantinya.
 * 'title' (opsional) = judul panel bagian bila berbeda dari 'label' (label dipakai pada tab dan riwayat).
 * Bagian boleh dibagi menjadi 'groups' (kunci => label, description, preview); tiap kolom menyebut 'group'-nya dan tiap kelompok punya pratinjau sendiri.
 *  - image: default berupa URL, atau path yang diawali / untuk berkas di folder public; file unggahan disimpan di disk public/pages.
 */
return [
    'home' => [
        'label' => 'Beranda',
        'description' => 'Teks dan gambar pada halaman utama website.',
        'route' => 'home',
        'sections' => [
            'hero' => [
                'label' => 'Bagian utama',
                'description' => 'Bagian paling atas halaman. Warnai kata pada teks kecil dengan tombol Warnai merah.',
                'fields' => [
                    'hero_eyebrow' => ['label' => 'Teks kecil di atas judul', 'type' => 'title', 'default' => 'PT. Bal*i*yoni Saguna', 'max' => 60, 'rows' => 1],
                    'hero_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'One Stop Solution', 'max' => 60],
                    'hero_subtitle' => ['label' => 'Judul kedua (merah)', 'default' => 'Solusi Tuntas, Bahagia Pantas', 'max' => 80],
                    'hero_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Perusahaan induk dari berbagai holding dan outlet Baliyoni Group. Kami menyediakan peralatan elektronik, perkantoran, pendidikan, pendingin udara, hingga sistem parkir elektronik.', 'max' => 400],
                    'hero_button_primary' => ['label' => 'Tombol utama', 'default' => 'Lihat Layanan', 'max' => 30, 'hint' => 'Tautan tombol tetap ke bagian Layanan di halaman ini.'],
                    'hero_button_secondary' => ['label' => 'Tombol kedua', 'default' => 'Hubungi Kami', 'max' => 30, 'hint' => 'Tautan tombol tetap ke halaman Kontak.'],
                    'hero_image' => ['label' => 'Foto latar', 'type' => 'image', 'default' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&h=1080&q=75', 'hint' => 'JPG, PNG, atau WebP, maksimal 4 MB. Gunakan foto lebar; bagian tepi dapat terpotong.'],
                ],
            ],
            'about' => [
                'label' => 'Profil singkat',
                'fields' => [
                    'about_eyebrow' => ['label' => 'Label kecil', 'default' => 'Profil Singkat', 'max' => 40],
                    'about_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Mengenal PT. Baliyoni Saguna', 'max' => 100],
                    'about_paragraph_1' => ['label' => 'Paragraf 1', 'type' => 'textarea', 'default' => 'PT. Baliyoni Saguna adalah perusahaan induk dari berbagai holding dan outlet Baliyoni Group yang berkedudukan di Bali, Indonesia.', 'max' => 500],
                    'about_paragraph_2' => ['label' => 'Paragraf 2', 'type' => 'textarea', 'default' => 'Dengan semboyan One Stop Solution, kami menghadirkan beragam produk dan layanan dalam satu pintu.', 'max' => 500, 'optional' => true, 'hint' => 'Boleh dikosongkan; paragraf tidak akan tampil.'],
                    'about_button' => ['label' => 'Tombol', 'default' => 'Selengkapnya', 'max' => 30, 'hint' => 'Mengarah ke halaman Tentang Kami.'],
                    'about_stat_label' => ['label' => 'Kartu angka: judul', 'default' => 'Kategori Produk & Layanan', 'max' => 50, 'hint' => 'Angkanya dihitung otomatis dari menu Kategori Usaha.'],
                    'about_stat_description' => ['label' => 'Kartu angka: keterangan', 'type' => 'textarea', 'default' => 'Kebutuhan bisnis, instansi, dan perorangan dalam satu tempat.', 'max' => 140],
                    'about_stat_link' => ['label' => 'Kartu angka: tautan', 'default' => 'Lihat semua layanan', 'max' => 40],
                ],
            ],
            'services' => [
                'label' => 'Layanan',
                'description' => 'Kartu kategori pada carousel diatur lewat menu Kategori Usaha.',
                'fields' => [
                    'services_eyebrow' => ['label' => 'Label kecil', 'default' => 'Layanan Kami', 'max' => 40],
                    'services_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Produk dan Layanan', 'max' => 80],
                    'services_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Beragam kategori yang kami sediakan untuk memenuhi kebutuhan Anda.', 'max' => 200],
                    'services_button' => ['label' => 'Tombol', 'default' => 'Lihat Semua', 'max' => 30, 'hint' => 'Mengarah ke halaman Layanan.'],
                ],
            ],
            'advantages' => [
                'label' => 'Mengapa kami',
                'fields' => [
                    'advantages_eyebrow' => ['label' => 'Label kecil', 'default' => 'Mengapa Kami', 'max' => 40],
                    'advantages_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Solusi Terpadu untuk Bisnis dan Instansi', 'max' => 100],
                    'advantages_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'PT. Baliyoni Saguna menghadirkan ekosistem digital terpadu yang menghubungkan semua layanan dalam satu genggaman. Dengan teknologi Business Intelligence dan Single Sign-On (SSO), pengalaman belanja Anda menjadi lebih mudah, cepat, dan aman.', 'max' => 500],
                    'advantages_items' => [
                        'label' => 'Daftar keunggulan', 'type' => 'cards', 'item_label' => 'Keunggulan', 'min_items' => 1, 'max_items' => 5, 'max_title' => 60, 'max_text' => 250,
                        'hint' => 'Ikon mengikuti urutan kartu. Ubah urutan untuk menukar ikon.',
                        'default' => [
                            ['title' => 'Semua dalam Satu Pintu', 'text' => 'Dari peralatan elektronik, perkantoran, dan pendidikan hingga pendingin udara dan sistem parkir elektronik, semuanya tersedia di satu tempat tanpa repot berpindah penyedia.'],
                            ['title' => 'Kecepatan & Efisiensi Layanan', 'text' => 'Setiap permintaan ditangani dengan cepat dan terkoordinasi, sehingga pengadaan kebutuhan Anda berjalan lancar tanpa penundaan.'],
                            ['title' => 'Transaksi Aman dan Jelas', 'text' => 'Setiap pemesanan dan pembayaran dilakukan secara jelas dan aman, sehingga Anda dapat bertransaksi dengan tenang.'],
                            ['title' => 'Layanan Lengkap Sesuai Kebutuhan Anda', 'text' => 'Dari pengadaan produk, jasa sewa, hingga perangkat lunak dan sistem, semuanya tersedia dalam satu grup.'],
                            ['title' => 'Pelayanan Pelanggan Terbaik', 'text' => 'Setiap pertanyaan dan keluhan Anda ditangani dengan cepat dan profesional oleh tim kami.'],
                        ],
                    ],
                ],
            ],
            'seo' => [
                'label' => 'Mesin pencari',
                'preview' => false,
                'note' => 'Bagian ini tidak tampil di halaman; hanya dibaca mesin pencari.',
                'fields' => [
                    'seo_description' => ['label' => 'Deskripsi singkat untuk Google', 'type' => 'textarea', 'default' => 'PT. Baliyoni Saguna, perusahaan induk Baliyoni Group di Bali. One Stop Solution untuk peralatan elektronik, perkantoran, pendidikan, pendingin udara, dan sistem parkir elektronik.', 'max' => 200, 'hint' => 'Tidak tampil di halaman; dipakai mesin pencari.'],
                ],
            ],
        ],
    ],

    'about' => [
        'label' => 'Tentang Kami',
        'description' => 'Teks dan foto pada halaman Tentang Kami.',
        'sections' => [
            'hero' => [
                'label' => 'Bagian utama',
                'fields' => [
                    'hero_eyebrow' => ['label' => 'Label kecil', 'default' => 'Tentang Kami', 'max' => 40],
                    'hero_title' => ['label' => 'Judul', 'type' => 'title', 'default' => "Mengenal\n*PT. Baliyoni Saguna*", 'max' => 120, 'rows' => 2, 'hint' => 'Enter untuk pindah baris. Blok kata lalu klik Warnai merah (bawaannya baris kedua berwarna merah).'],
                    'hero_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Perusahaan induk dari berbagai holding dan outlet Baliyoni Group, menghadirkan beragam produk dan layanan dalam satu pintu.', 'max' => 300],
                    'hero_image' => ['label' => 'Foto latar', 'type' => 'image', 'default' => 'https://images.unsplash.com/photo-1448670439281-3a2c30e6d4da?auto=format&fit=crop&w=1920&h=900&q=75', 'hint' => 'JPG, PNG, atau WebP, maksimal 4 MB. Gunakan foto lebar.'],
                ],
            ],
            'profile' => [
                'label' => 'Profil perusahaan',
                'fields' => [
                    'profile_label' => ['label' => 'Label kecil', 'default' => 'Profil Perusahaan', 'max' => 40],
                    'profile_title' => ['label' => 'Judul', 'type' => 'title', 'default' => 'Dari *Teknologi Informasi* hingga Pengadaan Barang dan Jasa', 'max' => 120, 'rows' => 2],
                    'profile_paragraph_1' => ['label' => 'Paragraf 1', 'type' => 'textarea', 'default' => 'PT. Baliyoni Saguna adalah perusahaan yang telah eksis dalam industri Teknologi Informasi (TI) di Bali, didirikan pada tanggal 3 Mei 2000. Teknologi dan produk berbasis TI yang kami kembangkan telah digunakan dalam sistem pelayanan oleh pelanggan kami, dan kami terus berkarya dengan semangat kreativitas, inovasi, legalitas, optimisme, serta komitmen untuk memberikan yang terbaik.', 'max' => 1200],
                    'profile_paragraph_2' => ['label' => 'Paragraf 2', 'type' => 'textarea', 'default' => 'Seiring perkembangan bisnis dalam bidang pengadaan barang dan jasa pemerintahan, jangkauan usaha kami meluas dari peralatan elektronik ke furniture dan office equipment, perkakas, air conditioner, internet service provider, lisensi perangkat lunak, alat tulis kantor, penyewaan peralatan elektronik, peralatan olahraga, dan peralatan pendidikan. Kini kami memiliki 46 anak perusahaan yang berjalan dalam pengadaan barang dan jasa pemerintahan skala regional dan nasional.', 'max' => 1200, 'optional' => true, 'hint' => 'Boleh dikosongkan; paragraf tidak akan tampil.'],
                    'profile_paragraph_3' => ['label' => 'Paragraf 3', 'type' => 'textarea', 'default' => 'Kami juga mengembangkan marketplace online Balimall.id untuk memasarkan produk UMKM Bali secara online dan mendukung program Pemerintah Provinsi Bali, serta layanan purna jual melalui Divisi Service sebagai Authorized Service Point berbagai brand, seperti HP, Acer, Axioo, Asus, Epson, Panasonic, Daikin, dan Mitsubishi.', 'max' => 1200, 'optional' => true, 'hint' => 'Boleh dikosongkan; paragraf tidak akan tampil.'],
                    'profile_image' => ['label' => 'Foto', 'type' => 'image', 'default' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=800&h=600&q=75', 'hint' => 'JPG, PNG, atau WebP, maksimal 4 MB. Tampil dengan rasio 4:3; sisi gambar dapat terpotong.'],
                    'profile_image_alt' => ['label' => 'Keterangan foto (untuk pembaca layar)', 'default' => 'Kantor PT Baliyoni Saguna', 'max' => 120],
                ],
            ],
            'facts' => [
                'label' => 'Data perusahaan',
                'preview' => 'profile',
                'description' => 'Ringkasan di bawah foto profil. Setiap data berisi judul dan isi.',
                'fields' => [
                    'facts_items' => [
                        'label' => 'Daftar data', 'type' => 'cards', 'item_label' => 'Data', 'min_items' => 1, 'max_items' => 8, 'max_title' => 40, 'max_text' => 100,
                        'default' => [
                            ['title' => 'Nama Perusahaan', 'text' => 'PT. Baliyoni Saguna'],
                            ['title' => 'Berdiri', 'text' => '3 Mei 2000'],
                            ['title' => 'Kedudukan', 'text' => 'Bali, Indonesia'],
                            ['title' => 'Anak Perusahaan', 'text' => '46 anak perusahaan'],
                            ['title' => 'Platform Marketplace', 'text' => 'Balimall.id'],
                        ],
                    ],
                ],
            ],
            'direction' => [
                'label' => 'Visi dan misi',
                'fields' => [
                    'direction_label' => ['label' => 'Label kecil', 'default' => 'Arah Perusahaan', 'max' => 40],
                    'direction_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Visi dan Misi', 'max' => 60],
                    'direction_tagline' => ['label' => 'Kalimat pengantar', 'type' => 'textarea', 'default' => 'Arah dan komitmen yang menjadi pedoman seluruh kegiatan perusahaan.', 'max' => 200],
                    'vision_text' => ['label' => 'Visi', 'type' => 'textarea', 'default' => 'Menjadi perusahaan penyedia barang dan jasa yang terlengkap di Indonesia, berbasis edukasi, inovasi, dan layanan purna jual.', 'max' => 300],
                    'motto_text' => ['label' => 'Moto', 'type' => 'title', 'default' => "One Stop Solution.\n*Solusi Tuntas, Bahagia Pantas.*", 'max' => 120, 'rows' => 2, 'hint' => 'Enter untuk pindah baris. Bawaannya baris kedua berwarna merah.'],
                    'mission_items' => [
                        'label' => 'Daftar misi', 'type' => 'cards', 'item_label' => 'Misi', 'min_items' => 1, 'max_items' => 8, 'max_title' => 40, 'max_text' => 250,
                        'default' => [
                            ['title' => 'People', 'text' => 'Membangun tim yang sehat fisik, mental, dan memiliki karakter yang tangguh, berpikir inovatif, serta melayani dengan tulus.'],
                            ['title' => 'Process', 'text' => 'Menjalankan proses pengadaan barang dan jasa secara akuntabel, efektif, efisien, dan transparan.'],
                            ['title' => 'Product/Service', 'text' => 'Memastikan barang dan jasa sesuai dengan spesifikasi, tepat waktu, dan disertai jaminan purna jual.'],
                            ['title' => 'Profit', 'text' => 'Memelihara profitabilitas melalui praktik bisnis yang bersih, etis, dan mengutamakan kebermanfaatan jangka panjang.'],
                            ['title' => 'Purpose', 'text' => 'Mencapai keuntungan yang selaras dengan nilai-nilai keluarga, sosial, dan masyarakat.'],
                        ],
                    ],
                ],
            ],
            'values' => [
                'label' => 'Nilai perusahaan',
                'fields' => [
                    'values_eyebrow' => ['label' => 'Label kecil', 'default' => 'Nilai Perusahaan', 'max' => 40],
                    'values_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Prinsip yang Kami Pegang', 'max' => 80],
                    'values_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Nilai yang menjadi landasan kami dalam bekerja dan melayani.', 'max' => 200],
                    'values_items' => [
                        'label' => 'Daftar nilai', 'type' => 'cards', 'item_label' => 'Nilai', 'min_items' => 1, 'max_items' => 5, 'max_title' => 40, 'max_text' => 150,
                        'hint' => 'Tampilan dirancang untuk hingga 5 nilai.',
                        'default' => [
                            ['title' => 'Integritas', 'text' => 'Jujur dan konsisten dalam setiap keputusan dan transaksi.'],
                            ['title' => 'Profesionalisme', 'text' => 'Bekerja dengan standar yang jelas dan penuh tanggung jawab.'],
                            ['title' => 'Pelayanan Prima', 'text' => 'Menempatkan kebutuhan pelanggan sebagai prioritas utama.'],
                            ['title' => 'Efisiensi', 'text' => 'Proses yang ringkas, cepat, dan tepat sasaran.'],
                            ['title' => 'Kolaborasi', 'text' => 'Bertumbuh bersama mitra, holding, dan outlet Baliyoni Group.'],
                        ],
                    ],
                ],
            ],
            'seo' => [
                'label' => 'Mesin pencari',
                'preview' => false,
                'note' => 'Bagian ini tidak tampil di halaman; hanya dibaca mesin pencari.',
                'fields' => [
                    'seo_description' => ['label' => 'Deskripsi singkat untuk Google', 'type' => 'textarea', 'default' => 'Profil, visi, misi, dan nilai-nilai PT Baliyoni Saguna, perusahaan induk Baliyoni Group.', 'max' => 200, 'hint' => 'Tidak tampil di halaman; dipakai mesin pencari.'],
                ],
            ],
        ],
    ],

    'services' => [
        'label' => 'Layanan',
        'description' => 'Teks pada halaman Layanan. Daftar kategori dan aktivitas usaha diatur lewat menu Kategori Usaha dan Aktivitas Usaha.',
        'sections' => [
            'hero' => [
                'label' => 'Bagian utama',
                'description' => 'Blok kata lalu klik Warnai merah pada label kecil untuk memberi aksen.',
                'fields' => [
                    'hero_label' => ['label' => 'Label kecil', 'type' => 'title', 'default' => 'Layanan *&* Produk', 'max' => 50, 'rows' => 1],
                    'hero_title' => ['label' => 'Judul', 'type' => 'title', 'default' => "Solusi Lengkap untuk\n*Setiap Kebutuhan*", 'max' => 120, 'rows' => 2, 'hint' => 'Enter untuk pindah baris. Blok kata lalu klik Warnai merah (bawaannya baris kedua berwarna merah).'],
                    'hero_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Dari peralatan elektronik dan kantor hingga AC, software, dan sistem parkir elektronik, semua dalam satu pintu.', 'max' => 300],
                    'hero_image' => ['label' => 'Foto latar', 'type' => 'image', 'default' => 'https://images.unsplash.com/photo-1470075801209-17f9ec0cada6?auto=format&fit=crop&w=1920&h=900&q=75', 'hint' => 'JPG, PNG, atau WebP, maksimal 4 MB. Gunakan foto lebar.'],
                ],
            ],
            'scope' => [
                'label' => 'Ruang lingkup',
                'description' => 'Kotak kategori di bawah judul ini diatur lewat menu Kategori Usaha.',
                'fields' => [
                    'scope_eyebrow' => ['label' => 'Label kecil', 'default' => 'Ruang Lingkup', 'max' => 40],
                    'scope_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Produk dan Layanan Kami', 'max' => 80],
                    'scope_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Beragam kategori yang dapat dipenuhi dalam satu tempat.', 'max' => 200],
                    'scope_hint' => ['label' => 'Petunjuk sebelum kategori dipilih', 'default' => 'Pilih salah satu kategori di atas untuk melihat aktivitas usahanya.', 'max' => 120],
                ],
            ],
            'seo' => [
                'label' => 'Mesin pencari',
                'preview' => false,
                'note' => 'Bagian ini tidak tampil di halaman; hanya dibaca mesin pencari.',
                'fields' => [
                    'seo_description' => ['label' => 'Deskripsi singkat untuk Google', 'type' => 'textarea', 'default' => 'Produk dan layanan PT Baliyoni Saguna: dari peralatan elektronik dan kantor hingga AC, software, dan sistem parkir elektronik.', 'max' => 200, 'hint' => 'Tidak tampil di halaman; dipakai mesin pencari.'],
                ],
            ],
        ],
    ],

    'gallery' => [
        'label' => 'Galeri',
        'description' => 'Teks pada halaman Galeri. Foto-fotonya diatur lewat menu Foto Galeri.',
        'sections' => [
            'hero' => [
                'label' => 'Bagian utama',
                'fields' => [
                    'hero_eyebrow' => ['label' => 'Label kecil', 'default' => 'Galeri', 'max' => 40],
                    'hero_title' => ['label' => 'Judul', 'type' => 'title', 'default' => "Jejak Visual\n*Baliyoni Saguna*", 'max' => 120, 'rows' => 2, 'hint' => 'Enter untuk pindah baris. Blok kata lalu klik Warnai merah (bawaannya baris kedua berwarna merah).'],
                    'hero_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Dokumentasi kantor, produk, dan layanan kami dalam satu galeri.', 'max' => 300],
                ],
            ],
            'photos' => [
                'label' => 'Galeri foto',
                'preview_max' => 420, // foto bisa sangat banyak; tinggi pratinjau dibatasi (px)
                'fields' => [
                    'photos_eyebrow' => ['label' => 'Label kecil', 'default' => 'Galeri Foto', 'max' => 40],
                    'photos_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Dokumentasi Kami', 'max' => 80],
                    'photos_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Klik foto untuk melihat tampilan yang lebih besar.', 'max' => 200],
                ],
            ],
            'seo' => [
                'label' => 'Mesin pencari',
                'preview' => false,
                'note' => 'Bagian ini tidak tampil di halaman; hanya dibaca mesin pencari.',
                'fields' => [
                    'seo_description' => ['label' => 'Deskripsi singkat untuk Google', 'type' => 'textarea', 'default' => 'Galeri foto PT Baliyoni Saguna: kantor, produk, dan layanan kami.', 'max' => 200, 'hint' => 'Tidak tampil di halaman; dipakai mesin pencari.'],
                ],
            ],
        ],
    ],

    'contact' => [
        'label' => 'Kontak',
        'description' => 'Teks pada halaman Kontak. Telepon, email, dan alamat diatur lewat menu Umum.',
        'sections' => [
            'hero' => [
                'label' => 'Bagian utama',
                'fields' => [
                    'hero_label' => ['label' => 'Label kecil', 'default' => 'Kontak Kami', 'max' => 40],
                    'hero_title' => ['label' => 'Judul', 'type' => 'title', 'default' => "Wujudkan Kebutuhan\n*Bersama Kami*", 'max' => 120, 'rows' => 2, 'hint' => 'Enter untuk pindah baris. Blok kata lalu klik Warnai merah (bawaannya baris kedua berwarna merah).'],
                    'hero_description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Sampaikan kebutuhan atau pertanyaan Anda. Tim kami siap membantu dan akan menjawab secepatnya.', 'max' => 300],
                    'hero_button_form' => ['label' => 'Tombol ke formulir', 'default' => 'Kirim Pesan', 'max' => 30],
                    'hero_button_map' => ['label' => 'Tombol ke peta', 'default' => 'Lihat Lokasi', 'max' => 30, 'hint' => 'Tidak tampil bila alamat dikosongkan di menu Umum.'],
                    'hero_image' => ['label' => 'Foto latar', 'type' => 'image', 'default' => 'https://images.unsplash.com/photo-1699884745550-07d309f2f61d?auto=format&fit=crop&w=1920&h=1080&q=75', 'hint' => 'JPG, PNG, atau WebP, maksimal 4 MB. Gunakan foto lebar.'],
                ],
            ],
            'info' => [
                'label' => 'Informasi kontak',
                'description' => 'Telepon, email, dan alamat pada daftar di bawah judul ini mengikuti menu Umum.',
                'fields' => [
                    'info_eyebrow' => ['label' => 'Label kecil', 'default' => 'Informasi Kontak', 'max' => 40],
                    'info_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Jangkau Kami dengan Mudah', 'max' => 80],
                ],
            ],
            'location' => [
                'label' => 'Lokasi',
                'description' => 'Bagian peta hanya tampil bila alamat terisi di menu Umum.',
                'fields' => [
                    'location_eyebrow' => ['label' => 'Label kecil', 'default' => 'Lokasi', 'max' => 40],
                    'location_title' => ['label' => 'Judul', 'type' => 'title', 'rows' => 1, 'default' => 'Kantor Kami', 'max' => 60],
                    'location_link' => ['label' => 'Tautan ke Google Maps', 'default' => 'Buka di Google Maps', 'max' => 40],
                ],
            ],
            'seo' => [
                'label' => 'Mesin pencari',
                'preview' => false,
                'note' => 'Bagian ini tidak tampil di halaman; hanya dibaca mesin pencari.',
                'fields' => [
                    'seo_description' => ['label' => 'Deskripsi singkat untuk Google', 'type' => 'textarea', 'default' => 'Hubungi PT Baliyoni Saguna: telepon, WhatsApp, email, alamat kantor, dan formulir pesan.', 'max' => 200, 'hint' => 'Tidak tampil di halaman; dipakai mesin pencari.'],
                ],
            ],
        ],
    ],

    // Data yang tampil di banyak halaman sekaligus (navbar, footer, CTA, halaman login). Menunya "Umum".
    'general' => [
        'label' => 'Umum',
        'description' => 'Logo, kontak, dan footer yang tampil di semua halaman website.',
        'sections' => [
            'identity' => [
                'label' => 'Identitas',
                'description' => 'Logo dan nama perusahaan di navbar, footer, halaman login, dan menu admin.',
                'preview' => false,
                'note' => 'Perubahan tampil di navbar, footer, dan halaman login setelah Anda menekan Simpan.',
                'fields' => [
                    'logo' => ['label' => 'Logo perusahaan', 'type' => 'image', 'preview_class' => 'size-28', 'fit' => 'object-contain', 'default' => '/images/logo.png', 'hint' => 'JPG, PNG, atau WebP, maksimal 4 MB. Disarankan berbentuk persegi, berlatar transparan, minimal 256 px.'],
                    'brand_text' => ['label' => 'Tulisan di samping logo', 'type' => 'title', 'default' => 'PT. Bal*i*yoni Saguna', 'max' => 40, 'rows' => 1, 'hint' => 'Satu baris. Blok huruf atau kata lalu klik Warnai merah untuk memberi aksen.'],
                    'site_name' => ['label' => 'Nama resmi perusahaan', 'default' => 'PT Baliyoni Saguna', 'max' => 80, 'hint' => 'Dipakai pada judul tab browser, hasil pencarian, dan hak cipta.'],
                    'tagline' => ['label' => 'Slogan singkat', 'default' => 'Holding company Baliyoni Group.', 'max' => 100, 'hint' => 'Dipakai sebagai deskripsi mesin pencari bila halaman tidak punya deskripsi sendiri.'],
                ],
            ],
            'contact' => [
                'label' => 'Kontak',
                'description' => 'Tampil di footer, ajakan menghubungi, dan halaman Kontak. Kolom yang dikosongkan tidak ditampilkan.',
                'preview' => false,
                'note' => 'Perubahan tampil di footer, ajakan menghubungi, dan halaman Kontak setelah Anda menekan Simpan.',
                'fields' => [
                    'email' => ['label' => 'Email', 'default' => 'info@baliyoni.co.id', 'max' => 100, 'optional' => true, 'rules' => ['email']],
                    'phone' => ['label' => 'Telepon kantor', 'default' => '(0361) 257055', 'max' => 30, 'optional' => true, 'rules' => ['regex:/^[0-9+()\s.\-]+$/'], 'hint' => 'Boleh berisi spasi, tanda kurung, dan strip. Nomor berawalan 0 otomatis diubah ke format internasional (+62) untuk tombol telepon.'],
                    'mobile' => ['label' => 'Nomor WhatsApp', 'default' => '0822 7226 9888', 'max' => 30, 'optional' => true, 'rules' => ['regex:/^[0-9+()\s.\-]+$/'], 'hint' => 'Dipakai tombol WhatsApp dan formulir kontak.'],
                    'address' => ['label' => 'Alamat', 'type' => 'textarea', 'default' => 'Jl. Tukad Batanghari No.1C, Panjer, Denpasar Selatan, Kota Denpasar, Bali', 'max' => 250, 'optional' => true, 'hint' => 'Juga dipakai untuk peta Google Maps.'],
                    'city' => ['label' => 'Kota dan negara', 'default' => 'Bali, Indonesia', 'max' => 60, 'hint' => 'Tampil di bagian bawah footer.'],
                ],
            ],
            'cta' => [
                'label' => 'CTA',
                'title' => 'Ajakan menghubungi',
                'description' => 'Ajakan di atas footer punya dua variasi tampilan. Telepon, email, dan alamat pada ajakan mengikuti bagian Kontak.',
                // Satu bagian, dua variasi: tiap kelompok punya pratinjau sendiri (target 'preview' = nama bagian di web/general.blade.php).
                'groups' => [
                    'card' => ['label' => 'Variasi 1: Kartu', 'description' => 'Kartu putih dengan ringkasan kontak. Tampil di halaman Beranda dan Layanan.', 'preview' => 'cta_card'],
                    'banner' => ['label' => 'Variasi 2: Banner', 'description' => 'Pita penuh berlatar foto. Tampil di halaman Tentang Kami dan detail aktivitas usaha.', 'preview' => 'cta_banner'],
                ],
                'fields' => [
                    'cta_card_eyebrow' => ['group' => 'card', 'label' => 'Label kecil', 'default' => 'Hubungi Kami', 'max' => 40],
                    'cta_card_title' => ['group' => 'card', 'label' => 'Judul', 'type' => 'title', 'default' => 'Butuh solusi untuk *kebutuhan Anda?*', 'max' => 100, 'rows' => 2],
                    'cta_card_description' => ['group' => 'card', 'label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Sampaikan kebutuhan Anda kepada kami, tim kami siap membantu.', 'max' => 200],
                    'cta_card_button' => ['group' => 'card', 'label' => 'Tombol utama', 'default' => 'Kontak Kami', 'max' => 30, 'hint' => 'Mengarah ke halaman Kontak.'],
                    'cta_card_button_phone' => ['group' => 'card', 'label' => 'Tombol telepon', 'default' => 'Telepon Kami', 'max' => 30, 'hint' => 'Memanggil nomor telepon kantor; tidak tampil bila nomor telepon dikosongkan.'],
                    'cta_banner_eyebrow' => ['group' => 'banner', 'label' => 'Label kecil', 'default' => 'Hubungi Kami', 'max' => 40],
                    'cta_banner_title' => ['group' => 'banner', 'label' => 'Judul', 'type' => 'title', 'default' => 'Butuh solusi untuk *kebutuhan Anda?*', 'max' => 100, 'rows' => 2],
                    'cta_banner_description' => ['group' => 'banner', 'label' => 'Deskripsi', 'type' => 'textarea', 'default' => 'Sampaikan kebutuhan Anda kepada kami, tim kami siap membantu.', 'max' => 200],
                    'cta_banner_button' => ['group' => 'banner', 'label' => 'Tombol utama', 'default' => 'Kontak Kami', 'max' => 30, 'hint' => 'Mengarah ke halaman Kontak.'],
                    'cta_banner_button_phone' => ['group' => 'banner', 'label' => 'Tombol telepon', 'default' => 'Telepon Kami', 'max' => 30, 'hint' => 'Memanggil nomor telepon kantor; tidak tampil bila nomor telepon dikosongkan.'],
                    'cta_banner_image' => ['group' => 'banner', 'label' => 'Foto latar', 'type' => 'image', 'default' => 'https://images.unsplash.com/photo-1512403754473-27835f7b9984?auto=format&fit=crop&w=1920&h=700&q=75', 'hint' => 'JPG, PNG, atau WebP, maksimal 4 MB. Gunakan foto lebar; bagian tepi dapat terpotong dan ditutup lapisan gelap.'],
                ],
            ],
            'footer' => [
                'label' => 'Footer',
                'preview' => false,
                'note' => 'Perubahan tampil di footer setelah Anda menekan Simpan.',
                'fields' => [
                    'footer_description' => ['label' => 'Deskripsi perusahaan', 'type' => 'textarea', 'default' => 'Perusahaan induk dari berbagai holding dan outlet Baliyoni Group, menghadirkan beragam produk dan layanan dalam satu pintu.', 'max' => 300],
                ],
            ],
        ],
    ],
];
