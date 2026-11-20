/**
 * Peringatan perubahan yang belum disimpan.
 *
 * Form dengan atribut data-unsaved-guard dipantau: isiannya dibandingkan dengan keadaan saat halaman
 * dimuat. Bila ada yang berubah dan admin hendak meninggalkan halaman:
 *  - klik tautan di dalam situs memunculkan dialog "Perubahan belum disimpan", dan
 *  - menutup tab, memuat ulang, atau tombol kembali browser memunculkan peringatan bawaan browser.
 * Menyimpan form itu sendiri tidak diperingatkan.
 *
 * Atribut tambahan:
 *   data-unsaved-dirty   form dianggap sudah berubah sejak awal (mis. dimuat ulang setelah galat validasi)
 *   data-unsaved-skip    pada tautan: tidak diperingatkan (mis. tombol Batal yang memang membuang perubahan)
 */
// 'bagian' = penanda bagian yang sedang dibuka di form isi halaman (bukan isian).
const IGNORED_NAMES = ['_token', '_method', 'bagian'];

export default function setupUnsavedGuard(Alpine) {
    const forms = () => [...document.querySelectorAll('form[data-unsaved-guard]')];
    const baselines = new WeakMap();
    let allowLeave = false;

    /** Ringkasan isian form sebagai satu teks; dua keadaan form sama bila teksnya sama. */
    const signature = (form) =>
        [...form.elements]
            .filter((el) => el.name && !el.disabled && !IGNORED_NAMES.includes(el.name))
            .filter((el) => !((el.type === 'checkbox' || el.type === 'radio') && !el.checked))
            .map((el) =>
                el.type === 'file'
                    ? `${el.name}:file:${[...el.files].map((file) => `${file.name}:${file.size}`).join('|')}`
                    : `${el.name}=${el.value}`,
            )
            .join('\u0001');

    const isDirty = () => forms().some((form) => baselines.has(form) && signature(form) !== baselines.get(form));

    const takeBaselines = () => {
        forms().forEach((form) => {
            // Form yang dimuat ulang setelah galat validasi sudah berisi ketikan yang belum tersimpan.
            baselines.set(form, 'unsavedDirty' in form.dataset ? '\u0000belum-tersimpan' : signature(form));
        });
    };

    window.addEventListener('load', () => setTimeout(takeBaselines, 150));

    // Pendengar ini dipasang setelah dialog konfirmasi (confirm.js), jadi hanya menerima pengiriman yang dilanjutkan.
    document.addEventListener('submit', (event) => {
        if (!event.defaultPrevented && event.target instanceof HTMLFormElement && event.target.hasAttribute('data-unsaved-guard')) {
            allowLeave = true;
        }
    });

    // Keluar otomatis karena tidak aktif atau pengalihan sah lain: tidak perlu diperingatkan.
    window.addEventListener('admin:allow-leave', () => {
        allowLeave = true;
    });

    window.addEventListener('beforeunload', (event) => {
        if (allowLeave || !isDirty()) return;

        event.preventDefault();
        event.returnValue = '';
    });

    document.addEventListener(
        'click',
        async (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            const link = event.target instanceof Element ? event.target.closest('a[href]') : null;

            if (!link || link.target === '_blank' || link.hasAttribute('download') || 'unsavedSkip' in link.dataset) return;

            const url = new URL(link.href, window.location.href);

            if (!['http:', 'https:'].includes(url.protocol)) return;

            // Tautan jangkar di halaman yang sama tidak meninggalkan halaman.
            if (url.origin === window.location.origin && url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
                return;
            }

            if (allowLeave || !isDirty()) return;

            event.preventDefault();
            event.stopPropagation();

            const choice = await Alpine.store('confirm').ask({
                title: 'Perubahan belum disimpan',
                message: 'Anda memiliki perubahan yang belum disimpan. Jika keluar sekarang, perubahan itu akan hilang.',
                label: 'Tetap di halaman',
                secondaryLabel: 'Keluar tanpa menyimpan',
                hideCancel: true,
            });

            if (choice === 'secondary') {
                allowLeave = true;
                window.location.href = link.href;
            }
        },
        true,
    );
}
