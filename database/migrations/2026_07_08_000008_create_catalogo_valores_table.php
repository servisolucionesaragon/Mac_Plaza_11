<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_tipo_id')->constrained('catalogo_tipos')->onDelete('cascade');
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['catalogo_tipo_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_valores');
    }
};
