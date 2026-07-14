<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVentaLote extends Model
{
    use HasFactory;

    protected $table = 'detalle_venta_lote';

    protected $fillable = ['detalle_venta_id', 'lote_variante_id', 'cantidad', 'costo_unitario'];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
    ];

    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class);
    }

    public function loteVariante()
    {
        return $this->belongsTo(LoteVariante::class, 'lote_variante_id');
    }
}
