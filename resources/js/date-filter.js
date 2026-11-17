/**
 * Filter tanggal satu tombol: panel berisi pintasan (hari ini, 7 hari, 30 hari), rentang tanggal, atau satu hari.
 * Nilai dikirim lewat dua input hidden (dari, sampai); form induk dikirim saat tombol Terapkan ditekan.
 * Dipakai komponen x-admin.date-filter.
 */
export default ({ from, to, presets, max }) => ({
    open: false,
    mode: from && from === to ? 'day' : 'range',
    from,
    to,
    presets,
    max,

    send() {
        this.$nextTick(() => this.$root.closest('form').submit());
    },

    apply() {
        if (this.mode === 'day') this.to = this.from;
        if (this.from && this.to && this.from > this.to) [this.from, this.to] = [this.to, this.from];

        this.send();
    },

    preset(item) {
        this.mode = item.from === item.to ? 'day' : 'range';
        this.from = item.from;
        this.to = item.to;

        this.send();
    },

    clear() {
        this.from = '';
        this.to = '';

        this.send();
    },
});
