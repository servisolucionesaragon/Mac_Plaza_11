<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaConteoDenominacion extends Model
{
    use HasFactory;

    protected $table = 'caja_conteo_denominaciones';

    protected $fillable = ['caja_id', 'denominacion_id', 'cantidad'];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function denominacion()
    {
        return $this->belongsTo(Denominacion::class);
    }
}
