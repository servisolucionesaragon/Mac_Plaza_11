<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoValor extends Model
{
    use HasFactory;

    protected $table = 'catalogo_valores';

    protected $fillable = ['catalogo_tipo_id', 'nombre', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function tipo()
    {
        return $this->belongsTo(CatalogoTipo::class, 'catalogo_tipo_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_catalogo_valor');
    }
}
