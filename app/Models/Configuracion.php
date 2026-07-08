<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuracion';

    protected $fillable = [
        'nombre_tienda', 'ruc', 'direccion', 'departamento', 'ciudad',
        'telefono', 'email', 'pagina_web', 'logo',
        'igv', 'moneda', 'simbolo_moneda', 'terminos_garantia', 'timezone',
        'color_primario', 'color_secundario', 'color_acento', 'color_sidebar',
    ];

    protected $casts = [
        'igv' => 'decimal:2',
    ];

    /**
     * Devuelve la fila única de configuración (singleton, id 1),
     * creándola con valores por defecto si aún no existe.
     */
    public static function actual(): self
    {
        return self::firstOrCreate([], [
            'nombre_tienda'  => 'CRM Celulares',
            'igv'            => 18.00,
            'moneda'         => 'COP',
            'simbolo_moneda' => '$',
            'timezone'       => 'America/Bogota',
        ]);
    }
}
