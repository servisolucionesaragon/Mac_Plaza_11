<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('metodos_pago')->insert([
            ['nombre' => 'Efectivo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tarjeta', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Transferencia', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
};
