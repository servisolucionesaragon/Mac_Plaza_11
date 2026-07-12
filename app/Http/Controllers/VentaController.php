<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Abono;
use App\Models\Caja;
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

        if ($request->filled('tipo_venta')) {
            if ($request->tipo_venta === 'credito') {
                $query->where('es_credito', true);
            } elseif ($request->tipo_venta === 'contado') {
                $query->where('es_credito', false);
            }
        }

        $ventas = $query->orderByDesc('fecha_venta')->paginate(15);

        $totalMes = Venta::where('estado', 'completada')
            ->where('fecha_venta', '>=', Carbon::now()->startOfMonth())
            ->sum('total');

        $cajaAbierta = (bool) Caja::abiertaActual();

        return view('ventas.index', compact('ventas', 'totalMes', 'cajaAbierta'));
    }

    public function create()
    {
        if (!Caja::abiertaActual()) {
            return redirect()->route('ventas.index')->with('error', 'Debes abrir la caja del día antes de registrar ventas.');
        }

        return view('ventas.create', $this->datosFormulario());
    }

    /**
     * Datos comunes para los formularios de crear/editar venta.
     * Si se pasa $venta, ajusta el catálogo de productos para incluir los que ya
     * están en su detalle (aunque estén inactivos/sin stock) y les "devuelve" la
     * cantidad ya reservada por esta misma venta, para que el formulario de edición
     * no rechace las cantidades ya vendidas.
     */
    private function datosFormulario(?Venta $venta = null): array
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();

        $cantidadesEnVenta = collect();
        if ($venta) {
            $venta->loadMissing('detalles');
            $cantidadesEnVenta = $venta->detalles->pluck('cantidad', 'producto_id');
        }

        $productosQuery = Producto::with(['categoria', 'marca', 'almacenamiento', 'ram', 'condicion']);
        if ($cantidadesEnVenta->isNotEmpty()) {
            $productosQuery->where(function ($q) use ($cantidadesEnVenta) {
                $q->where(fn($q2) => $q2->where('activo', true)->where('stock', '>', 0))
                  ->orWhereIn('id', $cantidadesEnVenta->keys());
            });
        } else {
            $productosQuery->where('activo', true)->where('stock', '>', 0);
        }
        $productos = $productosQuery->orderBy('nombre')->get();

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
                'cumple_mes'     => $c->cumpleAnioEsteMes(),
                'es_distribuidor' => (bool) $c->es_distribuidor,
            ];
        })->values();

        $productosJson = $productos->map(function ($p) use ($cantidadesEnVenta) {
            return [
                'id'                    => $p->id,
                'nombre'                => $p->nombre,
                'codigo'                => $p->codigo,
                'precio_venta'          => (float) $p->precio_venta,
                'stock'                 => $p->stock + ($cantidadesEnVenta[$p->id] ?? 0),
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

        return compact(
            'clientes', 'productos', 'metodosPago',
            'categorias', 'marcas', 'almacenamientos', 'rams', 'condiciones', 'colores',
            'clientesJson', 'productosJson'
        );
    }

    /**
     * Valida y arma los detalles de una venta a partir del array `productos` del
     * request, decrementando el stock de cada producto. Usado por store() y
     * update() (en update(), llamar solo después de restaurar el stock viejo).
     */
    private function procesarProductos(array $productosRequest): array
    {
        $subtotal = 0;
        $detalles = [];

        foreach ($productosRequest as $item) {
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

            $producto->decrement('stock', $item['cantidad']);
        }

        return [$detalles, $subtotal];
    }

    /** Calcula subtotal neto/impuesto/total según el modo de precio. */
    private function calcularMontos(float $subtotal, float $descuento, string $modoPrecio, float $igvConfig): array
    {
        $baseConDescuento = $subtotal - $descuento;

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

        return [$subtotalNeto, $impuesto, $total];
    }

    /** % de descuento configurado, aplicado sobre el subtotal si el cliente es distribuidor. */
    private function descuentoDistribuidor(float $subtotal, int $clienteId): float
    {
        $esDistribuidor = Cliente::where('id', $clienteId)->value('es_distribuidor');
        if (!$esDistribuidor) {
            return 0;
        }
        $porcentaje = \App\Models\Configuracion::actual()->descuento_distribuidor;
        return round($subtotal * ((float)$porcentaje / 100), 2);
    }

    private function reglasValidacion(): array
    {
        return [
            'cliente_id'          => 'required|exists:clientes,id',
            'metodo_pago_id'      => 'required|exists:metodos_pago,id',
            'productos'           => 'required|array|min:1',
            'productos.*.id'      => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'descuento_general'   => 'nullable|numeric|min:0',
            'modo_precio'         => 'nullable|in:incluido,sin_impuesto,subtotal_impuesto',
            'notas'               => 'nullable|string',
            'es_credito'          => 'nullable|boolean',
            'fecha_vencimiento'   => 'required_if:es_credito,1|nullable|date',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->reglasValidacion() + [
            'abono_inicial' => 'nullable|numeric|min:0',
        ]);

        if (!Caja::abiertaActual()) {
            return back()->with('error', 'Debes abrir la caja del día antes de registrar ventas.')->withInput();
        }

        DB::beginTransaction();
        try {
            [$detalles, $subtotal] = $this->procesarProductos($request->productos);

            $descuento   = (float)($request->descuento_general ?? 0)
                + $this->descuentoDistribuidor($subtotal, $request->cliente_id);
            $igvConfig   = \App\Models\Configuracion::actual()->igv;
            $modoPrecio  = $request->input('modo_precio', 'subtotal_impuesto');
            [$subtotalNeto, $impuesto, $total] = $this->calcularMontos($subtotal, $descuento, $modoPrecio, $igvConfig);

            $esCredito    = $request->boolean('es_credito');
            $abonoInicial = $esCredito ? (float)($request->abono_inicial ?? 0) : 0;

            if ($abonoInicial > $total) {
                throw new \Exception('El abono inicial no puede ser mayor al total de la venta.');
            }

            $saldoPendiente = $esCredito ? ($total - $abonoInicial) : 0;
            // Una venta a crédito no se considera "completada" hasta saldar el 100% del crédito.
            $estadoInicial = ($esCredito && $saldoPendiente > 0) ? 'pendiente' : 'completada';

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
                'estado'       => $estadoInicial,
                'notas'        => $request->notas,
                'es_credito'        => $esCredito,
                'saldo_pendiente'   => $saldoPendiente,
                'fecha_vencimiento' => $esCredito ? $request->fecha_vencimiento : null,
            ]);

            foreach ($detalles as $detalle) {
                $detalle['venta_id'] = $venta->id;
                DetalleVenta::create($detalle);
            }

            if ($esCredito && $abonoInicial > 0) {
                Abono::create([
                    'venta_id'       => $venta->id,
                    'monto'          => $abonoInicial,
                    'fecha_abono'    => now(),
                    'metodo_pago_id' => $request->metodo_pago_id,
                    'user_id'        => Auth::id(),
                    'notas'          => 'Abono inicial al registrar la venta.',
                ]);
            }

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', "Venta {$venta->numero_venta} registrada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Venta $venta)
    {
        abort_unless(Auth::user()->esAdmin(), 403);
        abort_if(in_array($venta->estado, ['cancelada', 'devuelta']), 403, 'No se puede editar una venta cancelada o devuelta.');

        $venta->load('detalles');

        return view('ventas.edit', $this->datosFormulario($venta) + ['venta' => $venta]);
    }

    public function update(Request $request, Venta $venta)
    {
        abort_unless(Auth::user()->esAdmin(), 403);
        abort_if(in_array($venta->estado, ['cancelada', 'devuelta']), 403, 'No se puede editar una venta cancelada o devuelta.');

        $request->validate($this->reglasValidacion());

        DB::beginTransaction();
        try {
            // Restaurar el stock de los productos/cantidades actuales antes de validar los nuevos.
            $venta->load('detalles');
            foreach ($venta->detalles as $detalleViejo) {
                $detalleViejo->producto?->increment('stock', $detalleViejo->cantidad);
            }

            [$detalles, $subtotal] = $this->procesarProductos($request->productos);

            $descuento   = (float)($request->descuento_general ?? 0)
                + $this->descuentoDistribuidor($subtotal, $request->cliente_id);
            $igvConfig   = \App\Models\Configuracion::actual()->igv;
            $modoPrecio  = $request->input('modo_precio', 'subtotal_impuesto');
            [$subtotalNeto, $impuesto, $total] = $this->calcularMontos($subtotal, $descuento, $modoPrecio, $igvConfig);

            $esCredito    = $request->boolean('es_credito');
            $totalAbonado = $venta->abonos()->sum('monto');
            $saldoPendiente = $esCredito ? max($total - $totalAbonado, 0) : 0;
            $estado = ($esCredito && $saldoPendiente > 0) ? 'pendiente' : 'completada';

            $venta->update([
                'cliente_id'   => $request->cliente_id,
                'subtotal'     => $subtotalNeto,
                'descuento'    => $descuento,
                'impuesto'     => $impuesto,
                'total'        => $total,
                'modo_precio'  => $modoPrecio,
                'metodo_pago_id' => $request->metodo_pago_id,
                'estado'       => $estado,
                'notas'        => $request->notas,
                'es_credito'        => $esCredito,
                'saldo_pendiente'   => $saldoPendiente,
                'fecha_vencimiento' => $esCredito ? $request->fecha_vencimiento : null,
            ]);

            $venta->detalles()->delete();
            foreach ($detalles as $detalle) {
                $detalle['venta_id'] = $venta->id;
                DetalleVenta::create($detalle);
            }

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', "Venta {$venta->numero_venta} actualizada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'vendedor', 'detalles.producto.marca', 'metodoPago', 'abonos.metodoPago', 'abonos.usuario']);
        return view('ventas.show', compact('venta'));
    }

    public function registrarAbono(Request $request, Venta $venta)
    {
        if (!$venta->es_credito || $venta->saldo_pendiente <= 0) {
            return back()->with('error', 'Esta venta no tiene saldo pendiente por cobrar.');
        }

        if (!Caja::abiertaActual()) {
            return back()->with('error', 'Debes abrir la caja del día antes de registrar un abono.');
        }

        $request->validate([
            'monto'          => 'required|numeric|min:0.01',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'notas'          => 'nullable|string',
        ]);

        if ($request->monto > $venta->saldo_pendiente) {
            return back()->with('error', 'El monto del abono no puede ser mayor al saldo pendiente ('
                . $venta->saldo_pendiente . ').')->withInput();
        }

        $abono = DB::transaction(function () use ($request, $venta) {
            $abono = Abono::create([
                'venta_id'       => $venta->id,
                'monto'          => $request->monto,
                'fecha_abono'    => now(),
                'metodo_pago_id' => $request->metodo_pago_id,
                'user_id'        => Auth::id(),
                'notas'          => $request->notas,
            ]);
            $venta->decrement('saldo_pendiente', $request->monto);
            $venta->refresh();
            // El crédito recién se considera "completada" cuando el saldo llega a 0.
            if ($venta->saldo_pendiente <= 0 && $venta->estado === 'pendiente') {
                $venta->update(['estado' => 'completada']);
            }
            return $abono;
        });

        return redirect()->route('ventas.abonos.recibo', [$venta, $abono])
            ->with('success', 'Abono registrado correctamente.');
    }

    public function recibo(Venta $venta)
    {
        $venta->load(['cliente', 'vendedor', 'detalles.producto.marca', 'metodoPago']);
        return view('ventas.recibo', compact('venta'));
    }

    public function reciboAbono(Venta $venta, Abono $abono)
    {
        abort_if($abono->venta_id !== $venta->id, 404);
        $venta->load('cliente');
        $abono->load('metodoPago', 'usuario');
        return view('ventas.abono-recibo', compact('venta', 'abono'));
    }

    /** Recibo de venta accesible sin login, vía link firmado (para compartir por WhatsApp). */
    public function reciboPublico(Venta $venta)
    {
        $venta->load(['cliente', 'vendedor', 'detalles.producto.marca', 'metodoPago']);
        $layout = 'layouts.publico';
        $publico = true;
        return view('ventas.recibo', compact('venta', 'layout', 'publico'));
    }

    /** Recibo de abono accesible sin login, vía link firmado (para compartir por WhatsApp). */
    public function abonoReciboPublico(Venta $venta, Abono $abono)
    {
        abort_if($abono->venta_id !== $venta->id, 404);
        $venta->load('cliente');
        $abono->load('metodoPago', 'usuario');
        $layout = 'layouts.publico';
        $publico = true;
        return view('ventas.abono-recibo', compact('venta', 'abono', 'layout', 'publico'));
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
