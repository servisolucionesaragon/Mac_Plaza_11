<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('restrict');
            $table->string('modelo')->nullable();
            $table->string('color')->nullable();
            $table->string('almacenamiento')->nullable(); // 64GB, 128GB, 256GB
            $table->string('ram')->nullable();            // 4GB, 6GB, 8GB
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('imagen')->nullable();
            $table->string('imei')->nullable();
            $table->enum('condicion', ['nuevo', 'reacondicionado', 'usado'])->default('nuevo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
