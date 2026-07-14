<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * El consumo FIFO ahora ocurre a nivel de `lote_variantes`, no de `lotes_producto`
 * completo. Cada lote existente hasta hoy tiene exactamente 1 `LoteVariante`
 * (creada por el backfill anterior), así que el repunte es 1 a 1 sin ambigüedad.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_venta_lote', function (Blueprint $table) {
            $table->foreignId('lote_variante_id')->nullable()->after('lote_id')
                ->constrained('lote_variantes')->onDelete('restrict');
        });

        $mapaLoteAVariante = DB::table('lote_variantes')->pluck('id', 'lote_id');

        foreach (DB::table('detalle_venta_lote')->select('id', 'lote_id')->get() as $fila) {
            DB::table('detalle_venta_lote')
                ->where('id', $fila->id)
                ->update(['lote_variante_id' => $mapaLoteAVariante[$fila->lote_id] ?? null]);
        }

        Schema::table('detalle_venta_lote', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropColumn('lote_id');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_venta_lote', function (Blueprint $table) {
            $table->foreignId('lote_id')->nullable()->after('detalle_venta_id')
                ->constrained('lotes_producto')->onDelete('restrict');
        });

        foreach (DB::table('detalle_venta_lote')->select('id', 'lote_variante_id')->get() as $fila) {
            $loteId = DB::table('lote_variantes')->where('id', $fila->lote_variante_id)->value('lote_id');
            DB::table('detalle_venta_lote')->where('id', $fila->id)->update(['lote_id' => $loteId]);
        }

        Schema::table('detalle_venta_lote', function (Blueprint $table) {
            $table->dropForeign(['lote_variante_id']);
            $table->dropColumn('lote_variante_id');
        });
    }
};
