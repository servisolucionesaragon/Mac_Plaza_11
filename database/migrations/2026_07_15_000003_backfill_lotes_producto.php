<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $userId = DB::table('users')->where('rol', 'admin')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        if (!$userId) {
            return;
        }

        $productos = DB::table('productos')->where('stock', '>', 0)->get(['id', 'stock', 'precio_compra', 'created_at']);

        foreach ($productos as $producto) {
            DB::table('lotes_producto')->insert([
                'producto_id'       => $producto->id,
                'cantidad_inicial'  => $producto->stock,
                'cantidad_restante' => $producto->stock,
                'costo_unitario'    => $producto->precio_compra,
                'proveedor'         => null,
                'fecha_ingreso'     => $producto->created_at ?? now(),
                'notas'             => 'Lote generado automáticamente al migrar a control de inventario por lotes.',
                'user_id'           => $userId,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('lotes_producto')
            ->where('notas', 'Lote generado automáticamente al migrar a control de inventario por lotes.')
            ->delete();
    }
};
