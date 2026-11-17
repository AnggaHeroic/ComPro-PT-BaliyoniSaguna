{{-- Props: title (judul bagian), description (penjelasan), action (URL DELETE), confirmTitle, confirmMessage, label (teks tombol). Bagian "Hapus" di bawah form ubah; konfirmasi lewat dialog global x-ui.confirm-dialog. --}}
@props(['title', 'description', 'action', 'confirmTitle' => 'Hapus data?', 'confirmMessage' => 'Data akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.', 'label' => 'Hapus'])
<section class="flex max-w-3xl flex-wrap items-center justify-between gap-4 border border-brand/25 bg-white p-6 sm:px-8">
    <div class="min-w-0 flex-1">
        <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-brand">{{ $title }}</h2>
        <p class="mt-1 text-sm leading-relaxed text-muted">{{ $description }}</p>
    </div>
    <x-ui.confirm-delete :action="$action" :title="$confirmTitle" :message="$confirmMessage" class="!min-h-11 !px-5">
        <x-ui.icon name="trash" class="size-4" />
        {{ $label }}
    </x-ui.confirm-delete>
</section>
