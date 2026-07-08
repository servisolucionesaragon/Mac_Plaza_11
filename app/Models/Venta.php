<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_venta', 'cliente_id', 'user_id', 'fecha_venta',
        'subtotal', 'descuento', 'impuesto', 'total', 'modo_precio',
        'metodo_pago_id', 'estado', 'notas',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'subtotal'    => 'decimal:2',
        'descuento'   => 'decimal:2',
        'impuesto'    => 'decimal:2',
        'total'       => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public static function generarNumero(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_venta, 4) + 1 : 1;
        return 'VTA-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
