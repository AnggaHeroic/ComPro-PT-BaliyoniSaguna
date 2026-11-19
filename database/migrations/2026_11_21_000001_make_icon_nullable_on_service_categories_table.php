<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            // Kategori yang memakai ikon kustom unggahan tidak perlu ikon dari daftar.
            $table->string('icon', 40)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->string('icon', 40)->nullable(false)->change();
        });
    }
};
