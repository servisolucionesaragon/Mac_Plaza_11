<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialReparacion extends Model
{
    use HasFactory;

    protected $table = 'reparacion_historial';

    protected $fillable = [
        'reparacion_id', 'user_id', 'estado_anterior', 'estado_nuevo', 'nota',
    ];

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
