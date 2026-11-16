<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('display_slides', function (Blueprint $table) {
            $table->id();
            $table->string('berkas');                 // path relatif disk media
            $table->string('judul', 255)->nullable(); // keterangan internal admin
            $table->unsignedInteger('durasi')->default(10); // detik per slide
            $table->unsignedInteger('urut')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('display_slides');
    }
};
