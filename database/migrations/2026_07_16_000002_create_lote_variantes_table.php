<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote_variantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes_producto')->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained('colores')->onDelete('restrict');
            $table->foreignId('almacenamiento_id')->nullable()->constrained('almacenamientos')->onDelete('restrict');
            $table->foreignId('ram_id')->nullable()->constrained('rams')->onDelete('restrict');
            $table->integer('cantidad_inicial');
            $table->integer('cantidad_restante');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_variantes');
    }
};
