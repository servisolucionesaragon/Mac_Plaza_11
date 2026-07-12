<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Gasto;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $query = Gasto::with('metodoPago', 'usuario');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_gasto', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_gasto', '<=', $request->fecha_hasta);
        }

        if ($request->filled('metodo_pago_id')) {
            $query->where('metodo_pago_id', $request->metodo_pago_id);
        }

        $gastos = $query->orderByDesc('fecha_gasto')->paginate(15)->withQueryString();

        $totalPeriodo = (clone $query)->sum('monto');

        $metodosPago = MetodoPago::where('activo', true)->orderBy('nombre')->get();
        $cajaAbierta = (bool) Caja::abiertaActual();

        return view('gastos.index', compact('gastos', 'totalPeriodo', 'metodosPago', 'cajaAbierta'));
    }

    public function store(Request $request)
    {
        if (!Caja::abiertaActual()) {
            return back()->with('error', 'Debes abrir la caja del día antes de registrar un gasto.')->withInput();
        }

        $validated = $request->validate([
            'fecha_gasto'    => 'nullable|date',
            'descripcion'    => 'required|string|max:255',
            'monto'          => 'required|numeric|min:0.01',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'notas'          => 'nullable|string',
        ]);

        Gasto::create([
            'fecha_gasto'    => $validated['fecha_gasto'] ?? now(),
            'descripcion'    => $validated['descripcion'],
            'monto'          => $validated['monto'],
            'metodo_pago_id' => $validated['metodo_pago_id'],
            'user_id'        => Auth::id(),
            'notas'          => $validated['notas'] ?? null,
        ]);

        return back()->with('success', 'Gasto registrado correctamente.');
    }

    public function update(Request $request, Gasto $gasto)
    {
        abort_unless(Auth::user()->esAdmin(), 403, 'Solo un administrador puede editar un gasto ya registrado.');

        $validated = $request->validate([
            'fecha_gasto'    => 'nullable|date',
            'descripcion'    => 'required|string|max:255',
            'monto'          => 'required|numeric|min:0.01',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'notas'          => 'nullable|string',
        ]);

        $gasto->update([
            'fecha_gasto'    => $validated['fecha_gasto'] ?? $gasto->fecha_gasto,
            'descripcion'    => $validated['descripcion'],
            'monto'          => $validated['monto'],
            'metodo_pago_id' => $validated['metodo_pago_id'],
            'notas'          => $validated['notas'] ?? null,
        ]);

        return back()->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Gasto $gasto)
    {
        abort_unless(Auth::user()->esAdmin(), 403, 'Solo un administrador puede eliminar un gasto.');

        $gasto->delete();

        return back()->with('success', 'Gasto eliminado correctamente.');
    }
}
