<?php

namespace App\Http\Controllers;

use App\Exports\ProductosExport;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Condicion;
use App\Models\Almacenamiento;
use App\Models\Ram;
use App\Models\Color;
use App\Models\CatalogoTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductoController extends Controller
{
    protected function queryFiltrada(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'condicion']);

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('codigo', 'like', "%{$request->buscar}%")
                  ->orWhere('modelo', 'like', "%{$request->buscar}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        if ($request->filled('condicion_id')) {
            $query->where('condicion_id', $request->condicion_id);
        }

        if ($request->filled('stock_bajo') && $request->stock_bajo) {
            $query->whereColumn('stock', '<=', 'stock_minimo');
        }

        return $query;
    }

    public function index(Request $request)
    {
        $productos   = $this->queryFiltrada($request)->orderByDesc('created_at')->paginate(15);
        $categorias  = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas      = Marca::where('activo', true)->orderBy('nombre')->get();
        $condiciones = Condicion::where('activo', true)->orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'marcas', 'condiciones'));
    }

    public function exportarExcel(Request $request)
    {
        // Exporta siempre el inventario completo, sin aplicar los filtros de pantalla.
        $query = Producto::with(['categoria', 'marca', 'condicion'])
            ->orderBy('nombre');

        return Excel::download(
            new ProductosExport($query),
            'inventario_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /** Tipos de catálogo dinámico activos, cada uno con sus valores activos — para los <select multiple> de Productos. */
    private function catalogoTiposActivos()
    {
        return CatalogoTipo::where('activo', true)
            ->with(['valores' => fn($q) => $q->where('activo', true)->orderBy('nombre')])
            ->orderBy('nombre')
            ->get()
            ->filter(fn($tipo) => $tipo->valores->isNotEmpty())
            ->values();
    }

    /** Valores activos del catálogo dinámico "Proveedores" — para el <select> de proveedor en los lotes de inventario. */
    private function proveedoresActivos()
    {
        return CatalogoTipo::where('nombre', 'Proveedores')
            ->with(['valores' => fn($q) => $q->where('activo', true)->orderBy('nombre')])
            ->first()
            ?->valores ?? collect();
    }

    public function create()
    {
        $categorias      = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas          = Marca::where('activo', true)->orderBy('nombre')->get();
        $condiciones     = Condicion::where('activo', true)->orderBy('nombre')->get();
        $almacenamientos = Almacenamiento::where('activo', true)->orderBy('nombre')->get();
        $rams            = Ram::where('activo', true)->orderBy('nombre')->get();
        $colores         = Color::where('activo', true)->orderBy('nombre')->get();
        $catalogoTipos   = $this->catalogoTiposActivos();
        $proveedores     = $this->proveedoresActivos();
        return view('productos.create', compact('categorias', 'marcas', 'condiciones', 'almacenamientos', 'rams', 'colores', 'catalogoTipos', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo'        => 'required|string|unique:productos,codigo|max:50',
            'nombre'        => 'required|string|max:150',
            'descripcion'   => 'nullable|string',
            'categoria_id'  => 'required|exists:categorias,id',
            'marca_id'      => 'required|exists:marcas,id',
            'modelo'        => 'nullable|string|max:100',
            'precio_venta'  => 'required|numeric|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'requiere_imei'   => 'boolean',
            'requiere_serial' => 'boolean',
            'condicion_id'  => 'required|exists:condiciones,id',
            'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'catalogo_valores'   => 'nullable|array',
            'catalogo_valores.*' => 'array',
            'catalogo_valores.*.*' => 'exists:catalogo_valores,id',
            'lotes'                             => 'required|array|min:1',
            'lotes.*.costo_unitario'            => 'required|numeric|min:0',
            'lotes.*.proveedor'                 => 'nullable|string|max:150',
            'lotes.*.variantes'                 => 'required|array|min:1',
            'lotes.*.variantes.*.cantidad'          => 'required|integer|min:1',
            'lotes.*.variantes.*.color_id'          => 'nullable|exists:colores,id',
            'lotes.*.variantes.*.almacenamiento_id' => 'nullable|exists:almacenamientos,id',
            'lotes.*.variantes.*.ram_id'            => 'nullable|exists:rams,id',
        ]);

        $validated['requiere_imei']   = $request->boolean('requiere_imei');
        $validated['requiere_serial'] = $request->boolean('requiere_serial');

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create(
            collect($validated)->except(['catalogo_valores', 'lotes'])->all()
        );

        $idsCatalogoValores = collect($request->input('catalogo_valores', []))->flatten()->unique()->values()->all();
        $producto->catalogoValores()->sync($idsCatalogoValores);

        foreach ($validated['lotes'] as $lote) {
            $producto->agregarLote([
                'costo_unitario' => $lote['costo_unitario'],
                'proveedor'      => $lote['proveedor'] ?? null,
                'user_id'        => auth()->id(),
                'variantes'      => array_map(fn ($v) => [
                    'cantidad'          => $v['cantidad'],
                    'color_id'          => $v['color_id'] ?? null,
                    'almacenamiento_id' => $v['almacenamiento_id'] ?? null,
                    'ram_id'            => $v['ram_id'] ?? null,
                ], $lote['variantes']),
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function agregarLote(Request $request, Producto $producto)
    {
        $validado = $request->validate([
            'costo_unitario' => 'required|numeric|min:0',
            'proveedor'      => 'nullable|string|max:150',
            'notas'          => 'nullable|string|max:500',
            'variantes'                 => 'required|array|min:1',
            'variantes.*.cantidad'          => 'required|integer|min:1',
            'variantes.*.color_id'          => 'nullable|exists:colores,id',
            'variantes.*.almacenamiento_id' => 'nullable|exists:almacenamientos,id',
            'variantes.*.ram_id'            => 'nullable|exists:rams,id',
        ]);

        $producto->agregarLote([
            'costo_unitario' => $validado['costo_unitario'],
            'proveedor'      => $validado['proveedor'] ?? null,
            'notas'          => $validado['notas'] ?? null,
            'user_id'        => auth()->id(),
            'variantes'      => array_map(fn ($v) => [
                'cantidad'          => $v['cantidad'],
                'color_id'          => $v['color_id'] ?? null,
                'almacenamiento_id' => $v['almacenamiento_id'] ?? null,
                'ram_id'            => $v['ram_id'] ?? null,
            ], $validado['variantes']),
        ]);

        return back()->with('success', 'Lote agregado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load([
            'categoria', 'marca', 'condicion',
            'detalleVentas.venta.cliente', 'catalogoValores.tipo',
            'lotes' => fn ($q) => $q->orderByDesc('fecha_ingreso')->orderByDesc('id'),
            'lotes.variantes.color', 'lotes.variantes.almacenamiento', 'lotes.variantes.ram',
        ]);
        $proveedores     = $this->proveedoresActivos();
        $colores         = Color::where('activo', true)->orderBy('nombre')->get();
        $almacenamientos = Almacenamiento::where('activo', true)->orderBy('nombre')->get();
        $rams            = Ram::where('activo', true)->orderBy('nombre')->get();
        return view('productos.show', compact('producto', 'proveedores', 'colores', 'almacenamientos', 'rams'));
    }

    public function edit(Producto $producto)
    {
        $categorias      = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas          = Marca::where('activo', true)->orderBy('nombre')->get();
        $condiciones     = Condicion::where('activo', true)->orderBy('nombre')->get();
        $catalogoTipos   = $this->catalogoTiposActivos();
        $producto->load('catalogoValores');
        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'condiciones', 'catalogoTipos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo'        => 'required|string|unique:productos,codigo,' . $producto->id . '|max:50',
            'nombre'        => 'required|string|max:150',
            'descripcion'   => 'nullable|string',
            'categoria_id'  => 'required|exists:categorias,id',
            'marca_id'      => 'required|exists:marcas,id',
            'modelo'        => 'nullable|string|max:100',
            'precio_venta'  => 'required|numeric|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'requiere_imei'   => 'boolean',
            'requiere_serial' => 'boolean',
            'condicion_id'  => 'required|exists:condiciones,id',
            'activo'        => 'boolean',
            'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'catalogo_valores'   => 'nullable|array',
            'catalogo_valores.*' => 'array',
            'catalogo_valores.*.*' => 'exists:catalogo_valores,id',
        ]);

        $validated['requiere_imei']   = $request->boolean('requiere_imei');
        $validated['requiere_serial'] = $request->boolean('requiere_serial');

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update(collect($validated)->except('catalogo_valores')->all());

        $idsCatalogoValores = collect($request->input('catalogo_valores', []))->flatten()->unique()->values()->all();
        $producto->catalogoValores()->sync($idsCatalogoValores);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->detalleVentas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: el producto tiene ventas registradas.');
        }

        if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
