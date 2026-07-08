<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'rol', 'telefono', 'foto', 'activo',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'user_id');
    }

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class, 'tecnico_id');
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esVendedor(): bool
    {
        return $this->rol === 'vendedor';
    }

    public function esTecnico(): bool
    {
        return $this->rol === 'tecnico';
    }

    public function puedeAcceder(string $modulo): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        return (bool) PermisoRol::where('rol', $this->rol)
            ->where('modulo', $modulo)
            ->value('permitido');
    }

    public static function modulosPrioridad(): array
    {
        return ['dashboard', 'clientes', 'productos', 'ventas', 'reparaciones', 'reportes'];
    }

    public static function rutaModulo(string $modulo): string
    {
        return [
            'dashboard'    => 'dashboard',
            'clientes'     => 'clientes.index',
            'productos'    => 'productos.index',
            'ventas'       => 'ventas.index',
            'reparaciones' => 'reparaciones.index',
            'reportes'     => 'reportes.index',
        ][$modulo];
    }

    public function primerModuloPermitido(): ?string
    {
        if ($this->esAdmin()) {
            return 'dashboard';
        }

        foreach (self::modulosPrioridad() as $modulo) {
            if ($this->puedeAcceder($modulo)) {
                return $modulo;
            }
        }

        return null;
    }
}
