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
        $numero = $this->celular ?: $this->telefono;
        if (!$numero) {
            return null;
        }

        $limpio = preg_replace('/\D+/', '', $numero);
        if (!$limpio) {
            return null;
        }

        if (!str_starts_with($limpio, '57') || strlen($limpio) === 10) {
            $limpio = '57' . ltrim($limpio, '0');
        }

        return $limpio;
    }

    public function whatsappUrl(string $mensaje = ''): ?string
    {
        $numero = $this->numeroWhatsapp();
        if (!$numero) {
            return null;
        }

        return 'https://wa.me/' . $numero . ($mensaje !== '' ? '?text=' . urlencode($mensaje) : '');
    }
}
