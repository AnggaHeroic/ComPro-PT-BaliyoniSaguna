<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris per halaman publik. Hanya isi yang berbeda dari teks bawaan (config/page_contents.php) yang disimpan.
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            // Isi yang diubah admin: kunci kolom => nilai (teks, daftar kartu, atau path gambar).
            $table->json('content')->nullable();
            // Setelan default kustom: acuan "Reset default" yang ditetapkan admin (bentuk sama dengan content).
            $table->json('defaults')->nullable();
            $table->timestamp('defaults_set_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
