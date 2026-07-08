<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('condicion_id')->nullable()->after('condicion')->constrained('condiciones')->onDelete('restrict');
            $table->foreignId('almacenamiento_id')->nullable()->after('almacenamiento')->constrained('almacenamientos')->onDelete('restrict');
            $table->foreignId('ram_id')->nullable()->after('ram')->constrained('rams')->onDelete('restrict');
        });

        // Backfill: migrar los valores string/enum existentes a las nuevas FK
        DB::table('productos')->get()->each(function ($producto) {
            $condicionId = DB::table('condiciones')->whereRaw('LOWER(nombre) = ?', [strtolower($producto->condicion)])->value('id');
            $almacenamientoId = $producto->almacenamiento
                ? DB::table('almacenamientos')->where('nombre', $producto->almacenamiento)->value('id')
                : null;
            $ramId = $producto->ram
                ? DB::table('rams')->where('nombre', $producto->ram)->value('id')
                : null;

            DB::table('productos')->where('id', $producto->id)->update([
                'condicion_id'      => $condicionId,
                'almacenamiento_id' => $almacenamientoId,
                'ram_id'            => $ramId,
            ]);
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['condicion', 'almacenamiento', 'ram']);
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->enum('condicion', ['nuevo', 'reacondicionado', 'usado'])->default('nuevo')->after('condicion_id');
            $table->string('almacenamiento')->nullable()->after('almacenamiento_id');
            $table->string('ram')->nullable()->after('ram_id');
        });

        DB::table('productos')->get()->each(function ($producto) {
            $condicionNombre = $producto->condicion_id ? strtolower(DB::table('condiciones')->where('id', $producto->condicion_id)->value('nombre')) : 'nuevo';
            $almacenamientoNombre = $producto->almacenamiento_id ? DB::table('almacenamientos')->where('id', $producto->almacenamiento_id)->value('nombre') : null;
            $ramNombre = $producto->ram_id ? DB::table('rams')->where('id', $producto->ram_id)->value('nombre') : null;

            DB::table('productos')->where('id', $producto->id)->update([
                'condicion'      => $condicionNombre,
                'almacenamiento' => $almacenamientoNombre,
                'ram'            => $ramNombre,
            ]);
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['condicion_id']);
            $table->dropForeign(['almacenamiento_id']);
            $table->dropForeign(['ram_id']);
            $table->dropColumn(['condicion_id', 'almacenamiento_id', 'ram_id']);
        });
    }
};
