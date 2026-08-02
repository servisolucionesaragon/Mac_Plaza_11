<?php

namespace App\Http\Controllers;

use App\Models\Denominacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DenominacionController extends Controller
{
    public function index()
    {
        $billetes = Denominacion::where('tipo', 'billete')->orderByDesc('valor')->get();
        $monedas  = Denominacion::where('tipo', 'moneda')->orderByDesc('valor')->get();

        return view('denominaciones.index', compact('billetes', 'monedas'));
    }

    public function store(Request $request)
    {
        $validated = $this->validarDatos($request);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('denominaciones', 'public');
        }

        Denominacion::create($validated);

        return back()->with('success', 'Denominación agregada correctamente.');
    }

    public function update(Request $request, Denominacion $denominacion)
    {
        $validated = $this->validarDatos($request, $denominacion->id);

        if ($request->hasFile('imagen')) {
            if ($denominacion->imagen) {
                Storage::disk('public')->delete($denominacion->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('denominaciones', 'public');
        }

        $denominacion->update($validated);

        return back()->with('success', 'Denominación actualizada correctamente.');
    }

    public function toggle(Denominacion $denominacion)
    {
        $denominacion->update(['activo' => !$denominacion->activo]);
        $estado = $denominacion->activo ? 'activada' : 'desactivada';

        return back()->with('success', "Denominación {$estado} correctamente.");
    }

    public function destroy(Denominacion $denominacion)
    {
        if ($denominacion->conteos()->exists()) {
            return back()->with('error', 'No se puede eliminar: ya se usó en un cierre de caja. Desactívala en su lugar.');
        }

        if ($denominacion->imagen) {
            Storage::disk('public')->delete($denominacion->imagen);
        }

        $denominacion->delete();

        return back()->with('success', 'Denominación eliminada correctamente.');
    }

    private function validarDatos(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'valor' => [
                'required', 'integer', 'min:1',
                Rule::unique('denominaciones', 'valor')
                    ->where(fn ($q) => $q->where('tipo', $request->tipo))
                    ->ignore($ignorarId),
            ],
            'tipo'   => 'required|in:billete,moneda',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:4096',
        ], [
            'valor.unique' => 'Ya existe una denominación con ese valor y tipo.',
            'imagen.image' => 'El archivo debe ser una imagen (JPG, PNG, GIF, BMP o WEBP). Fotos en formato HEIC de iPhone no son compatibles: conviértelas a JPG o PNG antes de subirlas.',
            'imagen.mimes' => 'El archivo debe ser una imagen (JPG, PNG, GIF, BMP o WEBP). Fotos en formato HEIC de iPhone no son compatibles: conviértelas a JPG o PNG antes de subirlas.',
            'imagen.max'   => 'La imagen no puede pesar más de 4 MB.',
        ]);
    }
}
