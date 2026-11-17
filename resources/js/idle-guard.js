/**
 * Penjaga ketidakaktifan admin. Aktivitas (gerak mouse, ketik, gulir, sentuh, klik) dicatat di localStorage agar semua tab
 * dashboard berbagi satu pewaktu. Selama admin aktif, server dikabari berkala (agar sesi tidak dianggap diam walau form
 * dibiarkan terbuka lama). Mendekati batas muncul peringatan dengan hitung mundur; aktivitas apa pun diabaikan sampai admin
 * menekan "Tetap masuk", dan peringatan tetap ada (dengan hitungan yang sama) saat pindah halaman atau memuat ulang.
 * Melewati batas, admin dikeluarkan lewat form biasa ke server (yang mencatatnya di riwayat aktivitas) dan diarahkan ke halaman login.
 *
 * Kabar ke server memakai navigator.sendBeacon (kirim-dan-lupa, hanya denyut sesi, bukan pengambilan data), dan keluar otomatis
 * memakai pengiriman form, jadi tidak ada fetch atau axios.
 */
export default ({ timeout, warning, pingUrl, warningUrl }) => ({
    remaining: null, // detik tersisa saat peringatan tampil; null = disembunyikan
    storageKey: 'admin-last-activity',
    warningKey: 'admin-idle-warning', // penanda bersama antar tab: peringatan sedang berjalan
    memory: Date.now(),
    lastPing: Date.now(),
    leaving: false,

    init() {
        // Memuat halaman dihitung sebagai aktivitas, kecuali peringatan sedang berjalan: berpindah halaman
        // atau memuat ulang tidak boleh mengulang hitungan mundur.
        const warningFrom = (timeout - warning) * 60 * 1000;

        if (Date.now() - this.lastActivity() < warningFrom) {
            this.touch(true);
        }

        ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach((name) => {
            window.addEventListener(name, () => this.touch(), { passive: true, capture: true });
        });

        window.addEventListener('storage', (event) => {
            if (event.key === this.storageKey) this.tick();
        });

        this.tick();
        setInterval(() => this.tick(), 1000);
    },

    get clock() {
        const seconds = Math.max(0, this.remaining ?? 0);

        return String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0');
    },

    lastActivity() {
        try {
            return Number(window.localStorage.getItem(this.storageKey)) || this.memory;
        } catch {
            return this.memory;
        }
    },

    flagged() {
        try {
            return window.localStorage.getItem(this.warningKey) === '1';
        } catch {
            return false;
        }
    },

    setFlag(on) {
        try {
            on ? window.localStorage.setItem(this.warningKey, '1') : window.localStorage.removeItem(this.warningKey);
        } catch {
            // penyimpanan tidak tersedia
        }
    },

    /**
     * Catat aktivitas (maksimal sekali per detik); sesekali kabari server selagi admin aktif. Saat peringatan tampil,
     * aktivitas biasa tidak dihitung: sesi hanya berlanjut bila admin menekan "Tetap masuk" (stay()).
     */
    touch(force = false) {
        if (this.remaining !== null && !force) return;

        const now = Date.now();

        if (!force && now - this.memory < 1000) return;

        this.memory = now;
        this.remaining = null;

        try {
            window.localStorage.setItem(this.storageKey, String(now));
        } catch {
            // penyimpanan tidak tersedia: pewaktu tetap jalan per tab
        }

        // Peringatan sebelumnya (mungkin di tab/halaman lain) selesai: server juga dilanjutkan.
        if (this.flagged()) {
            this.setFlag(false);
            this.ping();
        } else if (now - this.lastPing > 4 * 60 * 1000) {
            this.ping();
        }
    },

    tick() {
        if (this.leaving) return;

        const limit = timeout * 60 * 1000;
        const idle = Date.now() - this.lastActivity();

        if (idle >= limit) {
            this.expire();

            return;
        }

        const left = limit - idle;
        const inWarning = left <= warning * 60 * 1000;

        // Peringatan baru dimulai: beri tahu server agar jamnya selaras dan tidak disegarkan oleh pindah halaman.
        if (inWarning && this.remaining === null) {
            this.markWarning(Math.round(idle / 1000));
        }

        this.remaining = inWarning ? Math.ceil(left / 1000) : null;
    },

    /** Kirim denyut ke server tanpa menunggu jawaban; token CSRF ikut sebagai kolom form. */
    beacon(url, fields = {}) {
        const data = new FormData();

        data.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
        Object.entries(fields).forEach(([name, value]) => data.append(name, String(value)));

        try {
            navigator.sendBeacon?.(url, data);
        } catch {
            // jaringan terputus: server memakai waktu aktivitas terakhir yang diketahuinya
        }
    },

    markWarning(idleSeconds) {
        this.setFlag(true);
        this.beacon(warningUrl, { idle: idleSeconds });
    },

    ping() {
        this.lastPing = Date.now();
        this.beacon(pingUrl);
    },

    /** Tombol "Tetap masuk" pada peringatan. */
    stay() {
        this.setFlag(false);
        this.touch(true);
        this.ping();
    },

    expire() {
        this.leaving = true;
        this.setFlag(false);

        // Keluar yang sah: penjaga perubahan yang belum disimpan tidak perlu memperingatkan.
        window.dispatchEvent(new Event('admin:allow-leave'));
        this.$refs.logoutForm.submit();
    },
});
