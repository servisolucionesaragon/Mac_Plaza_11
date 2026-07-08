<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacenamientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('almacenamientos')->insert([
            ['nombre' => '32GB', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => '64GB', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => '128GB', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => '256GB', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => '512GB', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => '1TB', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('almacenamientos');
    }
};
