<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            // Ikon kustom hasil unggahan admin; bila ada, dipakai menggantikan ikon bawaan (kolom icon).
            $table->string('icon_image')->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn('icon_image');
        });
    }
};
