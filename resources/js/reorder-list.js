/**
 * Atur urutan item admin (baris tabel kategori/aktivitas, atau kotak kisi bento galeri) dengan seret (pointer
 * events: mouse, sentuh, stylus) atau tombol panah. Perubahan hanya ditampung di layar; baru dikirim ke
 * server lewat form biasa saat admin menekan "Simpan urutan".
 *
 * Markup yang dibutuhkan: <form x-ref="form"> berisi <div x-ref="fields"> kosong, dan kontainer
 * x-ref="body" berisi elemen [data-id] (mis. <tr>) dengan sel [data-position] untuk nomor urut.
 * Berkelompok: beri tiap item data-group (mis. id kategori); item hanya bisa dipindah di dalam kelompoknya dan
 * dikirim sebagai orders[kelompok][].
 * Kisi: beri kontainer data-layout="grid" agar arah seret dibaca dua dimensi; beri data-spans (JSON daftar
 * class ukuran per posisi) agar ukuran kotak mengikuti posisinya, bukan fotonya.
 */
export default (Alpine) => () => ({
    dirty: false,
    saving: false,
    dragRow: null,
    pointerId: null,
    lastY: 0,
    scrollTimer: null,
    snapshot: '',

    init() {
        // Cegah kehilangan urutan yang belum disimpan saat halaman ditinggalkan.
        window.addEventListener('beforeunload', (event) => {
            if (this.dirty && !this.saving) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
    },

    rows() {
        return Array.from(this.$refs.body.querySelectorAll('[data-id]'));
    },

    signature() {
        return this.rows().map((row) => row.dataset.id).join();
    },

    /**
     * Mulai menyeret. Mouse: dari mana saja di item (kecuali tautan/tombol). Sentuh dan stylus: hanya dari
     * pegangan (data-drag-handle) agar halaman tetap bisa digulir dengan jari di bagian lain, kecuali kontainer
     * memakai data-drag-anywhere.
     */
    down(event) {
        const row = event.target.closest('[data-id]');

        if (!row) return;

        const handle = event.target.closest('[data-drag-handle]');
        const interactive = event.target.closest('a, button, input, select, textarea, label');
        // Kisi galeri (data-drag-anywhere): seluruh foto bisa diseret, termasuk dengan jari.
        const free = this.$refs.body.hasAttribute('data-drag-anywhere');

        if (event.pointerType === 'mouse') {
            if (event.button !== 0) return;
            if (!handle && interactive) return;
        } else if (!handle && (!free || interactive)) {
            return;
        }

        event.preventDefault();

        this.dragRow = row;
        this.pointerId = event.pointerId;
        this.lastY = event.clientY;
        this.snapshot = this.signature();

        row.classList.add('opacity-60', 'shadow-lg', 'relative', 'z-10');
        this.$refs.body.setPointerCapture?.(event.pointerId);

        clearInterval(this.scrollTimer);
        this.scrollTimer = setInterval(() => this.autoScroll(), 16);
    },

    drag(event) {
        if (!this.dragRow || event.pointerId !== this.pointerId) return;

        event.preventDefault();
        this.lastY = event.clientY;

        const under = document.elementFromPoint(event.clientX, event.clientY);
        const target = under?.closest('[data-id]');

        // Item hanya bisa dipindah di dalam kelompoknya (mis. aktivitas di dalam kategorinya sendiri).
        if (!target || target === this.dragRow || target.dataset.group !== this.dragRow.dataset.group) return;

        const rect = target.getBoundingClientRect();
        const dx = event.clientX - (rect.left + rect.width / 2);
        const dy = event.clientY - (rect.top + rect.height / 2);

        // Tabel: hanya arah vertikal. Kisi: arah yang dominan menentukan sebelum/sesudah target.
        const grid = this.$refs.body.dataset.layout === 'grid';
        const after = grid && Math.abs(dx) > Math.abs(dy) * 0.6 ? dx > 0 : dy > 0;

        after ? target.after(this.dragRow) : target.before(this.dragRow);
        this.applySpans();
    },

    drop(event) {
        if (!this.dragRow || (event && event.pointerId !== this.pointerId)) return;

        const row = this.dragRow;

        clearInterval(this.scrollTimer);
        this.scrollTimer = null;
        this.dragRow = null;

        row.classList.remove('opacity-60', 'shadow-lg', 'relative', 'z-10');
        this.$refs.body.releasePointerCapture?.(this.pointerId);

        if (this.snapshot !== this.signature()) this.stage();
    },

    /** Gulir halaman otomatis saat item diseret mendekati tepi atas/bawah layar. */
    autoScroll() {
        if (!this.dragRow) return;

        const edge = 90;
        const top = 120;

        if (this.lastY < top) {
            window.scrollBy(0, -Math.ceil((top - this.lastY) / 6));
        } else if (this.lastY > window.innerHeight - edge) {
            window.scrollBy(0, Math.ceil((this.lastY - (window.innerHeight - edge)) / 4));
        }
    },

    move(event, delta) {
        const row = event.target.closest('[data-id]');
        const siblings = row ? this.rows().filter((item) => item.dataset.group === row.dataset.group) : [];
        const target = row && siblings[siblings.indexOf(row) + delta];

        if (!target) return;

        delta < 0 ? target.before(row) : target.after(row);
        row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });

        this.stage();
    },

    /** Kisi bento: ukuran kotak mengikuti posisi, jadi class ukuran dipasang ulang sesuai urutan baru. */
    applySpans() {
        const spans = JSON.parse(this.$refs.body.dataset.spans || '[]');

        if (spans.length === 0) return;

        this.rows().forEach((row, index) => {
            const next = spans[index % spans.length] ?? '';

            (row.dataset.span ?? '').split(' ').filter(Boolean).forEach((name) => row.classList.remove(name));
            next.split(' ').filter(Boolean).forEach((name) => row.classList.add(name));
            row.dataset.span = next;
        });
    },

    /** Tandai urutan berubah (belum disimpan) dan perbarui nomor urut di layar. */
    stage() {
        this.dirty = true;
        this.applySpans();

        // Nomor urut dihitung per kelompok (tanpa data-group, seluruh daftar adalah satu kelompok).
        const counters = {};

        this.rows().forEach((row) => {
            const group = row.dataset.group ?? '';
            counters[group] = (counters[group] ?? 0) + 1;

            row.querySelectorAll('[data-position]').forEach((cell) => {
                cell.textContent = counters[group];
            });
        });
    },

    /** Kirim urutan lewat form biasa (tanpa fetch); server memvalidasi dan mengarahkan kembali ke daftar. */
    save() {
        this.saving = true;
        this.$refs.fields.replaceChildren(
            ...this.rows().map((row) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                // Berkelompok: orders[id_kelompok][]; selain itu order[].
                input.name = row.dataset.group !== undefined ? `orders[${row.dataset.group}][]` : 'order[]';
                input.value = row.dataset.id;

                return input;
            }),
        );
        this.$refs.form.submit();
    },

    /** Keluar dari mode atur urutan; bila ada perubahan, tawarkan simpan atau buang. */
    async leave(url) {
        if (!this.dirty) {
            window.location.href = url;

            return;
        }

        const choice = await Alpine.store('confirm').ask({
            title: 'Keluar dari atur urutan?',
            message: 'Urutan baru belum disimpan. Simpan atau buang perubahan ini?',
            label: 'Ya, simpan',
            secondaryLabel: 'Buang perubahan',
            hideCancel: true,
        });

        if (choice === 'secondary') {
            this.dirty = false;
            window.location.href = url;
        } else if (choice === true) {
            this.save();
        }
    },
});
