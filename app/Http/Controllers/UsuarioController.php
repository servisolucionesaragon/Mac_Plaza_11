<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PermisoRol;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('rol')->orderBy('name')->get();
        $permisosMatriz = PermisoRol::all()->keyBy(fn($p) => $p->rol . '.' . $p->modulo);
        return view('usuarios.index', compact('usuarios', 'permisosMatriz'));
    }

    public function updatePermisos(Request $request)
    {
        $modulos = ['dashboard', 'clientes', 'productos', 'ventas', 'caja', 'gastos', 'ingresos', 'reparaciones', 'reportes', 'usuarios', 'configuracion', 'backup'];

        foreach (['vendedor', 'tecnico'] as $rol) {
            foreach ($modulos as $modulo) {
                PermisoRol::updateOrCreate(
                    ['rol' => $rol, 'modulo' => $modulo],
                    ['permitido' => $request->boolean("permisos.$rol.$modulo")]
                );
            }
        }

        return back()->with('success', 'Permisos de roles actualizados correctamente.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol'      => 'required|in:admin,vendedor,tecnico',
            'telefono' => 'nullable|string|max:20',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol'      => $validated['rol'],
            'telefono' => $validated['telefono'] ?? null,
        ]);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
            'rol'      => 'required|in:admin,vendedor,tecnico',
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'rol'      => $validated['rol'],
            'telefono' => $validated['telefono'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $usuario->update($data);
        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggle(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }
        $usuario->update(['activo' => !$usuario->activo]);
        $estado = $usuario->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$estado} correctamente.");
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        if ($usuario->ventas()->exists() || $usuario->abonos()->exists() || $usuario->reparaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: el usuario tiene ventas, abonos o reparaciones asociadas. Desactívalo en su lugar.');
        }
        $usuario->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
