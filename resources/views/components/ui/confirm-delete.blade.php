{{-- Props: action (URL DELETE, wajib), title (judul dialog), message (isi dialog). Slot = teks tombol (default "Hapus"). Konfirmasi lewat dialog global x-ui.confirm-dialog. --}}
@props(['action', 'title' => 'Hapus data?', 'message' => 'Data akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'])
<form method="POST" action="{{ $action }}" data-confirm-title="{{ $title }}" data-confirm-message="{{ $message }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->class('inline-flex min-h-9 items-center justify-center border border-brand/30 bg-white px-3 text-xs font-semibold uppercase tracking-wider text-brand transition-colors hover:bg-brand hover:text-white') }}>
        {{ $slot->isEmpty() ? 'Hapus' : $slot }}
    </button>
</form>
