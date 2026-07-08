<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'categoria_id', 'marca_id',
        'modelo', 'color', 'almacenamiento_id', 'ram_id', 'precio_compra',
        'precio_venta', 'stock', 'stock_minimo', 'imagen',
        'requiere_imei', 'requiere_serial',
        'condicion_id', 'activo',
    ];

    protected $casts = [
        'precio_compra'   => 'decimal:2',
        'precio_venta'    => 'decimal:2',
        'activo'          => 'boolean',
        'requiere_imei'   => 'boolean',
        'requiere_serial' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function condicion()
    {
        return $this->belongsTo(Condicion::class);
    }

    public function almacenamiento()
    {
        return $this->belongsTo(Almacenamiento::class);
    }

    public function ram()
    {
        return $this->belongsTo(Ram::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function getMargenAttribute(): float
    {
        if ($this->precio_compra > 0) {
            return (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
        }
        return 0;
    }
}
