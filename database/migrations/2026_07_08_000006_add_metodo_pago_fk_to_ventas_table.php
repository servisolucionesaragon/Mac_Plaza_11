<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('metodo_pago_id')->nullable()->after('metodo_pago')->constrained('metodos_pago')->onDelete('restrict');
        });

        // Backfill: migrar el enum viejo a la nueva FK (case-insensitive por si acaso)
        DB::table('ventas')->get()->each(function ($venta) {
            $metodoPagoId = DB::table('metodos_pago')->whereRaw('LOWER(nombre) = ?', [strtolower($venta->metodo_pago)])->value('id');

            // Si el método de pago viejo (yape/plin/cuotas) ya no existe en el catálogo nuevo,
            // no perder el dato: crear la fila en el catálogo automáticamente (inactiva, para no ofrecerla en nuevos formularios pero preservar el histórico).
            if (!$metodoPagoId) {
                $metodoPagoId = DB::table('metodos_pago')->insertGetId([
                    'nombre'     => ucfirst($venta->metodo_pago),
                    'activo'     => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('ventas')->where('id', $venta->id)->update(['metodo_pago_id' => $metodoPagoId]);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'cuotas', 'yape', 'plin'])->default('efectivo')->after('metodo_pago_id');
        });

        DB::table('ventas')->get()->each(function ($venta) {
            $nombre = $venta->metodo_pago_id ? strtolower(DB::table('metodos_pago')->where('id', $venta->metodo_pago_id)->value('nombre')) : 'efectivo';
            DB::table('ventas')->where('id', $venta->id)->update(['metodo_pago' => $nombre]);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['metodo_pago_id']);
            $table->dropColumn('metodo_pago_id');
        });
    }
};
