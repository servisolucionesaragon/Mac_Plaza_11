<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denominacion extends Model
{
    use HasFactory;

    protected $table = 'denominaciones';

    protected $fillable = ['valor', 'tipo', 'imagen', 'activo'];

    protected $casts = [
        'valor'  => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function conteos()
    {
        return $this->hasMany(CajaConteoDenominacion::class);
    }
}
