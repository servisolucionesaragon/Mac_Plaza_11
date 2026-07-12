<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Ingreso;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
        $query = Ingreso::with('metodoPago', 'usuario');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_hasta);
        }

        if ($request->filled('metodo_pago_id')) {
            $query->where('metodo_pago_id', $request->metodo_pago_id);
        }

        $ingresos = $query->orderByDesc('fecha_ingreso')->paginate(15)->withQueryString();

        $totalPeriodo = (clone $query)->sum('monto');

        $metodosPago = MetodoPago::where('activo', true)->orderBy('nombre')->get();
        $cajaAbierta = (bool) Caja::abiertaActual();

        return view('ingresos.index', compact('ingresos', 'totalPeriodo', 'metodosPago', 'cajaAbierta'));
    }

    public function store(Request $request)
    {
        if (!Caja::abiertaActual()) {
            return back()->with('error', 'Debes abrir la caja del día antes de registrar un ingreso.')->withInput();
        }

        $validated = $request->validate([
            'fecha_ingreso'  => 'nullable|date',
            'descripcion'    => 'required|string|max:255',
            'monto'          => 'required|numeric|min:0.01',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'notas'          => 'nullable|string',
        ]);

        Ingreso::create([
            'fecha_ingreso'  => $validated['fecha_ingreso'] ?? now(),
            'descripcion'    => $validated['descripcion'],
            'monto'          => $validated['monto'],
            'metodo_pago_id' => $validated['metodo_pago_id'],
            'user_id'        => Auth::id(),
            'notas'          => $validated['notas'] ?? null,
        ]);

        return back()->with('success', 'Ingreso registrado correctamente.');
    }

    public function update(Request $request, Ingreso $ingreso)
    {
        abort_unless(Auth::user()->esAdmin(), 403, 'Solo un administrador puede editar un ingreso ya registrado.');

        $validated = $request->validate([
            'fecha_ingreso'  => 'nullable|date',
            'descripcion'    => 'required|string|max:255',
            'monto'          => 'required|numeric|min:0.01',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'notas'          => 'nullable|string',
        ]);

        $ingreso->update([
            'fecha_ingreso'  => $validated['fecha_ingreso'] ?? $ingreso->fecha_ingreso,
            'descripcion'    => $validated['descripcion'],
            'monto'          => $validated['monto'],
            'metodo_pago_id' => $validated['metodo_pago_id'],
            'notas'          => $validated['notas'] ?? null,
        ]);

        return back()->with('success', 'Ingreso actualizado correctamente.');
    }

    public function destroy(Ingreso $ingreso)
    {
        abort_unless(Auth::user()->esAdmin(), 403, 'Solo un administrador puede eliminar un ingreso.');

        $ingreso->delete();

        return back()->with('success', 'Ingreso eliminado correctamente.');
    }
}
