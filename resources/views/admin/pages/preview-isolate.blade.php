{{-- Disisipkan ke halaman publik hanya saat pratinjau bagian di admin: menyisakan satu bagian dan menonaktifkan interaksi. Variabel: $section (kunci bagian). --}}
<style>
    body { pointer-events: none !important; }
    html { scroll-behavior: auto !important; }
</style>
<script>
    (function () {
        // Animasi muncul-saat-digulir dimatikan agar semua isi langsung terlihat di pratinjau.
        document.documentElement.classList.remove('reveal-ready');

        var targets = Array.prototype.slice.call(document.querySelectorAll('[data-cms-section="' + @json($section) + '"]'));

        if (!targets.length) {
            document.body.innerHTML = '<p style="padding:48px;font:16px system-ui,sans-serif;color:#64748b">Bagian ini tidak tampil di halaman.</p>';

            return;
        }

        // Pertahankan target beserta seluruh induknya; sembunyikan saudara-saudara lainnya di tiap tingkat.
        var keep = new Set();

        targets.forEach(function (target) {
            for (var node = target; node && node !== document.documentElement; node = node.parentElement) {
                keep.add(node);
            }
        });

        keep.forEach(function (node) {
            if (targets.indexOf(node) !== -1) return;

            Array.prototype.forEach.call(node.children, function (child) {
                if (!keep.has(child) && !/^(SCRIPT|STYLE|LINK|TEMPLATE)$/.test(child.tagName)) {
                    child.style.display = 'none';
                }
            });
        });
    })();
</script>
