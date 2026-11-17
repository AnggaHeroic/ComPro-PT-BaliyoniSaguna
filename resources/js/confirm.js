/**
 * Dialog konfirmasi global (komponen x-ui.confirm-dialog).
 *
 * Dipakai dua cara:
 *  - Form dengan atribut data-confirm-title meminta konfirmasi dulu sebelum dikirim
 *    (juga data-confirm-message, data-confirm-label, data-confirm-tone="danger|default"; danger = merah untuk aksi permanen seperti hapus, default = hitam untuk peringatan biasa).
 *  - Aksi besar bisa meminta persetujuan dua kali: tambahkan data-confirm-second-title (juga -message, -label, -tone).
 *  - Kode JS lain: const jawaban = await Alpine.store('confirm').ask({ title, message, label, secondaryLabel, tone, hideCancel }).
 *    Jawaban: true (tombol utama), 'secondary' (tombol kedua), atau false (batal).
 */
export default function setupConfirm(Alpine) {
    const defaults = { title: '', message: '', label: 'Ya', secondaryLabel: '', tone: 'default', hideCancel: false };

    Alpine.store('confirm', {
        open: false,
        resolver: null,
        ...defaults,

        ask(options = {}) {
            Object.assign(this, defaults, options);
            this.open = true;

            return new Promise((resolve) => {
                this.resolver = resolve;
            });
        },

        answer(value) {
            this.open = false;
            this.resolver?.(value);
            this.resolver = null;
        },
    });

    document.addEventListener(
        'submit',
        async (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || !form.dataset.confirmTitle || form.dataset.confirmed === '1') {
                return;
            }

            event.preventDefault();

            const ok = await Alpine.store('confirm').ask({
                title: form.dataset.confirmTitle,
                message: form.dataset.confirmMessage ?? '',
                label: form.dataset.confirmLabel ?? 'Ya',
                tone: form.dataset.confirmTone ?? 'danger',
            });

            // Aksi besar meminta persetujuan kedua (data-confirm-second-title) sebelum dikirim.
            const sure = ok === true && form.dataset.confirmSecondTitle
                ? await Alpine.store('confirm').ask({
                    title: form.dataset.confirmSecondTitle,
                    message: form.dataset.confirmSecondMessage ?? '',
                    label: form.dataset.confirmSecondLabel ?? 'Ya',
                    tone: form.dataset.confirmSecondTone ?? form.dataset.confirmTone ?? 'danger',
                })
                : ok;

            if (sure === true) {
                form.dataset.confirmed = '1';
                form.requestSubmit(event.submitter ?? undefined);
            }
        },
        true,
    );
}
