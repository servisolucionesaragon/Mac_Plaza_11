<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoteVariante extends Model
{
    use HasFactory;

    protected $fillable = [
        'lote_id', 'color_id', 'almacenamiento_id', 'ram_id',
        'cantidad_inicial', 'cantidad_restante',
    ];

    public function lote()
    {
        return $this->belongsTo(LoteProducto::class, 'lote_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function almacenamiento()
    {
        return $this->belongsTo(Almacenamiento::class);
    }

    public function ram()
    {
        return $this->belongsTo(Ram::class);
    }
}
