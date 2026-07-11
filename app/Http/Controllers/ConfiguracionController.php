<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Configuracion;
use App\Models\PermisoRol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('rol')->orderBy('name')->get();
        $permisosMatriz = PermisoRol::all()->keyBy(fn($p) => $p->rol . '.' . $p->modulo);
        return view('configuracion.index', compact('usuarios', 'permisosMatriz'));
    }

    public function updatePermisos(Request $request)
    {
        $modulos = ['dashboard', 'clientes', 'productos', 'ventas', 'reparaciones', 'reportes'];

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

    public function updateGeneral(Request $request)
    {
        $config = Configuracion::actual();

        $validated = $request->validate([
            'nombre_tienda'   => 'required|string|max:255',
            'ruc'             => 'nullable|string|max:20',
            'telefono'        => 'nullable|string|max:20',
            'departamento'    => 'nullable|string|max:100',
            'ciudad'          => 'nullable|string|max:100',
            'direccion'       => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:150',
            'pagina_web'      => 'nullable|string|max:150',
            'timezone'        => 'required|string|max:64',
            'moneda'          => 'required|string|max:10',
            'simbolo_moneda'  => 'required|string|max:10',
            'igv'             => 'required|numeric|min:0|max:100',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:4096',
            'color_primario'    => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_secundario'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_acento'      => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_sidebar'     => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_menu_texto'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_menu_activo' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_boton_texto' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_boton_fondo' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_grafico_1'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_grafico_2'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_grafico_3'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'logo.image'  => 'El archivo debe ser una imagen (JPG, PNG, GIF, BMP o WEBP). Fotos en formato HEIC de iPhone no son compatibles: conviértelas a JPG o PNG antes de subirlas.',
            'logo.mimes'  => 'El archivo debe ser una imagen (JPG, PNG, GIF, BMP o WEBP). Fotos en formato HEIC de iPhone no son compatibles: conviértelas a JPG o PNG antes de subirlas.',
            'logo.max'    => 'El logo no puede pesar más de 4 MB.',
        ]);

        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo);
            }
            $validated['logo'] = $request->file('logo')->store('configuracion', 'public');
        }

        $config->update($validated);

        return redirect()->route('configuracion.index')->with('success', 'Configuración general actualizada correctamente.');
    }

    public function storeUsuario(Request $request)
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

    public function updateUsuario(Request $request, User $usuario)
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

    public function toggleUsuario(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }
        $usuario->update(['activo' => !$usuario->activo]);
        $estado = $usuario->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$estado} correctamente.");
    }

    public function destroyUsuario(User $usuario)
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
