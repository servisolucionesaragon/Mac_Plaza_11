<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoRol extends Model
{
    use HasFactory;

    protected $table = 'permisos_rol';

    protected $fillable = ['rol', 'modulo', 'permitido'];

    protected $casts = ['permitido' => 'boolean'];
}
