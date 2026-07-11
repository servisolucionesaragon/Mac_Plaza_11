<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('color_login_fondo', 7)->default('#1a0a3e');
            $table->string('color_login_tarjeta', 7)->default('#a855f7');
            $table->string('color_login_texto_modulos', 7)->default('#ffffff');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn([
                'color_login_fondo', 'color_login_tarjeta', 'color_login_texto_modulos',
            ]);
        });
    }
};
