<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_catalogo_valor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('catalogo_valor_id')->constrained('catalogo_valores')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['producto_id', 'catalogo_valor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_catalogo_valor');
    }
};
