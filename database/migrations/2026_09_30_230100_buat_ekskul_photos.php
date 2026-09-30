<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Foto kegiatan tiap ekstrakurikuler. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekskul_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekskul_id')->constrained('ekskuls')->cascadeOnDelete();
            $table->string('berkas');
            $table->string('judul')->nullable();
            $table->integer('urut')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekskul_photos');
    }
};
