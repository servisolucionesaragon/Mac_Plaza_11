<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('color_menu_texto', 7)->default('#c9c3d9');
            $table->string('color_menu_activo', 7)->default('#a855f7');
            $table->string('color_boton_texto', 7)->default('#ffffff');
            $table->string('color_boton_fondo', 7)->default('#a855f7');
            $table->string('color_grafico_1', 7)->default('#f97d07');
            $table->string('color_grafico_2', 7)->default('#00b5c8');
            $table->string('color_grafico_3', 7)->default('#fecf1c');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn([
                'color_menu_texto', 'color_menu_activo',
                'color_boton_texto', 'color_boton_fondo',
                'color_grafico_1', 'color_grafico_2', 'color_grafico_3',
            ]);
        });
    }
};
