<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('requiere_serial')->default(false)->after('requiere_imei');
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->string('serial_vendido')->nullable()->after('imei_vendido');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('requiere_serial');
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn('serial_vendido');
        });
    }
};
