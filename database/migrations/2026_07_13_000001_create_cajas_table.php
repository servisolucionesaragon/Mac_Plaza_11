<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('monto_inicial', 10, 2)->default(0);
            $table->text('notas_apertura')->nullable();
            $table->foreignId('user_apertura_id')->constrained('users')->onDelete('restrict');
            $table->dateTime('fecha_apertura');
            $table->string('estado', 20)->default('abierta');
            $table->text('notas_cierre')->nullable();
            $table->foreignId('user_cierre_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->dateTime('fecha_cierre')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
