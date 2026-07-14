<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Snapshot de qué variante (color/almacenamiento/ram) se vendió en cada línea
 * de venta, igual criterio que `imei_vendido`/`serial_vendido` — evita JOINs
 * para mostrar el recibo y protege el histórico si algún día se permite
 * editar una variante. Backfill best-effort desde el producto (los datos
 * históricos hasta hoy no distinguían variante por línea).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->after('producto_id')
                ->constrained('colores')->onDelete('restrict');
            $table->foreignId('almacenamiento_id')->nullable()->after('color_id')
                ->constrained('almacenamientos')->onDelete('restrict');
            $table->foreignId('ram_id')->nullable()->after('almacenamiento_id')
                ->constrained('rams')->onDelete('restrict');
        });

        $productos = DB::table('productos')->select('id', 'color', 'almacenamiento_id', 'ram_id')->get()->keyBy('id');
        $coloresPorNombre = DB::table('colores')
            ->select('id', DB::raw('LOWER(nombre) as nombre_lower'))
            ->get()->pluck('id', 'nombre_lower')->all();

        foreach (DB::table('detalle_ventas')->select('id', 'producto_id')->get() as $detalle) {
            $producto = $productos[$detalle->producto_id] ?? null;
            if (!$producto) {
                continue;
            }
            $colorId = $producto->color ? ($coloresPorNombre[strtolower(trim($producto->color))] ?? null) : null;

            DB::table('detalle_ventas')->where('id', $detalle->id)->update([
                'color_id'          => $colorId,
                'almacenamiento_id' => $producto->almacenamiento_id,
                'ram_id'            => $producto->ram_id,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropForeign(['almacenamiento_id']);
            $table->dropForeign(['ram_id']);
            $table->dropColumn(['color_id', 'almacenamiento_id', 'ram_id']);
        });
    }
};
