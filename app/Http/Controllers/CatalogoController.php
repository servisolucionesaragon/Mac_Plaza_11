<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Condicion;
use App\Models\Almacenamiento;
use App\Models\Ram;
use App\Models\MetodoPago;
use App\Models\CatalogoTipo;

class CatalogoController extends Controller
{
    protected array $modelos = [
        'categorias'      => Categoria::class,
        'marcas'          => Marca::class,
        'condiciones'     => Condicion::class,
        'almacenamientos' => Almacenamiento::class,
        'rams'            => Ram::class,
        'metodos_pago'    => MetodoPago::class,
    ];

    protected array $singular = [
        'categorias'      => 'Categoría',
        'marcas'          => 'Marca',
        'condiciones'     => 'Condición',
        'almacenamientos' => 'Almacenamiento',
        'rams'            => 'RAM',
        'metodos_pago'    => 'Método de pago',
    ];

    public function index()
    {
        $categorias      = Categoria::orderBy('nombre')->get();
        $marcas          = Marca::orderBy('nombre')->get();
        $condiciones     = Condicion::orderBy('nombre')->get();
        $almacenamientos = Almacenamiento::orderBy('nombre')->get();
        $rams            = Ram::orderBy('nombre')->get();
        $metodosPago     = MetodoPago::orderBy('nombre')->get();
        $catalogoTipos   = CatalogoTipo::with('valores')->orderBy('nombre')->get();

        return view('catalogos.index', compact('categorias', 'marcas', 'condiciones', 'almacenamientos', 'rams', 'metodosPago', 'catalogoTipos'));
    }

    public function store(Request $request, string $tipo)
    {
        $modelo = $this->resolverModelo($tipo);
        $tabla = (new $modelo)->getTable();

        $validated = $request->validate([
            'nombre' => "required|string|max:100|unique:{$tabla},nombre",
        ]);

        $modelo::create(['nombre' => $validated['nombre'], 'activo' => true]);

        return back()->with('success', $this->singular[$tipo] . ' creada correctamente.');
    }

    public function update(Request $request, string $tipo, int $id)
    {
        $modelo = $this->resolverModelo($tipo);
        $registro = $modelo::findOrFail($id);
        $tabla = $registro->getTable();

        $validated = $request->validate([
            'nombre' => "required|string|max:100|unique:{$tabla},nombre,{$id}",
        ]);

        $registro->update(['nombre' => $validated['nombre']]);

        return back()->with('success', $this->singular[$tipo] . ' actualizada correctamente.');
    }

    public function toggle(string $tipo, int $id)
    {
        $modelo = $this->resolverModelo($tipo);
        $registro = $modelo::findOrFail($id);
        $registro->update(['activo' => !$registro->activo]);
        $estado = $registro->activo ? 'activada' : 'desactivada';

        return back()->with('success', $this->singular[$tipo] . " {$estado} correctamente.");
    }

    public function destroy(string $tipo, int $id)
    {
        $modelo = $this->resolverModelo($tipo);
        $registro = $modelo::findOrFail($id);

        $relacion = method_exists($registro, 'productos') ? 'productos' : 'ventas';

        if ($registro->{$relacion}()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay ' . ($relacion === 'productos' ? 'productos' : 'ventas') . ' usando este valor. Desactívalo en su lugar.');
        }

        $registro->delete();

        return back()->with('success', $this->singular[$tipo] . ' eliminada correctamente.');
    }

    protected function resolverModelo(string $tipo): string
    {
        abort_unless(array_key_exists($tipo, $this->modelos), 404);
        return $this->modelos[$tipo];
    }
}
