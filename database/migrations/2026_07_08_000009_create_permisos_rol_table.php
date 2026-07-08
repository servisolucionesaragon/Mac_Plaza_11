<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_rol', function (Blueprint $table) {
            $table->id();
            $table->string('rol');
            $table->string('modulo');
            $table->boolean('permitido')->default(false);
            $table->timestamps();

            $table->unique(['rol', 'modulo']);
        });

        $ahora = now();

        $permisos = [
            'vendedor' => [
                'dashboard'    => true,
                'clientes'     => true,
                'productos'    => false,
                'ventas'       => true,
                'reparaciones' => true,
                'reportes'     => true,
            ],
            'tecnico' => [
                'dashboard'    => false,
                'clientes'     => false,
                'productos'    => false,
                'ventas'       => false,
                'reparaciones' => true,
                'reportes'     => false,
            ],
        ];

        $filas = [];
        foreach ($permisos as $rol => $modulos) {
            foreach ($modulos as $modulo => $permitido) {
                $filas[] = [
                    'rol'        => $rol,
                    'modulo'     => $modulo,
                    'permitido'  => $permitido,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ];
            }
        }

        DB::table('permisos_rol')->insert($filas);
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_rol');
    }
};
