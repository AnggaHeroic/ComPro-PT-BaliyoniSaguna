// Perilaku navbar: transparan di atas hero, putih setelah scroll atau saat menu terbuka,
// serta mega-menu "Layanan" (hover/klik) dan menu mobile.
export default () => ({
    scrolled: false,
    mega: false,
    menu: false,
    closeTimer: null,

    init() {
        this.onScroll();
    },

    get solid() {
        return this.scrolled || this.mega || this.menu;
    },

    onScroll() {
        this.scrolled = window.scrollY > 8;
    },

    // Hover hanya berlaku di perangkat dengan mouse; di layar sentuh pakai klik.
    canHover() {
        return window.matchMedia('(hover: hover)').matches;
    },

    openMega() {
        clearTimeout(this.closeTimer);
        this.mega = true;
    },

    hoverMega() {
        if (this.canHover()) this.openMega();
    },

    // Jeda singkat agar kursor bisa berpindah dari tombol ke panel tanpa menutupnya.
    leaveMega() {
        if (!this.canHover()) return;
        clearTimeout(this.closeTimer);
        this.closeTimer = setTimeout(() => (this.mega = false), 150);
    },

    toggleMega() {
        clearTimeout(this.closeTimer);
        this.mega = !this.mega;
    },

    closeAll() {
        clearTimeout(this.closeTimer);
        this.mega = false;
        this.menu = false;
    },

    onResize() {
        if (window.innerWidth >= 1024) {
            this.menu = false;
        } else {
            this.mega = false;
        }
    },
});
