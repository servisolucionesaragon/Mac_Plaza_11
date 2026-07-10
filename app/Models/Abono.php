<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id', 'monto', 'fecha_abono', 'metodo_pago_id', 'user_id', 'notas',
    ];

    protected $casts = [
        'monto'       => 'decimal:2',
        'fecha_abono' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
