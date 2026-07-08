<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('restrict');
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('dispositivo');       // Nombre del equipo
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('imei')->nullable();
            $table->string('color')->nullable();
            $table->text('falla_reportada');     // Lo que reporta el cliente
            $table->text('diagnostico')->nullable(); // Lo que encontró el técnico
            $table->text('solucion')->nullable();
            $table->decimal('presupuesto', 10, 2)->nullable()->default(null);
            $table->decimal('costo_final', 10, 2)->nullable()->default(null);
            $table->enum('estado', [
                'recibido',
                'en_diagnostico',
                'esperando_repuesto',
                'en_reparacion',
                'listo',
                'entregado',
                'no_reparable'
            ])->default('recibido');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->dateTime('fecha_recepcion');
            $table->dateTime('fecha_estimada')->nullable();
            $table->dateTime('fecha_entrega')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('garantia')->default(false);
            $table->integer('dias_garantia')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparaciones');
    }
};
