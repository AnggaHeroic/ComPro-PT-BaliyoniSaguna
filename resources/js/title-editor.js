/**
 * Kolom judul dengan kata berwarna: teks terpilih dibungkus *...* lewat tombol "Warnai merah" dan
 * disimpan sebagai teks biasa. Menekan tombol pada kata yang sudah berwarna melepas tandanya.
 */
export default ({ text }) => ({
    text,
    needsSelection: false,

    /** Pratinjau hasil: teks di-escape lebih dulu, lalu *kata* menjadi bagian berwarna. */
    get preview() {
        const box = document.createElement('div');
        box.textContent = this.text;

        return box.innerHTML.replace(/\*(.+?)\*/gs, '<span class="text-brand">$1</span>').replace(/\n/g, '<br>');
    },

    get balanced() {
        return (this.text.split('*').length - 1) % 2 === 0;
    },

    toggle() {
        const el = this.$refs.field;
        const start = el.selectionStart;
        const end = el.selectionEnd;

        this.needsSelection = start === end;

        if (this.needsSelection) return;

        const t = this.text;
        const inner = t.slice(start, end);
        let from = start;
        let to = end;

        if (inner.length > 1 && inner.startsWith('*') && inner.endsWith('*')) {
            // Seluruh pilihan sudah berwarna: lepas tandanya.
            this.text = t.slice(0, start) + inner.slice(1, -1) + t.slice(end);
            to = end - 2;
        } else if (t[start - 1] === '*' && t[end] === '*') {
            // Pilihan berada tepat di dalam tanda: lepas tanda di kiri dan kanannya.
            this.text = t.slice(0, start - 1) + inner + t.slice(end + 1);
            from = start - 1;
            to = end - 1;
        } else {
            this.text = `${t.slice(0, start)}*${inner}*${t.slice(end)}`;
            to = end + 2;
        }

        this.$nextTick(() => {
            el.focus();
            el.setSelectionRange(from, to);
            el.dispatchEvent(new Event('input', { bubbles: true }));
        });
    },
});
