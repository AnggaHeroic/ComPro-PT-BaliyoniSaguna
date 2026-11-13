{{-- Props: company (memakai mobile_url (wa.me), mobile, dan email), subject (perihal awal, opsional). Semua kolom wajib; email dan telepon cukup salah satu (atribut required dinamis lewat Alpine). Formulir tanpa backend: pengunjung memilih kirim lewat WhatsApp perusahaan atau lewat email (membuka aplikasi email dengan pesan terisi). Perilaku ada di resources/js/contact-form.js. --}}
@props(['company', 'subject' => ''])
@php($field = 'mt-2 block w-full border border-black-soft/20 bg-white px-4 py-2.5 text-sm text-black-soft placeholder:text-muted/70 focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand')
<form x-data="contactForm(@js($subject), @js($company['mobile_url']), @js($company['email']))" @submit.prevent="send($event.submitter?.value)"
      class="border border-t-4 border-black-soft/10 border-t-brand bg-white p-6 sm:p-8">
    <h2 class="sr-only">Formulir kirim pesan</h2>

    <div class="space-y-4">
        <div>
            <label for="kontak-nama" class="text-sm font-semibold text-black-soft">Nama</label>
            <input id="kontak-nama" type="text" x-model="name" required autocomplete="name" placeholder="Nama lengkap Anda" class="{{ $field }}">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="kontak-email" class="text-sm font-semibold text-black-soft">Email</label>
                <input id="kontak-email" type="email" x-model="email" :required="!phone" autocomplete="email" placeholder="nama@perusahaan.com" class="{{ $field }}">
            </div>
            <div>
                <label for="kontak-telepon" class="text-sm font-semibold text-black-soft">No. Telepon</label>
                <input id="kontak-telepon" type="tel" x-model="phone" :required="!email" autocomplete="tel" inputmode="tel" maxlength="20" placeholder="08xx xxxx xxxx" class="{{ $field }}">
            </div>
        </div>
        <p class="-mt-1 text-xs text-muted">Isi salah satu: email atau nomor telepon agar kami bisa menghubungi Anda kembali.</p>
        <div>
            <label for="kontak-perihal" class="text-sm font-semibold text-black-soft">Perihal</label>
            <input id="kontak-perihal" type="text" x-model="subject" required maxlength="100" placeholder="Mis. penawaran AC split wall" class="{{ $field }}">
        </div>
        <div>
            <label for="kontak-pesan" class="text-sm font-semibold text-black-soft">Pesan</label>
            <textarea id="kontak-pesan" x-model="message" required rows="4" placeholder="Tuliskan kebutuhan atau pertanyaan Anda" class="{{ $field }} resize-y"></textarea>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        <x-ui.button type="submit" value="whatsapp" class="sm:flex-1">
            <x-ui.icon name="whatsapp" class="size-4" />
            Kirim lewat WhatsApp
        </x-ui.button>
        @if ($company['email'])
            <x-ui.button type="submit" value="email" variant="outline" class="sm:flex-1">
                <x-ui.icon name="mail" class="size-4" />
                Kirim lewat Email
            </x-ui.button>
        @endif
    </div>
    <p class="mt-4 text-xs leading-5 text-muted">Pesan baru terkirim setelah Anda menekan kirim di WhatsApp atau aplikasi email Anda.</p>
</form>
