/**
 * Unggah banyak foto sekaligus dengan pratinjau. Foto bisa dipilih lewat dialog file atau diseret ke area unggah.
 * Dipakai komponen x-admin.photo-upload; file dikirim lewat form biasa (input file bernama images[]).
 */
export default ({ max }) => ({
    max,
    previews: [],
    dragging: false,

    pick(event) {
        this.show(Array.from(event.target.files));
    },

    drop(event) {
        this.dragging = false;

        const input = this.$refs.input;
        const transfer = new DataTransfer();

        Array.from(event.dataTransfer.files)
            .filter((file) => file.type.startsWith('image/'))
            .slice(0, this.max)
            .forEach((file) => transfer.items.add(file));

        input.files = transfer.files;
        this.show(Array.from(input.files));
    },

    show(files) {
        this.previews.forEach((item) => URL.revokeObjectURL(item.url));
        this.previews = files.slice(0, this.max).map((file) => ({ name: file.name, url: URL.createObjectURL(file) }));
    },

    clear() {
        this.$refs.input.value = '';
        this.show([]);
    },
});
