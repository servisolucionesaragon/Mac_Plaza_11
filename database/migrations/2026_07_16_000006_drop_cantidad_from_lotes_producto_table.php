<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `lotes_producto` pasa a ser solo la cabecera de compra (costo/proveedor/
 * fecha); la cantidad ahora vive por variante en `lote_variantes`
 * (ya backfilleada en la migración anterior).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes_producto', function (Blueprint $table) {
            $table->dropColumn(['cantidad_inicial', 'cantidad_restante']);
        });
    }

    public function down(): void
    {
        Schema::table('lotes_producto', function (Blueprint $table) {
            $table->integer('cantidad_inicial')->default(0)->after('producto_id');
            $table->integer('cantidad_restante')->default(0)->after('cantidad_inicial');
        });
    }
};
