<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Condicion;
use App\Models\Almacenamiento;
use App\Models\Ram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'condicion', 'almacenamiento', 'ram']);

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

        $productos   = $query->orderByDesc('created_at')->paginate(15);
        $categorias  = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas      = Marca::where('activo', true)->orderBy('nombre')->get();
        $condiciones = Condicion::where('activo', true)->orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'marcas', 'condiciones'));
    }

    public function create()
    {
        $categorias      = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas          = Marca::where('activo', true)->orderBy('nombre')->get();
        $condiciones     = Condicion::where('activo', true)->orderBy('nombre')->get();
        $almacenamientos = Almacenamiento::where('activo', true)->orderBy('nombre')->get();
        $rams            = Ram::where('activo', true)->orderBy('nombre')->get();
        return view('productos.create', compact('categorias', 'marcas', 'condiciones', 'almacenamientos', 'rams'));
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
            'color'         => 'nullable|string|max:50',
            'almacenamiento_id' => 'nullable|exists:almacenamientos,id',
            'ram_id'        => 'nullable|exists:rams,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'requiere_imei'   => 'boolean',
            'requiere_serial' => 'boolean',
            'condicion_id'  => 'required|exists:condiciones,id',
            'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['requiere_imei']   = $request->boolean('requiere_imei');
        $validated['requiere_serial'] = $request->boolean('requiere_serial');

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'marca', 'condicion', 'almacenamiento', 'ram', 'detalleVentas.venta.cliente']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias      = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas          = Marca::where('activo', true)->orderBy('nombre')->get();
        $condiciones     = Condicion::where('activo', true)->orderBy('nombre')->get();
        $almacenamientos = Almacenamiento::where('activo', true)->orderBy('nombre')->get();
        $rams            = Ram::where('activo', true)->orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'condiciones', 'almacenamientos', 'rams'));
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
            'color'         => 'nullable|string|max:50',
            'almacenamiento_id' => 'nullable|exists:almacenamientos,id',
            'ram_id'        => 'nullable|exists:rams,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'requiere_imei'   => 'boolean',
            'requiere_serial' => 'boolean',
            'condicion_id'  => 'required|exists:condiciones,id',
            'activo'        => 'boolean',
            'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['requiere_imei']   = $request->boolean('requiere_imei');
        $validated['requiere_serial'] = $request->boolean('requiere_serial');

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($validated);

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
