<?php

namespace App\Models;

use App\Traits\TieneWhatsapp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory, TieneWhatsapp;

    protected $table = 'configuracion';

    protected $fillable = [
        'nombre_tienda', 'ruc', 'direccion', 'departamento', 'ciudad',
        'telefono', 'email', 'pagina_web', 'logo',
        'igv', 'moneda', 'simbolo_moneda', 'terminos_garantia', 'timezone',
        'color_primario', 'color_secundario', 'color_acento', 'color_sidebar',
        'color_menu_texto', 'color_menu_activo', 'color_boton_texto', 'color_boton_fondo',
        'color_grafico_1', 'color_grafico_2', 'color_grafico_3',
        'color_login_fondo', 'color_login_tarjeta', 'color_login_texto_modulos',
        'descuento_distribuidor',
    ];

    protected $casts = [
        'igv' => 'decimal:2',
        'descuento_distribuidor' => 'decimal:2',
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
            'descuento_distribuidor' => 20.00,
        ]);
    }

    public function numeroWhatsapp(): ?string
    {
        return $this->limpiarNumeroWhatsapp($this->telefono);
    }

    public function whatsappUrl(string $mensaje = ''): ?string
    {
        return $this->armarWhatsappUrl($this->numeroWhatsapp(), $mensaje);
    }
}
