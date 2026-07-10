<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->boolean('es_credito')->default(false)->after('estado');
            $table->decimal('saldo_pendiente', 10, 2)->default(0)->after('es_credito');
            $table->date('fecha_vencimiento')->nullable()->after('saldo_pendiente');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['es_credito', 'saldo_pendiente', 'fecha_vencimiento']);
        });
    }
};
