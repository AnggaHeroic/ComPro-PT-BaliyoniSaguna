// Perilaku bagian Ruang Lingkup: memilih kategori menampilkan aktivitas usahanya di bagian putih di bawahnya.
// Semua panel sudah ada di HTML (hanya disembunyikan), sehingga isinya tetap terbaca mesin pencari.
const PREFIX = 'ruang-lingkup-';

const behavior = () => (window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth');

export default () => ({
    active: null,
    // Tombol kembali tampil setelah bagian aktivitas dicapai dan tetap tampil sampai melewati footer.
    showBack: false,

    init() {
        // Memberi tahu navbar kategori mana yang aktif agar menu Layanan ikut menandainya.
        this.$watch('active', (slug) => {
            this.$dispatch('service-selected', slug);
            this.$nextTick(() => this.onScroll());
        });
        this.fromQuery();
        this.fromHash();
    },

    // Tautan /layanan?kategori=ac-split-wall#ruang-lingkup (mis. "Lihat semua kategori" di halaman detail aktivitas) menggulir ke Ruang Lingkup
    // lewat hash biasa, sambil tetap memilih kategori terakhir sehingga aktivitasnya sudah muncul di bawahnya. Hash kategori (fromHash) menang bila ada.
    fromQuery() {
        const slug = new URLSearchParams(location.search).get('kategori');

        if (slug && this.$root.querySelector(`[data-slug="${CSS.escape(slug)}"]`)) this.active = slug;
    },

    // Tautan seperti /layanan#ruang-lingkup-ac-split-wall (mis. dari menu Layanan di navbar) langsung membuka kategori terkait,
    // baik saat halaman dimuat maupun saat hash berubah di halaman yang sama (dipanggil juga dari @hashchange.window).
    fromHash() {
        const hash = decodeURIComponent(location.hash.slice(1));
        if (!hash.startsWith(PREFIX)) return;

        const slug = hash.slice(PREFIX.length);
        if (!this.$root.querySelector(`[data-slug="${CSS.escape(slug)}"]`)) return;

        this.active = slug;
        this.$nextTick(() => this.reveal());
    },

    // Mengetuk kategori yang sama sekali lagi menutup panelnya.
    select(slug) {
        this.active = this.active === slug ? null : slug;
        // Query ?kategori dibuang agar muat ulang tidak memilih ulang kategori lama.
        history.replaceState(null, '', location.pathname + (this.active ? `#${PREFIX}${slug}` : ''));

        if (this.active) this.$nextTick(() => this.reveal());
    },

    // Bagian aktivitas digulirkan ke layar hanya bila posisinya di luar jangkauan pandang (umum terjadi di ponsel).
    reveal() {
        const top = this.$refs.panel.getBoundingClientRect().top;
        if (top > window.innerHeight * 0.6 || top < 80) {
            this.$refs.panel.scrollIntoView({ behavior: behavior(), block: 'start' });
        }
    },

    onScroll() {
        this.showBack = this.active !== null && this.$refs.panel.getBoundingClientRect().top < window.innerHeight * 0.5;
    },

    backToScope() {
        this.$root.scrollIntoView({ behavior: behavior(), block: 'start' });
    },
});
