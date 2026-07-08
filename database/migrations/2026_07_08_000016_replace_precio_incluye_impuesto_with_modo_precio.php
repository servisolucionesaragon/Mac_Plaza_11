<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('modo_precio')->default('subtotal_impuesto')->after('impuesto');
        });

        DB::table('ventas')->where('precio_incluye_impuesto', true)->update(['modo_precio' => 'incluido']);
        DB::table('ventas')->where('precio_incluye_impuesto', false)->update(['modo_precio' => 'subtotal_impuesto']);

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('precio_incluye_impuesto');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->boolean('precio_incluye_impuesto')->default(false)->after('impuesto');
        });

        DB::table('ventas')->where('modo_precio', 'incluido')->update(['precio_incluye_impuesto' => true]);

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('modo_precio');
        });
    }
};
