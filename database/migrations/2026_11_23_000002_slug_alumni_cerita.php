<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumni_cerita', function (Blueprint $table) {
            if (! Schema::hasColumn('alumni_cerita', 'slug')) {
                $table->string('slug', 160)->nullable()->unique()->after('nama');
            }
            if (! Schema::hasColumn('alumni_cerita', 'dilihat')) {
                $table->unsignedInteger('dilihat')->default(0)->after('diverifikasi_pada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alumni_cerita', function (Blueprint $table) {
            $table->dropColumn(['slug', 'dilihat']);
        });
    }
};
