<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaConteo extends Model
{
    use HasFactory;

    protected $fillable = ['caja_id', 'metodo_pago_id', 'monto_contado'];

    protected $casts = [
        'monto_contado' => 'decimal:2',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }
}
