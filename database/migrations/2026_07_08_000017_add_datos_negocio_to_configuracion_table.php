<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('departamento')->nullable()->after('direccion');
            $table->string('ciudad')->nullable()->after('departamento');
            $table->string('pagina_web')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['departamento', 'ciudad', 'pagina_web']);
        });
    }
};
