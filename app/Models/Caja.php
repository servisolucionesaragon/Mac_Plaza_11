<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha', 'monto_inicial', 'notas_apertura', 'user_apertura_id', 'fecha_apertura',
        'estado', 'notas_cierre', 'user_cierre_id', 'fecha_cierre',
    ];

    protected $casts = [
        'fecha'          => 'date',
        'monto_inicial'  => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre'   => 'datetime',
    ];

    public function usuarioApertura()
    {
        return $this->belongsTo(User::class, 'user_apertura_id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'user_cierre_id');
    }

    public function conteos()
    {
        return $this->hasMany(CajaConteo::class);
    }

    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    public static function abiertaActual(): ?self
    {
        return self::where('estado', 'abierta')->latest('fecha_apertura')->first();
    }
}
