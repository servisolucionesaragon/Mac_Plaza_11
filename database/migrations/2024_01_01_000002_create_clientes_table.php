<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->nullable()->unique();
            $table->string('telefono');
            $table->string('celular')->nullable();
            $table->string('dni')->nullable()->unique();
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('tipo', ['particular', 'empresa'])->default('particular');
            $table->string('empresa')->nullable();
            $table->string('ruc')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
