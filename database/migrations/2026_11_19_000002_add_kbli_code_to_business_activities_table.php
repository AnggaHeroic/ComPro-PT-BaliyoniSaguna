<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_activities', function (Blueprint $table) {
            // Selalu string (bukan integer): kode KBLI bisa diawali nol dan memuat huruf.
            $table->string('kbli_code', 10)->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('business_activities', function (Blueprint $table) {
            $table->dropColumn('kbli_code');
        });
    }
};
