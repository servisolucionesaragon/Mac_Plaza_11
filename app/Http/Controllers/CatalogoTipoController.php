<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatalogoTipo;

class CatalogoTipoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:catalogo_tipos,nombre',
            'descripcion' => 'nullable|string|max:255',
            'icono'       => 'nullable|string|max:50',
        ]);

        CatalogoTipo::create([
            'nombre'      => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'icono'       => $validated['icono'] ?? 'fa-list',
            'activo'      => true,
        ]);

        return back()->with('success', 'Catálogo "' . $validated['nombre'] . '" creado correctamente.');
    }

    public function update(Request $request, int $catalogoTipo)
    {
        $tipo = CatalogoTipo::findOrFail($catalogoTipo);

        $validated = $request->validate([
            'nombre'      => "required|string|max:100|unique:catalogo_tipos,nombre,{$catalogoTipo}",
            'descripcion' => 'nullable|string|max:255',
            'icono'       => 'nullable|string|max:50',
        ]);

        $tipo->update([
            'nombre'      => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'icono'       => $validated['icono'] ?? $tipo->icono,
        ]);

        return back()->with('success', 'Catálogo actualizado correctamente.');
    }

    public function toggle(int $catalogoTipo)
    {
        $tipo = CatalogoTipo::findOrFail($catalogoTipo);
        $tipo->update(['activo' => !$tipo->activo]);
        $estado = $tipo->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Catálogo {$estado} correctamente.");
    }

    public function destroy(int $catalogoTipo)
    {
        $tipo = CatalogoTipo::findOrFail($catalogoTipo);
        $nombre = $tipo->nombre;
        $tipo->delete();

        return back()->with('success', "Catálogo \"{$nombre}\" y sus valores eliminados correctamente.");
    }
}
