/**
 * Form isi halaman admin: tab per bagian. Hanya bagian yang dipilih yang tampil,
 * tetapi semua bagian tetap ikut terkirim saat Simpan.
 */
export default ({ active, sections }) => ({
    active,
    sections,

    select(key) {
        if (!this.sections.includes(key)) return;

        this.active = key;
        this.$nextTick(() => this.$refs.top?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
    },

    /**
     * Kolom wajib yang kosong di bagian yang sedang disembunyikan tidak bisa difokuskan browser;
     * buka bagiannya lebih dulu, lalu tampilkan pesannya.
     */
    onInvalid(event) {
        const section = event.target.closest('[data-section]')?.dataset.section;

        if (section && section !== this.active) {
            this.active = section;
            this.$nextTick(() => event.target.reportValidity());
        }
    },
});
