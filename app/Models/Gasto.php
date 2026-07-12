<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_gasto', 'descripcion', 'monto', 'metodo_pago_id', 'user_id', 'notas',
    ];

    protected $casts = [
        'fecha_gasto' => 'datetime',
        'monto'       => 'decimal:2',
    ];

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
