<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'email', 'telefono', 'celular', 'dni',
        'tipo_documento', 'direccion', 'departamento', 'ciudad',
        'fecha_nacimiento', 'tipo', 'empresa', 'ruc', 'notas', 'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class);
    }

    public function totalCompras(): float
    {
        return $this->ventas()->where('estado', 'completada')->sum('total');
    }
}
