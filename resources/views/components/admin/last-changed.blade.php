{{-- Props: log (ActivityLog terakhir untuk objek ini, atau null). Menampilkan "Terakhir diubah ... oleh ...". --}}
@props(['log' => null])
@if ($log)
    <p class="flex items-center gap-2 text-xs text-muted">
        <x-ui.icon name="history" class="size-4" />
        Terakhir diubah {{ \App\Helpers\DateHelper::local($log->created_at)->translatedFormat('j F Y, H:i') }} oleh {{ $log->user?->name ?? 'Sistem' }}
    </p>
@endif
