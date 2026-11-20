<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug', 160);
            $table->string('summary', 500)->nullable();
            // Paragraf dipisahkan baris kosong; kosong = halaman publik memakai teks contoh.
            $table->text('detail')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            // Alamat hanya perlu unik di dalam satu kategori.
            $table->unique(['service_category_id', 'slug']);
            $table->index(['service_category_id', 'is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_activities');
    }
};
