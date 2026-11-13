// Animasi muncul-saat-digulir: elemen bertanda [data-reveal] naik tipis sambil memudar masuk, sekali saja, saat terlihat di layar.
// [data-reveal-stagger] pada kontainer membuat anak-anaknya muncul bergantian. Gaya ada di resources/css/app.css (aktif hanya bila
// <html> berkelas reveal-ready dan pengguna tidak memilih "kurangi gerakan"). Tanpa JS atau IntersectionObserver, semua konten tetap tampil.
const STAGGER_STEP = 55;
const STAGGER_MAX = 20;

export default () => {
    clearTimeout(window.__revealTimer);

    const root = document.documentElement;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
        root.classList.remove('reveal-ready');

        return;
    }

    document.querySelectorAll('[data-reveal-stagger]').forEach((container) => {
        [...container.children].forEach((child, index) => {
            child.setAttribute('data-reveal', '');
            child.style.setProperty('--reveal-delay', `${Math.min(index, STAGGER_MAX) * STAGGER_STEP}ms`);
        });
    });

    // Setelah animasi selesai, atribut dicabut agar gaya reveal (transition, transform) tidak mengganggu hover elemen itu.
    const finish = (el) => {
        el.removeAttribute('data-reveal');
        el.classList.remove('is-revealed');
        el.style.removeProperty('--reveal-delay');
    };

    const show = (el) => {
        el.classList.add('is-revealed');

        let done = false;
        const end = () => {
            if (done) return;
            done = true;
            finish(el);
        };

        el.addEventListener('transitionend', (event) => {
            if (event.target === el && event.propertyName === 'opacity') end();
        });
        setTimeout(end, 2000); // cadangan bila transitionend tidak datang
    };

    const observe = (selector, options, onVisible) => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                observer.unobserve(entry.target);
                onVisible(entry.target);
            });
        }, options);

        document.querySelectorAll(selector).forEach((el) => observer.observe(el));
    };

    // Elemen tunggal muncul saat sebagian terlihat; anak dalam kontainer stagger tidak diamati sendiri-sendiri.
    observe('[data-reveal]:not([data-reveal-stagger] > *)', { threshold: 0.12, rootMargin: '0px 0px -6% 0px' }, show);

    // Kontainer stagger: begitu bagian atasnya masuk layar, seluruh anaknya muncul berurutan tanpa menunggu gulir lebih jauh.
    observe('[data-reveal-stagger]', { threshold: 0, rootMargin: '0px 0px -8% 0px' }, (container) => {
        [...container.children].forEach(show);
    });
};
