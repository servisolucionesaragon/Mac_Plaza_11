<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Color/Almacenamiento/RAM dejan de ser atributos fijos del producto — ya
 * fueron backfilleados a `lote_variantes` (migración 2026_07_16_000003).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['almacenamiento_id']);
            $table->dropForeign(['ram_id']);
            $table->dropColumn(['color', 'almacenamiento_id', 'ram_id']);
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('color')->nullable()->after('modelo');
            $table->foreignId('almacenamiento_id')->nullable()->after('color')->constrained('almacenamientos')->onDelete('restrict');
            $table->foreignId('ram_id')->nullable()->after('almacenamiento_id')->constrained('rams')->onDelete('restrict');
        });

        // Reconstruye desde la variante más reciente de cada producto (mejor esfuerzo, no exacto si hubo múltiples variantes).
        $variantes = DB::table('lote_variantes')
            ->join('lotes_producto', 'lotes_producto.id', '=', 'lote_variantes.lote_id')
            ->leftJoin('colores', 'colores.id', '=', 'lote_variantes.color_id')
            ->select('lotes_producto.producto_id', 'colores.nombre as color_nombre', 'lote_variantes.almacenamiento_id', 'lote_variantes.ram_id')
            ->orderByDesc('lote_variantes.id')
            ->get()
            ->unique('producto_id');

        foreach ($variantes as $v) {
            DB::table('productos')->where('id', $v->producto_id)->update([
                'color'             => $v->color_nombre,
                'almacenamiento_id' => $v->almacenamiento_id,
                'ram_id'            => $v->ram_id,
            ]);
        }
    }
};
