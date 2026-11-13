// Formulir kontak tanpa backend: pesan disusun lalu dibuka lewat WhatsApp perusahaan (whatsappUrl = tautan wa.me)
// atau lewat aplikasi email pengunjung (mailto ke email perusahaan). channel = 'whatsapp' (default) atau 'email'.
export default (subject, whatsappUrl, companyEmail) => ({
    name: '',
    email: '',
    phone: '',
    subject,
    message: '',

    // Isi pesan sama untuk kedua saluran; email dan telepon pengirim dicantumkan bila diisi agar tim bisa membalas.
    compose() {
        const lines = [`Halo, saya ${this.name}.`];

        if (this.subject) lines.push(`Perihal: ${this.subject}`);

        lines.push('', this.message);

        const contacts = [];

        if (this.email) contacts.push(`Email: ${this.email}`);
        if (this.phone) contacts.push(`Telepon: ${this.phone}`);
        if (contacts.length) lines.push('', ...contacts);

        return lines.join('\n');
    },

    send(channel = 'whatsapp') {
        const body = encodeURIComponent(this.compose());

        if (channel === 'email' && companyEmail) {
            const title = encodeURIComponent(this.subject || `Pesan dari ${this.name}`);

            window.location.href = `mailto:${companyEmail}?subject=${title}&body=${body}`;

            return;
        }

        window.open(`${whatsappUrl}?text=${body}`, '_blank', 'noopener');
    },
});
