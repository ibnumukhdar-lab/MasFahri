<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_photos', function (Blueprint $table) {
            // Gambar yang DICENTANG admin → dipakai sebagai latar hero halaman utama.
            $table->boolean('layar_utama')->default(false);
            $table->index('layar_utama');
        });
    }

    public function down(): void
    {
        Schema::table('gallery_photos', function (Blueprint $table) {
            $table->dropIndex(['layar_utama']);
            $table->dropColumn('layar_utama');
        });
    }
};
