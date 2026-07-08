<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    use HasFactory;

    protected $table = 'reparaciones';

    protected $fillable = [
        'numero_orden', 'cliente_id', 'tecnico_id', 'dispositivo', 'marca',
        'modelo', 'imei', 'color', 'falla_reportada', 'diagnostico',
        'solucion', 'presupuesto', 'costo_final', 'estado', 'prioridad',
        'fecha_recepcion', 'fecha_estimada', 'fecha_entrega', 'notas',
        'garantia', 'dias_garantia',
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime',
        'fecha_estimada'  => 'datetime',
        'fecha_entrega'   => 'datetime',
        'presupuesto'     => 'decimal:2',
        'costo_final'     => 'decimal:2',
        'garantia'        => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialReparacion::class)->latest();
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'recibido'           => 'secondary',
            'en_diagnostico'     => 'info',
            'esperando_repuesto' => 'warning',
            'en_reparacion'      => 'primary',
            'listo'              => 'success',
            'entregado'          => 'dark',
            'no_reparable'       => 'danger',
            default              => 'secondary',
        };
    }

    public static function generarNumero(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_orden, 4) + 1 : 1;
        return 'REP-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
