<?php

namespace App\Http\Controllers;

use App\Models\Reparacion;
use App\Models\Cliente;
use App\Models\User;
use App\Models\HistorialReparacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReparacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Reparacion::with(['cliente', 'tecnico']);

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero_orden', 'like', "%{$request->buscar}%")
                  ->orWhere('dispositivo', 'like', "%{$request->buscar}%")
                  ->orWhereHas('cliente', fn($cq) =>
                      $cq->where('nombre', 'like', "%{$request->buscar}%")
                         ->orWhere('apellido', 'like', "%{$request->buscar}%")
                  );
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $reparaciones = $query->orderByDesc('fecha_recepcion')->paginate(15);
        $estadisticas = [
            'recibidos'   => Reparacion::where('estado', 'recibido')->count(),
            'en_proceso'  => Reparacion::whereIn('estado', ['en_diagnostico','en_reparacion','esperando_repuesto'])->count(),
            'listos'      => Reparacion::where('estado', 'listo')->count(),
            'entregados'  => Reparacion::where('estado', 'entregado')->count(),
        ];

        return view('reparaciones.index', compact('reparaciones', 'estadisticas'));
    }

    public function create()
    {
        $clientes  = Cliente::where('activo', true)->orderBy('nombre')->get();
        $tecnicos  = User::where('rol', 'tecnico')->where('activo', true)->orderBy('name')->get();
        return view('reparaciones.create', compact('clientes', 'tecnicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'      => 'required|exists:clientes,id',
            'tecnico_id'      => 'required|exists:users,id',
            'dispositivo'     => 'required|string|max:150',
            'marca'           => 'nullable|string|max:80',
            'modelo'          => 'nullable|string|max:100',
            'imei'            => 'nullable|string|max:20',
            'color'           => 'nullable|string|max:50',
            'falla_reportada' => 'required|string',
            'presupuesto'     => 'nullable|numeric|min:0',
            'prioridad'       => 'required|in:baja,media,alta,urgente',
            'fecha_estimada'  => 'nullable|date',
            'notas'           => 'nullable|string',
        ]);

        $validated['numero_orden']    = Reparacion::generarNumero();
        $validated['estado']          = 'recibido';
        $validated['fecha_recepcion'] = now();

        $reparacion = Reparacion::create($validated);

        HistorialReparacion::create([
            'reparacion_id'   => $reparacion->id,
            'user_id'         => Auth::id(),
            'estado_anterior' => null,
            'estado_nuevo'    => 'recibido',
            'nota'            => 'Orden creada.',
        ]);

        return redirect()->route('reparaciones.index')
            ->with('success', 'Orden de reparación registrada correctamente.');
    }

    public function show(Reparacion $reparacion)
    {
        $reparacion->load(['cliente', 'tecnico', 'historial.usuario']);
        return view('reparaciones.show', compact('reparacion'));
    }

    public function recibo(Reparacion $reparacion)
    {
        $reparacion->load(['cliente', 'tecnico']);
        return view('reparaciones.recibo', compact('reparacion'));
    }

    /** Recibo de reparación accesible sin login, vía link firmado (para compartir por WhatsApp). */
    public function reciboPublico(Reparacion $reparacion)
    {
        $reparacion->load(['cliente', 'tecnico']);
        $layout = 'layouts.publico';
        $publico = true;
        return view('reparaciones.recibo', compact('reparacion', 'layout', 'publico'));
    }

    public function edit(Reparacion $reparacion)
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        $tecnicos = User::where('rol', 'tecnico')->where('activo', true)->orderBy('name')->get();
        return view('reparaciones.edit', compact('reparacion', 'clientes', 'tecnicos'));
    }

    public function update(Request $request, Reparacion $reparacion)
    {
        $validated = $request->validate([
            'tecnico_id'      => 'required|exists:users,id',
            'dispositivo'     => 'required|string|max:150',
            'marca'           => 'nullable|string|max:80',
            'modelo'          => 'nullable|string|max:100',
            'imei'            => 'nullable|string|max:20',
            'color'           => 'nullable|string|max:50',
            'falla_reportada' => 'required|string',
            'diagnostico'     => 'nullable|string',
            'solucion'        => 'nullable|string',
            'presupuesto'     => 'nullable|numeric|min:0',
            'costo_final'     => 'nullable|numeric|min:0',
            'estado'          => 'required|in:recibido,en_diagnostico,esperando_repuesto,en_reparacion,listo,entregado,no_reparable',
            'prioridad'       => 'required|in:baja,media,alta,urgente',
            'fecha_estimada'  => 'nullable|date',
            'garantia'        => 'boolean',
            'dias_garantia'   => 'nullable|integer|min:0',
            'notas'           => 'nullable|string',
            'nota_cambio'     => 'nullable|string',
        ]);

        $estadoAnterior = $reparacion->estado;

        if ($validated['estado'] === 'entregado' && !$reparacion->fecha_entrega) {
            $validated['fecha_entrega'] = now();
        }

        $reparacion->update($validated);

        if ($estadoAnterior !== $validated['estado'] || $request->filled('nota_cambio')) {
            HistorialReparacion::create([
                'reparacion_id'   => $reparacion->id,
                'user_id'         => Auth::id(),
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => $validated['estado'],
                'nota'            => $request->nota_cambio,
            ]);
        }

        return redirect()->route('reparaciones.show', $reparacion)
            ->with('success', 'Reparación actualizada correctamente.');
    }
}
