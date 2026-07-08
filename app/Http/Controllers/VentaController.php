<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\MetodoPago;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Almacenamiento;
use App\Models\Ram;
use App\Models\Condicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'vendedor', 'metodoPago']);

        if ($request->filled('buscar')) {
            $query->where('numero_venta', 'like', "%{$request->buscar}%")
                  ->orWhereHas('cliente', fn($q) =>
                      $q->where('nombre', 'like', "%{$request->buscar}%")
                        ->orWhere('apellido', 'like', "%{$request->buscar}%")
                  );
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_venta', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_venta', '<=', $request->fecha_hasta);
        }

        $ventas = $query->orderByDesc('fecha_venta')->paginate(15);

        $totalMes = Venta::where('estado', 'completada')
            ->where('fecha_venta', '>=', Carbon::now()->startOfMonth())
            ->sum('total');

        return view('ventas.index', compact('ventas', 'totalMes'));
    }

    public function create()
    {
        $clientes  = Cliente::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::with(['categoria', 'marca', 'almacenamiento', 'ram', 'condicion'])
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();
        $metodosPago = MetodoPago::where('activo', true)->orderBy('nombre')->get();

        $categorias      = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas          = Marca::where('activo', true)->orderBy('nombre')->get();
        $almacenamientos = Almacenamiento::where('activo', true)->orderBy('nombre')->get();
        $rams            = Ram::where('activo', true)->orderBy('nombre')->get();
        $condiciones     = Condicion::where('activo', true)->orderBy('nombre')->get();
        $colores         = Producto::where('activo', true)
            ->whereNotNull('color')->where('color', '!=', '')
            ->distinct()->orderBy('color')->pluck('color');

        $clientesJson = $clientes->map(function ($c) {
            return [
                'id'             => $c->id,
                'nombre'         => $c->nombre_completo,
                'dni'            => $c->dni,
                'tipo_documento' => $c->tipo_documento,
                'telefono'       => $c->telefono,
            ];
        })->values();

        $productosJson = $productos->map(function ($p) {
            return [
                'id'                    => $p->id,
                'nombre'                => $p->nombre,
                'codigo'                => $p->codigo,
                'precio_venta'          => (float) $p->precio_venta,
                'stock'                 => $p->stock,
                'categoria_id'          => $p->categoria_id,
                'categoria_nombre'      => $p->categoria->nombre ?? null,
                'marca_id'              => $p->marca_id,
                'marca_nombre'          => $p->marca->nombre ?? null,
                'color'                 => $p->color,
                'almacenamiento_id'     => $p->almacenamiento_id,
                'almacenamiento_nombre' => $p->almacenamiento->nombre ?? null,
                'ram_id'                => $p->ram_id,
                'ram_nombre'            => $p->ram->nombre ?? null,
                'condicion_id'          => $p->condicion_id,
                'condicion_nombre'      => $p->condicion->nombre ?? null,
                'requiere_imei'         => (bool) $p->requiere_imei,
                'requiere_serial'       => (bool) $p->requiere_serial,
            ];
        })->values();

        return view('ventas.create', compact(
            'clientes', 'productos', 'metodosPago',
            'categorias', 'marcas', 'almacenamientos', 'rams', 'condiciones', 'colores',
            'clientesJson', 'productosJson'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'          => 'required|exists:clientes,id',
            'metodo_pago_id'      => 'required|exists:metodos_pago,id',
            'productos'           => 'required|array|min:1',
            'productos.*.id'      => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'descuento_general'   => 'nullable|numeric|min:0',
            'modo_precio'         => 'nullable|in:incluido,sin_impuesto,subtotal_impuesto',
            'notas'               => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $detalles = [];

            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id']);

                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                }

                if ($producto->requiere_imei && empty($item['imei'] ?? null)) {
                    throw new \Exception("El producto \"{$producto->nombre}\" requiere IMEI para completar la venta.");
                }

                if ($producto->requiere_serial && empty($item['serial'] ?? null)) {
                    throw new \Exception("El producto \"{$producto->nombre}\" requiere Serial para completar la venta.");
                }

                $precioUnitario = $producto->precio_venta;
                $descItem       = isset($item['descuento']) ? (float)$item['descuento'] : 0;
                $subItem        = ($precioUnitario * $item['cantidad']) - $descItem;
                $subtotal      += $subItem;

                $detalles[] = [
                    'producto_id'    => $producto->id,
                    'cantidad'       => $item['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'descuento'      => $descItem,
                    'subtotal'       => $subItem,
                    'imei_vendido'   => $item['imei'] ?? null,
                    'serial_vendido' => $item['serial'] ?? null,
                ];

                // Reducir stock
                $producto->decrement('stock', $item['cantidad']);
            }

            $descuento        = (float)($request->descuento_general ?? 0);
            $baseConDescuento = $subtotal - $descuento;
            $igvConfig        = \App\Models\Configuracion::actual()->igv;
            $modoPrecio       = $request->input('modo_precio', 'subtotal_impuesto');

            if ($modoPrecio === 'incluido') {
                $total        = $baseConDescuento;
                $subtotalNeto = round($total / (1 + $igvConfig / 100), 2);
                $impuesto     = round($total - $subtotalNeto, 2);
            } elseif ($modoPrecio === 'sin_impuesto') {
                $subtotalNeto = $baseConDescuento;
                $impuesto     = 0;
                $total        = $subtotalNeto;
            } else {
                $subtotalNeto = $baseConDescuento;
                $impuesto     = round($subtotalNeto * ($igvConfig / 100), 2);
                $total        = $subtotalNeto + $impuesto;
            }

            $venta = Venta::create([
                'numero_venta' => Venta::generarNumero(),
                'cliente_id'   => $request->cliente_id,
                'user_id'      => Auth::id(),
                'fecha_venta'  => now(),
                'subtotal'     => $subtotalNeto,
                'descuento'    => $descuento,
                'impuesto'     => $impuesto,
                'total'        => $total,
                'modo_precio'  => $modoPrecio,
                'metodo_pago_id' => $request->metodo_pago_id,
                'estado'       => 'completada',
                'notas'        => $request->notas,
            ]);

            foreach ($detalles as $detalle) {
                $detalle['venta_id'] = $venta->id;
                DetalleVenta::create($detalle);
            }

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', "Venta {$venta->numero_venta} registrada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'vendedor', 'detalles.producto.marca', 'metodoPago']);
        return view('ventas.show', compact('venta'));
    }

    public function cancelar(Venta $venta)
    {
        if ($venta->estado !== 'completada') {
            return back()->with('error', 'Solo se pueden cancelar ventas completadas.');
        }

        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
            $venta->update(['estado' => 'cancelada']);
        });

        return back()->with('success', 'Venta cancelada y stock restaurado.');
    }
}
