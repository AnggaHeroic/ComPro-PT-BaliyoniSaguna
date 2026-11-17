/**
 * Daftar kartu (judul + uraian) yang bisa ditambah, dihapus, dan diurutkan. Dipakai komponen x-admin.card-list;
 * kartu kosong dibuang server saat disimpan.
 */
export default ({ items, min, max }) => ({
    items,
    min,
    max,

    add() {
        if (this.items.length < this.max) this.items.push({ title: '', text: '' });
        this.changed();
    },

    remove(index) {
        if (this.items.length > this.min) this.items.splice(index, 1);
        this.changed();
    },

    move(from, to) {
        if (to < 0 || to >= this.items.length) return;

        this.items.splice(to, 0, this.items.splice(from, 1)[0]);
        this.changed();
    },

    /** Menambah, menghapus, dan memindah kartu tidak memicu event input; kabari form agar perubahannya terdeteksi. */
    changed() {
        this.$nextTick(() => this.$el.dispatchEvent(new Event('change', { bubbles: true })));
    },
});
