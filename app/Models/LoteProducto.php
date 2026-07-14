<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoteProducto extends Model
{
    use HasFactory;

    protected $table = 'lotes_producto';

    protected $fillable = [
        'producto_id', 'costo_unitario', 'proveedor', 'fecha_ingreso', 'notas', 'user_id',
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'fecha_ingreso'  => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function variantes()
    {
        return $this->hasMany(LoteVariante::class, 'lote_id');
    }
}
