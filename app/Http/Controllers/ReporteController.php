<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Reparacion;
use App\Models\DetalleVenta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->filled('desde')
            ? Carbon::parse($request->desde)->startOfDay()
            : Carbon::now()->startOfMonth();

        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->hasta)->endOfDay()
            : Carbon::now()->endOfDay();

        // ── Resumen general ───────────────────────────────────────────────
        $totalVentas      = Venta::whereBetween('fecha_venta', [$desde, $hasta])->where('estado', 'completada')->sum('total');
        $cantidadVentas   = Venta::whereBetween('fecha_venta', [$desde, $hasta])->where('estado', 'completada')->count();
        $ticketPromedio   = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;
        $totalReparaciones = Reparacion::whereBetween('fecha_recepcion', [$desde, $hasta])->sum('costo_final');
        $clientesNuevos   = Cliente::whereBetween('created_at', [$desde, $hasta])->count();

        // ── Ventas por día ────────────────────────────────────────────────
        $ventasPorDia = Venta::select(
                DB::raw('DATE(fecha_venta) as fecha'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->whereBetween('fecha_venta', [$desde, $hasta])
            ->where('estado', 'completada')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // ── Ventas por método de pago ──────────────────────────────────────
        $ventasPorPago = Venta::select('metodo_pago_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as monto'))
            ->whereBetween('fecha_venta', [$desde, $hasta])
            ->where('estado', 'completada')
            ->groupBy('metodo_pago_id')
            ->with('metodoPago')
            ->get();

        // ── Top 10 productos más vendidos ─────────────────────────────────
        $topProductos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.estado', 'completada')
            ->whereBetween('ventas.fecha_venta', [$desde, $hasta])
            ->select(
                'productos.nombre',
                'productos.codigo',
                DB::raw('SUM(detalle_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalle_ventas.subtotal) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
            ->orderByDesc('ingresos')
            ->limit(10)
            ->get();

        // ── Top 10 clientes ───────────────────────────────────────────────
        $topClientes = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->where('ventas.estado', 'completada')
            ->whereBetween('ventas.fecha_venta', [$desde, $hasta])
            ->select(
                'clientes.id',
                DB::raw("CONCAT(clientes.nombre,' ',clientes.apellido) as nombre"),
                DB::raw('COUNT(ventas.id) as compras'),
                DB::raw('SUM(ventas.total) as total')
            )
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.apellido')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── Reparaciones por estado ───────────────────────────────────────
        $repPorEstado = Reparacion::select('estado', DB::raw('COUNT(*) as total'))
            ->whereBetween('fecha_recepcion', [$desde, $hasta])
            ->groupBy('estado')
            ->get();

        // ── Productos con stock bajo ───────────────────────────────────────
        $stockBajo = Producto::with(['categoria', 'marca'])
            ->where('activo', true)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->get();

        return view('reportes.index', compact(
            'totalVentas', 'cantidadVentas', 'ticketPromedio',
            'totalReparaciones', 'clientesNuevos',
            'ventasPorDia', 'ventasPorPago',
            'topProductos', 'topClientes',
            'repPorEstado', 'stockBajo',
            'desde', 'hasta'
        ));
    }
}
