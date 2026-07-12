<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PermisoRol;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['caja', 'gastos', 'ingresos'] as $modulo) {
            PermisoRol::updateOrCreate(
                ['rol' => 'vendedor', 'modulo' => $modulo],
                ['permitido' => true]
            );
        }
    }

    public function down(): void
    {
        foreach (['caja', 'gastos', 'ingresos'] as $modulo) {
            PermisoRol::where('rol', 'vendedor')->where('modulo', $modulo)->update(['permitido' => false]);
        }
    }
};
