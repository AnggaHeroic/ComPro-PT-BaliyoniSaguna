/**
 * Pemilih ikon kategori (dropdown berisi kisi ikon). value = kunci ikon terpilih (kosong = belum ada), labels = kunci => nama,
 * pairedWith = name kolom unggah ikon kustom pasangannya (hanya salah satu yang berlaku), custom = pasangannya sudah berisi.
 * Memilih ikon meniadakan sementara ikon kustom; membatalkan pilihan (klik ikon terpilih atau "Kosongkan pilihan") mengembalikannya
 * selama form belum disimpan. Nilai dikirim lewat <input type="hidden"> di komponen x-admin.icon-picker.
 */
export default ({ value, labels, pairedWith = null, custom = false }) => ({
    open: false,
    value,
    labels,
    pairedWith,
    custom, // ada ikon kustom (tersimpan atau baru dipilih) yang dipakai selama tidak ada ikon daftar
    hasCustom: custom,

    choose(key) {
        this.open = false;

        if (this.value === key) {
            this.clear();

            return;
        }

        this.value = key;
        this.custom = false;

        if (this.pairedWith) this.$dispatch('upload-reset', this.pairedWith);
    },

    clear() {
        this.value = '';
        this.open = false;
        this.custom = this.hasCustom;

        if (this.pairedWith) this.$dispatch('upload-restore', this.pairedWith);
    },

    customPicked() {
        this.value = '';
        this.custom = true;
        this.hasCustom = true;
    },
});
