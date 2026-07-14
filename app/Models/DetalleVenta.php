<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id', 'producto_id', 'cantidad', 'precio_unitario',
        'descuento', 'subtotal', 'imei_vendido', 'serial_vendido',
        'color_id', 'almacenamiento_id', 'ram_id',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'descuento'       => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function lotes()
    {
        return $this->hasMany(DetalleVentaLote::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function almacenamiento()
    {
        return $this->belongsTo(Almacenamiento::class);
    }

    public function ram()
    {
        return $this->belongsTo(Ram::class);
    }
}
