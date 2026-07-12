<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Caja;
use App\Models\CajaConteo;
use App\Models\Gasto;
use App\Models\Ingreso;
use App\Models\MetodoPago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $cajaActual = Caja::abiertaActual();

        $query = Caja::with('usuarioApertura', 'usuarioCierre');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $cajas = $query->orderByDesc('fecha_apertura')->paginate(15)->withQueryString();

        return view('caja.index', compact('cajaActual', 'cajas'));
    }

    public function create()
    {
        if (Caja::abiertaActual()) {
            return redirect()->route('caja.index')->with('error', 'Ya hay una caja abierta.');
        }

        $ultimoCierre = Caja::where('estado', 'cerrada')->latest('fecha_cierre')->first();
        $ultimoConteoEfectivo = null;
        if ($ultimoCierre) {
            $ultimoConteoEfectivo = $ultimoCierre->conteos()
                ->whereHas('metodoPago', fn($q) => $q->whereRaw('LOWER(nombre) = ?', ['efectivo']))
                ->value('monto_contado');
        }

        return view('caja.create', compact('ultimoCierre', 'ultimoConteoEfectivo'));
    }

    public function store(Request $request)
    {
        if (Caja::abiertaActual()) {
            return back()->with('error', 'Ya hay una caja abierta. Debes cerrarla antes de abrir una nueva.');
        }

        $validated = $request->validate([
            'monto_inicial'  => 'required|numeric|min:0',
            'notas_apertura' => 'nullable|string',
        ]);

        $caja = Caja::create([
            'fecha'            => now()->toDateString(),
            'monto_inicial'    => $validated['monto_inicial'],
            'notas_apertura'   => $validated['notas_apertura'] ?? null,
            'user_apertura_id' => Auth::id(),
            'fecha_apertura'   => now(),
            'estado'           => 'abierta',
        ]);

        return redirect()->route('caja.show', $caja)->with('success', 'Caja abierta correctamente.');
    }

    public function show(Caja $caja)
    {
        $esperadoPorMetodo = $this->calcularEsperadoPorMetodo($caja);
        $totalEsperado = $esperadoPorMetodo->sum('esperado');

        $caja->load('conteos.metodoPago', 'usuarioApertura', 'usuarioCierre');
        $totalContado = $caja->conteos->sum('monto_contado');

        $fecha = $caja->fecha->format('Y-m-d');
        $gastosDelDia = Gasto::with('metodoPago', 'usuario')->whereDate('fecha_gasto', $fecha)->orderBy('fecha_gasto')->get();
        $ingresosDelDia = Ingreso::with('metodoPago', 'usuario')->whereDate('fecha_ingreso', $fecha)->orderBy('fecha_ingreso')->get();

        return view('caja.show', compact('caja', 'esperadoPorMetodo', 'totalEsperado', 'totalContado', 'gastosDelDia', 'ingresosDelDia'));
    }

    public function cierreForm(Caja $caja)
    {
        abort_if(!$caja->estaAbierta(), 403, 'Esta caja ya está cerrada.');

        $esperadoPorMetodo = $this->calcularEsperadoPorMetodo($caja);
        $totalEsperado = $esperadoPorMetodo->sum('esperado');

        return view('caja.cierre', compact('caja', 'esperadoPorMetodo', 'totalEsperado'));
    }

    public function cerrar(Request $request, Caja $caja)
    {
        abort_if(!$caja->estaAbierta(), 403, 'Esta caja ya está cerrada.');

        $validated = $request->validate([
            'notas_cierre'  => 'nullable|string',
            'conteos'       => 'required|array',
            'conteos.*'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($caja, $validated) {
            foreach ($validated['conteos'] as $metodoPagoId => $montoContado) {
                CajaConteo::create([
                    'caja_id'        => $caja->id,
                    'metodo_pago_id' => $metodoPagoId,
                    'monto_contado'  => $montoContado,
                ]);
            }

            $caja->update([
                'estado'         => 'cerrada',
                'notas_cierre'   => $validated['notas_cierre'] ?? null,
                'user_cierre_id' => Auth::id(),
                'fecha_cierre'   => now(),
            ]);
        });

        return redirect()->route('caja.reporte', $caja)->with('success', 'Caja cerrada correctamente.');
    }

    public function reporte(Caja $caja)
    {
        return view('caja.reporte', $this->datosReporte($caja));
    }

    public function reportePdf(Caja $caja)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('caja.reporte-pdf', $this->datosReporte($caja))
            ->setPaper('letter', 'portrait');

        return $pdf->download('cierre-caja-' . $caja->fecha->format('Y-m-d') . '.pdf');
    }

    private function datosReporte(Caja $caja): array
    {
        $fecha = $caja->fecha->format('Y-m-d');

        $ventasContado = Venta::with('metodoPago')
            ->where('es_credito', false)
            ->where('estado', 'completada')
            ->whereDate('fecha_venta', $fecha)
            ->get();

        $abonosDelDia = Abono::with('metodoPago')->whereDate('fecha_abono', $fecha)->get();
        $gastosDelDia = Gasto::with('metodoPago', 'usuario')->whereDate('fecha_gasto', $fecha)->orderBy('fecha_gasto')->get();
        $ingresosDelDia = Ingreso::with('metodoPago', 'usuario')->whereDate('fecha_ingreso', $fecha)->orderBy('fecha_ingreso')->get();

        $totalVentasContado = $ventasContado->sum('total');
        $cantidadVentasContado = $ventasContado->count();
        $totalDescuentos = $ventasContado->sum('descuento');
        $totalAbonos = $abonosDelDia->sum('monto');
        $totalGastos = $gastosDelDia->sum('monto');
        $totalIngresos = $ingresosDelDia->sum('monto');

        $esperadoPorMetodo = $this->calcularEsperadoPorMetodo($caja);
        $totalEsperado = $esperadoPorMetodo->sum('esperado');

        $caja->load('conteos.metodoPago', 'usuarioApertura', 'usuarioCierre');
        $totalContado = $caja->conteos->sum('monto_contado');

        return compact(
            'caja', 'ventasContado', 'abonosDelDia', 'gastosDelDia', 'ingresosDelDia',
            'totalVentasContado', 'cantidadVentasContado', 'totalDescuentos', 'totalAbonos',
            'totalGastos', 'totalIngresos', 'esperadoPorMetodo', 'totalEsperado', 'totalContado'
        );
    }

    /**
     * Esperado por método de pago = ventas de contado completadas + abonos cobrados
     * + ingresos - gastos, todo filtrado por el día de la caja. El método "Efectivo"
     * además suma el fondo inicial.
     */
    private function calcularEsperadoPorMetodo(Caja $caja)
    {
        $fecha = $caja->fecha->format('Y-m-d');

        $ventas = Venta::selectRaw('metodo_pago_id, SUM(total) as monto')
            ->where('es_credito', false)
            ->where('estado', 'completada')
            ->whereDate('fecha_venta', $fecha)
            ->groupBy('metodo_pago_id')
            ->pluck('monto', 'metodo_pago_id');

        $abonos = Abono::selectRaw('metodo_pago_id, SUM(monto) as monto')
            ->whereDate('fecha_abono', $fecha)
            ->groupBy('metodo_pago_id')
            ->pluck('monto', 'metodo_pago_id');

        $ingresos = Ingreso::selectRaw('metodo_pago_id, SUM(monto) as monto')
            ->whereDate('fecha_ingreso', $fecha)
            ->groupBy('metodo_pago_id')
            ->pluck('monto', 'metodo_pago_id');

        $gastos = Gasto::selectRaw('metodo_pago_id, SUM(monto) as monto')
            ->whereDate('fecha_gasto', $fecha)
            ->groupBy('metodo_pago_id')
            ->pluck('monto', 'metodo_pago_id');

        return MetodoPago::where('activo', true)->orderBy('nombre')->get()
            ->map(function ($metodo) use ($ventas, $abonos, $ingresos, $gastos, $caja) {
                $esperado = (float) ($ventas[$metodo->id] ?? 0)
                    + (float) ($abonos[$metodo->id] ?? 0)
                    + (float) ($ingresos[$metodo->id] ?? 0)
                    - (float) ($gastos[$metodo->id] ?? 0);

                if (strtolower(trim($metodo->nombre)) === 'efectivo') {
                    $esperado += (float) $caja->monto_inicial;
                }

                return [
                    'metodo_pago_id' => $metodo->id,
                    'nombre'         => $metodo->nombre,
                    'esperado'       => round($esperado, 2),
                ];
            });
    }
}
