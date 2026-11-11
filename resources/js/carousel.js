// Carousel berulang: bergeser pelan ke kiri, berhenti saat kursor di atas kartu atau saat pengguna berinteraksi.
// Dipakai lewat x-data="carousel(kecepatan)" pada components/web/service-carousel.blade.php.
// Status disimpan di closure (bukan properti data) agar tidak ikut dipantau reaktivitas Alpine.
const RESUME_DELAY = 2500;

export default (speed = 35) => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    let track;
    let originals = [];
    let extra = 1;
    let pos = 0;
    let lastInteraction = -Infinity;
    let hovering = false;
    let focused = false;
    let visible = true;
    let wheelRemaining = 0;
    let frame;
    let observer;

    const touch = () => {
        lastInteraction = performance.now();
    };

    const step = () => {
        const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
        return originals[0].getBoundingClientRect().width + gap;
    };

    const setWidth = () => originals.length * step();

    const copy = () =>
        originals.map((item) => {
            const clone = item.cloneNode(true);
            clone.removeAttribute('data-carousel-item');
            clone.setAttribute('data-clone', '');
            clone.setAttribute('aria-hidden', 'true');
            clone.inert = true;
            return clone;
        });

    // Menjaga posisi tetap di antara salinan kiri dan kanan agar putaran tidak putus.
    const wrap = () => {
        const w = setWidth();
        if (track.scrollLeft >= (extra + 1) * w) track.scrollLeft -= w;
        else if (track.scrollLeft < w) track.scrollLeft += w;
        pos = track.scrollLeft;
    };

    const build = () => {
        track.querySelectorAll('[data-clone]').forEach((el) => el.remove());
        extra = Math.ceil(track.clientWidth / setWidth()) + 1;
        for (let i = 0; i < extra; i++) {
            track.prepend(...copy());
            track.append(...copy());
        }
        track.scrollLeft = pos = extra * setWidth();
    };

    const tick = (() => {
        let last = performance.now();

        const run = (now) => {
            const dt = Math.min(now - last, 100);
            last = now;

            if (wheelRemaining) {
                // Mendekati target secara eksponensial agar terasa mengalir, bukan loncat per langkah roda.
                let move = wheelRemaining * (1 - Math.exp(-dt / 110));
                if (Math.abs(wheelRemaining) < 0.5) move = wheelRemaining;
                wheelRemaining -= move;
                pos += move;
                track.scrollLeft = pos;
            } else if (hovering || focused || !visible || document.hidden || now - lastInteraction < RESUME_DELAY) {
                pos = track.scrollLeft;
            } else if (speed) {
                pos += (speed * dt) / 1000;
                track.scrollLeft = pos;
            }

            frame = requestAnimationFrame(run);
        };

        return run;
    })();

    return {
        init() {
            track = this.$refs.track;
            originals = [...track.querySelectorAll('[data-carousel-item]')];
            if (originals.length === 0) return;

            build();

            observer = new IntersectionObserver(([entry]) => {
                visible = entry.isIntersecting;
            });
            observer.observe(this.$el);

            if (!reduceMotion.matches) frame = requestAnimationFrame(tick);
        },

        destroy() {
            cancelAnimationFrame(frame);
            observer?.disconnect();
        },

        rebuild() {
            if (originals.length) build();
        },

        go(dir) {
            touch();
            track.scrollBy({ left: dir * step(), behavior: reduceMotion.matches ? 'auto' : 'smooth' });
        },

        onScroll() {
            if (Math.abs(track.scrollLeft - pos) > 2) pos = track.scrollLeft; // gulir manual
            wrap();
        },

        // Scroll mouse (vertikal) di atas carousel menggeser kartu ke kanan/kiri.
        onWheel(e) {
            if (e.ctrlKey) return; // biarkan zoom
            const delta = Math.abs(e.deltaY) >= Math.abs(e.deltaX) ? e.deltaY : e.deltaX;
            if (!delta) return;

            e.preventDefault();
            touch();

            const unit = e.deltaMode === 1 ? 16 : e.deltaMode === 2 ? track.clientWidth : 1;
            if (reduceMotion.matches) {
                track.scrollLeft += delta * unit;
                return;
            }

            const limit = track.clientWidth * 2;
            wheelRemaining = Math.max(-limit, Math.min(limit, wheelRemaining + delta * unit));
        },

        touch,

        onEnter(e) {
            if (e.pointerType === 'mouse') hovering = true;
        },

        onLeave() {
            hovering = false;
        },

        onFocusIn(e) {
            focused = e.target.matches(':focus-visible');
        },

        onFocusOut() {
            focused = false;
        },
    };
};
