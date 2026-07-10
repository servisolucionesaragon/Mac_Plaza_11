<?php

namespace App\Models;

use App\Traits\TieneWhatsapp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory, TieneWhatsapp;

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

    public function cumpleAnioEsteMes(): bool
    {
        return $this->fecha_nacimiento && $this->fecha_nacimiento->month === now()->month;
    }

    public function scopeConCumpleanioEsteMes($query)
    {
        return $query->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', now()->month);
    }

    /**
     * Número limpio para WhatsApp (wa.me), con indicativo +57 (Colombia) por ahora.
     * Prefiere celular sobre teléfono fijo.
     */
    public function numeroWhatsapp(): ?string
    {
        return $this->limpiarNumeroWhatsapp($this->celular ?: $this->telefono);
    }

    public function whatsappUrl(string $mensaje = ''): ?string
    {
        return $this->armarWhatsappUrl($this->numeroWhatsapp(), $mensaje);
    }
}
