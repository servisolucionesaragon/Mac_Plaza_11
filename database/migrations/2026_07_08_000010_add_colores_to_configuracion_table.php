<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('color_primario', 7)->default('#a855f7');
            $table->string('color_secundario', 7)->default('#ec4899');
            $table->string('color_acento', 7)->default('#06b6d4');
            $table->string('color_sidebar', 7)->default('#1a0a3e');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['color_primario', 'color_secundario', 'color_acento', 'color_sidebar']);
        });
    }
};
