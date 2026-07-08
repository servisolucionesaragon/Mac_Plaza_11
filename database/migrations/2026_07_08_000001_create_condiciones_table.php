<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condiciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('condiciones')->insert([
            ['nombre' => 'Nuevo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Reacondicionado', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Usado', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('condiciones');
    }
};
