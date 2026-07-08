<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CatalogoTipo;
use App\Models\CatalogoValor;

class CatalogoValorController extends Controller
{
    public function store(Request $request, int $catalogoTipo)
    {
        $tipo = CatalogoTipo::findOrFail($catalogoTipo);

        $validated = $request->validate([
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('catalogo_valores', 'nombre')->where('catalogo_tipo_id', $tipo->id),
            ],
        ]);

        CatalogoValor::create([
            'catalogo_tipo_id' => $tipo->id,
            'nombre'           => $validated['nombre'],
            'activo'           => true,
        ]);

        return back()->with('success', $validated['nombre'] . ' agregado correctamente.');
    }

    public function update(Request $request, int $valor)
    {
        $registro = CatalogoValor::findOrFail($valor);

        $validated = $request->validate([
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('catalogo_valores', 'nombre')
                    ->where('catalogo_tipo_id', $registro->catalogo_tipo_id)
                    ->ignore($registro->id),
            ],
        ]);

        $registro->update(['nombre' => $validated['nombre']]);

        return back()->with('success', 'Valor actualizado correctamente.');
    }

    public function toggle(int $valor)
    {
        $registro = CatalogoValor::findOrFail($valor);
        $registro->update(['activo' => !$registro->activo]);
        $estado = $registro->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Valor {$estado} correctamente.");
    }

    public function destroy(int $valor)
    {
        $registro = CatalogoValor::findOrFail($valor);
        $registro->delete();

        return back()->with('success', 'Valor eliminado correctamente.');
    }
}
