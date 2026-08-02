<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denominaciones', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 12, 2);
            $table->enum('tipo', ['billete', 'moneda']);
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denominaciones');
    }
};
