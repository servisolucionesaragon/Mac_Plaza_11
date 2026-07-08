<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoTipo extends Model
{
    use HasFactory;

    protected $table = 'catalogo_tipos';

    protected $fillable = ['nombre', 'descripcion', 'icono', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function valores()
    {
        return $this->hasMany(CatalogoValor::class, 'catalogo_tipo_id')->orderBy('nombre');
    }
}
