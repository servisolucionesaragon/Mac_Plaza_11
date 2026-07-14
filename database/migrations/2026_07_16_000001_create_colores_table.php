<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('colores')->insert([
            ['nombre' => 'Negro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Blanco', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Azul', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Rojo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Verde', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Gris', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Dorado', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('colores');
    }
};
