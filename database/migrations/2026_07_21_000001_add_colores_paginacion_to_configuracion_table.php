<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('color_paginacion_texto', 7)->default('#7c3aed');
            $table->string('color_paginacion_activo_fondo', 7)->default('#7c3aed');
            $table->string('color_paginacion_activo_texto', 7)->default('#ffffff');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn([
                'color_paginacion_texto',
                'color_paginacion_activo_fondo',
                'color_paginacion_activo_texto',
            ]);
        });
    }
};
