// Pratinjau foto galeri (lightbox) dengan zoom dan geser. Logikanya diadaptasi dari lightbox dokumentasi PT Putra Maju Sukses:
// zoom lewat tombol, roda mouse, ketuk dua kali, atau cubit dua jari; foto yang diperbesar bisa digeser; panah keyboard untuk berpindah.
// items = daftar { title, description, src }; index null berarti panel tertutup.
export default (items = []) => ({
    items,
    index: null,

    zoom: 1,
    minZoom: 1,
    maxZoom: 3,

    translateX: 0,
    translateY: 0,

    isDragging: false,
    dragStartX: 0,
    dragStartY: 0,
    startTranslateX: 0,
    startTranslateY: 0,

    // Posisi jari/kursor yang sedang aktif (kunci = pointerId), dipakai untuk cubit dua jari.
    pointers: {},
    pinchStartDistance: 0,
    pinchStartZoom: 1,

    get isOpen() {
        return this.index !== null;
    },

    get current() {
        return this.items[this.index] ?? null;
    },

    get counter() {
        return this.isOpen ? `${this.index + 1} / ${this.items.length}` : '';
    },

    open(index) {
        if (!Number.isInteger(index) || index < 0 || index >= this.items.length) return;

        this.index = index;
        this.resetZoom();
    },

    close() {
        this.index = null;
        this.resetZoom();
    },

    next() {
        this.index = (this.index + 1) % this.items.length;
        this.resetZoom();
    },

    previous() {
        this.index = (this.index - 1 + this.items.length) % this.items.length;
        this.resetZoom();
    },

    resetZoom() {
        this.zoom = 1;
        this.translateX = 0;
        this.translateY = 0;
        this.isDragging = false;
        this.pointers = {};
    },

    zoomIn() {
        this.setZoom(this.zoom + 0.5);
    },

    zoomOut() {
        this.setZoom(this.zoom - 0.5);
    },

    toggleZoom() {
        this.zoom > 1 ? this.resetZoom() : this.setZoom(2);
    },

    setZoom(value) {
        this.zoom = Math.min(this.maxZoom, Math.max(this.minZoom, value));

        if (this.zoom === 1) {
            this.translateX = 0;
            this.translateY = 0;
        } else {
            this.clampPosition();
        }
    },

    handleWheel(event) {
        if (!this.isOpen) return;

        this.setZoom(this.zoom + (event.deltaY < 0 ? 0.25 : -0.25));
    },

    handlePointerDown(event) {
        if (!this.isOpen) return;

        this.pointers[event.pointerId] = { x: event.clientX, y: event.clientY };
        event.currentTarget.setPointerCapture(event.pointerId);

        const ids = Object.keys(this.pointers);

        // Dua jari: mulai cubit; satu jari saat diperbesar: mulai geser.
        if (ids.length === 2) {
            this.pinchStartDistance = this.pointerDistance(ids);
            this.pinchStartZoom = this.zoom;
            this.isDragging = false;

            return;
        }

        if (this.zoom > 1) {
            this.isDragging = true;
            this.dragStartX = event.clientX;
            this.dragStartY = event.clientY;
            this.startTranslateX = this.translateX;
            this.startTranslateY = this.translateY;
        }
    },

    handlePointerMove(event) {
        if (!this.isOpen || !this.pointers[event.pointerId]) return;

        this.pointers[event.pointerId].x = event.clientX;
        this.pointers[event.pointerId].y = event.clientY;

        const ids = Object.keys(this.pointers);

        if (ids.length === 2) {
            if (this.pinchStartDistance > 0) {
                this.setZoom(this.pinchStartZoom * (this.pointerDistance(ids) / this.pinchStartDistance));
            }

            return;
        }

        if (!this.isDragging || this.zoom <= 1) return;

        this.translateX = this.startTranslateX + (event.clientX - this.dragStartX);
        this.translateY = this.startTranslateY + (event.clientY - this.dragStartY);
        this.clampPosition();
    },

    handlePointerUp(event) {
        delete this.pointers[event.pointerId];

        const count = Object.keys(this.pointers).length;

        if (count < 2) this.pinchStartDistance = 0;
        if (count === 0) this.isDragging = false;
    },

    pointerDistance(ids) {
        const first = this.pointers[ids[0]];
        const second = this.pointers[ids[1]];

        return Math.hypot(second.x - first.x, second.y - first.y);
    },

    // Foto yang diperbesar tidak boleh tergeser sampai keluar bingkai.
    clampPosition() {
        const image = this.$refs.previewImage;

        if (this.zoom <= 1 || !image) {
            this.translateX = 0;
            this.translateY = 0;

            return;
        }

        const maxX = (image.clientWidth * (this.zoom - 1)) / 2;
        const maxY = (image.clientHeight * (this.zoom - 1)) / 2;

        this.translateX = Math.min(maxX, Math.max(-maxX, this.translateX));
        this.translateY = Math.min(maxY, Math.max(-maxY, this.translateY));
    },
});
