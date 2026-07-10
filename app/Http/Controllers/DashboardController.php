<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Reparacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonth()->startOfMonth();
        $finMesAnterior = Carbon::now()->subMonth()->endOfMonth();

        // ── KPIs principales ─────────────────────────────────────────────
        $ventasHoy        = Venta::whereDate('fecha_venta', $hoy)->where('estado', 'completada')->sum('total');
        $ventasMes        = Venta::where('fecha_venta', '>=', $inicioMes)->where('estado', 'completada')->sum('total');
        $ventasMesAnterior = Venta::whereBetween('fecha_venta', [$inicioMesAnterior, $finMesAnterior])->where('estado', 'completada')->sum('total');
        $crecimientoVentas = $ventasMesAnterior > 0 ? (($ventasMes - $ventasMesAnterior) / $ventasMesAnterior) * 100 : 0;

        $totalClientes    = Cliente::where('activo', true)->count();
        $clientesNuevosMes = Cliente::where('created_at', '>=', $inicioMes)->count();

        $totalProductos   = Producto::where('activo', true)->count();
        $stockBajo        = Producto::where('activo', true)->whereColumn('stock', '<=', 'stock_minimo')->count();

        $reparacionesPendientes = Reparacion::whereNotIn('estado', ['entregado', 'no_reparable'])->count();
        $reparacionesListas    = Reparacion::where('estado', 'listo')->count();

        $carteraPendiente     = Venta::where('es_credito', true)->where('saldo_pendiente', '>', 0)->sum('saldo_pendiente');
        $carteraAtrasadaCount = Venta::where('es_credito', true)->where('saldo_pendiente', '>', 0)
            ->whereDate('fecha_vencimiento', '<', $hoy)->count();

        // ── Gráfica de ventas por día (últimos 7 días) ────────────────────
        $ventasSemana = Venta::select(
                DB::raw('DATE(fecha_venta) as fecha'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('fecha_venta', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->where('estado', 'completada')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $diasSemana = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::now()->subDays($i)->format('Y-m-d');
            $venta = $ventasSemana->firstWhere('fecha', $dia);
            $diasSemana->push([
                'fecha'    => Carbon::now()->subDays($i)->isoFormat('ddd D'),
                'total'    => $venta ? (float) $venta->total : 0,
                'cantidad' => $venta ? (int) $venta->cantidad : 0,
            ]);
        }

        // ── Ventas por mes (últimos 6 meses) ─────────────────────────────
        $ventasPorMes = Venta::select(
                DB::raw('YEAR(fecha_venta) as año'),
                DB::raw('MONTH(fecha_venta) as mes'),
                DB::raw('SUM(total) as total')
            )
            ->where('fecha_venta', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->where('estado', 'completada')
            ->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get()
            ->map(fn($v) => [
                'mes'   => Carbon::createFromDate($v->año, $v->mes, 1)->isoFormat('MMM YY'),
                'total' => (float) $v->total,
            ]);

        // ── Top 5 productos más vendidos ──────────────────────────────────
        $topProductos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.estado', 'completada')
            ->where('ventas.fecha_venta', '>=', $inicioMes)
            ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'), DB::raw('SUM(detalle_ventas.subtotal) as ingresos'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // ── Últimas ventas ────────────────────────────────────────────────
        $ultimasVentas = Venta::with(['cliente', 'vendedor'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // ── Reparaciones recientes ────────────────────────────────────────
        $ultimasReparaciones = Reparacion::with(['cliente', 'tecnico'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'ventasHoy', 'ventasMes', 'crecimientoVentas',
            'totalClientes', 'clientesNuevosMes',
            'totalProductos', 'stockBajo',
            'reparacionesPendientes', 'reparacionesListas',
            'carteraPendiente', 'carteraAtrasadaCount',
            'diasSemana', 'ventasPorMes', 'topProductos',
            'ultimasVentas', 'ultimasReparaciones'
        ));
    }
}
