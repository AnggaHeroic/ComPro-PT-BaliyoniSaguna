/**
 * Pemilih ikon kategori (dropdown berisi kisi ikon). value = kunci ikon terpilih, labels = kunci => nama.
 * Nilai dikirim lewat <input type="hidden"> di komponen x-admin.icon-picker.
 */
export default ({ value, labels }) => ({
    open: false,
    value,
    labels,

    choose(key) {
        this.value = key;
        this.open = false;
    },
});
