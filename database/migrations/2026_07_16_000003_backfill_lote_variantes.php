<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfillea la tabla nueva `lote_variantes` a partir de los `lotes_producto`
 * ya existentes (creados en los últimos días bajo el esquema viejo, donde
 * color/almacenamiento/ram vivían en `productos`, no en el lote). También
 * agrega a `colores` cualquier valor de `productos.color` (texto libre) que
 * no coincida por nombre con ninguno de los colores ya sembrados.
 */
return new class extends Migration
{
    public function up(): void
    {
        $coloresExistentes = DB::table('colores')
            ->select('id', DB::raw('LOWER(nombre) as nombre_lower'))
            ->get()
            ->pluck('id', 'nombre_lower')
            ->all();

        $coloresLibres = DB::table('productos')
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->distinct()
            ->pluck('color');

        foreach ($coloresLibres as $nombreColor) {
            $clave = strtolower(trim($nombreColor));
            if (!isset($coloresExistentes[$clave])) {
                $id = DB::table('colores')->insertGetId([
                    'nombre'     => trim($nombreColor),
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $coloresExistentes[$clave] = $id;
            }
        }

        $lotes = DB::table('lotes_producto')
            ->join('productos', 'productos.id', '=', 'lotes_producto.producto_id')
            ->select(
                'lotes_producto.id as lote_id',
                'lotes_producto.cantidad_inicial',
                'lotes_producto.cantidad_restante',
                'productos.color as color_texto',
                'productos.almacenamiento_id',
                'productos.ram_id'
            )
            ->get();

        foreach ($lotes as $lote) {
            $colorId = null;
            if (!empty($lote->color_texto)) {
                $colorId = $coloresExistentes[strtolower(trim($lote->color_texto))] ?? null;
            }

            DB::table('lote_variantes')->insert([
                'lote_id'           => $lote->lote_id,
                'color_id'          => $colorId,
                'almacenamiento_id' => $lote->almacenamiento_id,
                'ram_id'            => $lote->ram_id,
                'cantidad_inicial'  => $lote->cantidad_inicial,
                'cantidad_restante' => $lote->cantidad_restante,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('lote_variantes')->truncate();
    }
};
