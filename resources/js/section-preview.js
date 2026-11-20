/**
 * Pratinjau satu bagian halaman publik di form isi halaman.
 *
 * Isian form (yang belum disimpan) dikirim lewat pengiriman form biasa ke iframe, tanpa fetch: form
 * sementara diarahkan ke rute pratinjau, dikirim ke iframe, lalu dikembalikan seperti semula. Server
 * merender halaman publik dan menyisakan bagian itu saja. Iframe selebar 1280 px lalu diperkecil agar muat.
 */
const DESKTOP_WIDTH = 1280;

export default ({ url, section, tab }) => ({
    open: true,
    loading: false,
    requested: false,
    dirty: false, // ada isian yang diubah sejak halaman dibuka
    scale: 0.5,
    height: 400,
    timer: null,
    frameName: `preview-${tab}-${section}`, // sama dengan atribut name iframe di komponen (harus ada sejak HTML dimuat, bukan lewat binding); unik per tab dan bagian

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

        if (width > 0) this.scale = width / DESKTOP_WIDTH;
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

        // Yang diukur bagian yang dipratinjau, bukan body (body punya tinggi minimum satu layar sehingga menyisakan ruang kosong).
        // Dua kali ukur: bagian dengan tinggi minimum (mis. hero) menyesuaikan diri dengan tinggi viewport iframe.
        const target = doc.querySelector(`[data-cms-section="${section}"]`) ?? doc.body;
        const bodyHeight = () => Math.ceil(target.getBoundingClientRect().height);

        frame.style.height = '900px';
        frame.style.height = `${bodyHeight()}px`;

        this.height = bodyHeight();
        frame.style.height = `${this.height}px`;
        this.rescale();
    },
});
