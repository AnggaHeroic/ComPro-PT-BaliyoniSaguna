/**
 * Pratinjau satu bagian halaman publik di form isi halaman.
 *
 * Isian form (yang belum disimpan) dikirim lewat pengiriman form biasa ke iframe, tanpa fetch: form
 * sementara diarahkan ke rute pratinjau, dikirim ke iframe, lalu dikembalikan seperti semula. Server
 * merender halaman publik dan menyisakan bagian itu saja. Iframe selebar 1280 px lalu diperkecil agar muat.
 */
const DESKTOP_WIDTH = 1280;
// Skala terbesar pratinjau (setara kotak 720 px). Kotak yang lebih lebar tidak memperbesar gambar, tetapi memperlebar
// area tampil iframe, sehingga pratinjau melebar ke samping tanpa bertambah tinggi.
const MAX_SCALE = 0.5625;

export default ({ url, section, tab, maxHeight = null }) => ({
    open: true,
    loading: false,
    requested: false,
    dirty: false, // ada isian yang diubah sejak halaman dibuka
    scale: 0.5,
    frameWidth: DESKTOP_WIDTH,
    height: 400,
    timer: null,
    frameName: `preview-${tab}-${section}`, // sama dengan atribut name iframe di komponen (harus ada sejak HTML dimuat, bukan lewat binding); unik per tab dan bagian

    /** Tinggi kotak: setinggi isi, tetapi tidak melebihi batas (bila ada). */
    get boxHeight() {
        const natural = Math.max(this.height * this.scale, 120);

        return maxHeight ? Math.min(natural, maxHeight) : natural;
    },

    /** Isi lebih panjang dari batas, jadi bagian bawahnya terpotong. */
    get cropped() {
        return Boolean(maxHeight) && this.height * this.scale > maxHeight;
    },

    init() {
        const form = this.$el.closest('form');
        const edited = () => {
            this.dirty = true;

            // Hanya bagian yang sedang tampil yang diperbarui.
            if (this.$el.offsetParent !== null) this.schedule(900);
        };

        form.addEventListener('input', edited);
        form.addEventListener('change', edited);

        new ResizeObserver(() => this.rescale()).observe(this.$refs.box);
    },

    schedule(delay = 0) {
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.load(), delay);
    },

    rescale() {
        const width = this.$refs.box.clientWidth;

        if (width <= 0) return;

        this.scale = Math.min(width / DESKTOP_WIDTH, MAX_SCALE);

        const frameWidth = Math.round(width / this.scale);

        if (frameWidth === this.frameWidth) return;

        // Lebar iframe berubah, jadi isi halaman tersusun ulang dan tingginya perlu diukur lagi.
        this.frameWidth = frameWidth;
        this.$refs.frame.style.width = `${frameWidth}px`;
        this.fit();
    },

    load() {
        const form = this.$el.closest('form');
        const original = { action: form.getAttribute('action'), target: form.getAttribute('target'), enctype: form.enctype };

        // File, penanda PUT, dan penanda bagian tidak ikut ke pratinjau.
        const skipped = [...form.elements].filter((el) => !el.disabled && (el.type === 'file' || el.name === '_method' || el.name === 'bagian'));

        this.loading = true;
        this.requested = true;
        skipped.forEach((el) => (el.disabled = true));

        form.setAttribute('action', url);
        form.setAttribute('target', this.frameName);
        form.enctype = 'application/x-www-form-urlencoded';
        form.submit(); // submit() tidak memicu event submit, jadi dialog konfirmasi dan penjaga perubahan tidak ikut jalan.

        form.setAttribute('action', original.action);
        original.target === null ? form.removeAttribute('target') : form.setAttribute('target', original.target);
        form.enctype = original.enctype;
        skipped.forEach((el) => (el.disabled = false));
    },

    /** Setelah dokumen (dan gambarnya) selesai dimuat: sesuaikan tinggi iframe dengan isinya. */
    measure() {
        const frame = this.$refs.frame;
        const doc = frame.contentDocument;

        if (!this.requested || !doc?.body) return;

        this.loading = false;
        this.fit();
    },

    /** Sesuaikan tinggi iframe dengan isi bagian yang dipratinjau. */
    fit() {
        const frame = this.$refs.frame;
        const doc = frame.contentDocument;

        if (!this.requested || !doc?.body) return;

        // Yang diukur bagian yang dipratinjau, bukan body (body punya tinggi minimum satu layar sehingga menyisakan ruang kosong).
        // Dua kali ukur: bagian dengan tinggi minimum (mis. hero) menyesuaikan diri dengan tinggi viewport iframe.
        const target = doc.querySelector(`[data-cms-section="${section}"]`) ?? doc.body;
        const bodyHeight = () => Math.ceil(target.getBoundingClientRect().height);

        frame.style.height = '900px';
        frame.style.height = `${bodyHeight()}px`;

        this.height = bodyHeight();
        frame.style.height = `${this.height}px`;
    },
});
