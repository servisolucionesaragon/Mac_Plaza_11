<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerificarPermisoModulo
{
    public function handle(Request $request, Closure $next, string $modulo)
    {
        if (!$request->user()->puedeAcceder($modulo)) {
            $destino = $request->user()->primerModuloPermitido();

            if (!$destino) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Tu usuario no tiene ningún módulo habilitado. Contacta al administrador.',
                ]);
            }

            return redirect()->route(User::rutaModulo($destino))
                ->with('error', 'No tienes permiso para acceder a esa sección.');
        }

        return $next($request);
    }
}
