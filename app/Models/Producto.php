<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'categoria_id', 'marca_id',
        'modelo', 'precio_compra',
        'precio_venta', 'stock', 'stock_minimo', 'imagen',
        'requiere_imei', 'requiere_serial',
        'condicion_id', 'activo',
    ];

    protected $casts = [
        'precio_compra'   => 'decimal:2',
        'precio_venta'    => 'decimal:2',
        'activo'          => 'boolean',
        'requiere_imei'   => 'boolean',
        'requiere_serial' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function condicion()
    {
        return $this->belongsTo(Condicion::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function catalogoValores()
    {
        return $this->belongsToMany(CatalogoValor::class, 'producto_catalogo_valor');
    }

    public function lotes()
    {
        return $this->hasMany(LoteProducto::class);
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function getMargenAttribute(): float
    {
        if ($this->precio_compra > 0) {
            return (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
        }
        return 0;
    }

    /**
     * Registra un lote nuevo de stock (compra/reabastecimiento) con su propio costo,
     * conteniendo 1+ variantes (color/almacenamiento/ram + cantidad) — un mismo lote
     * puede mezclar varias combinaciones al mismo costo. Suma la cantidad total al
     * stock del producto y recalcula el costo "de frente" FIFO.
     */
    public function agregarLote(array $datos): LoteProducto
    {
        $lote = $this->lotes()->create([
            'costo_unitario' => $datos['costo_unitario'],
            'proveedor'      => $datos['proveedor'] ?? null,
            'fecha_ingreso'  => $datos['fecha_ingreso'] ?? now(),
            'notas'          => $datos['notas'] ?? null,
            'user_id'        => $datos['user_id'],
        ]);

        $totalCantidad = 0;
        foreach ($datos['variantes'] as $variante) {
            $lote->variantes()->create([
                'color_id'          => $variante['color_id'] ?? null,
                'almacenamiento_id' => $variante['almacenamiento_id'] ?? null,
                'ram_id'            => $variante['ram_id'] ?? null,
                'cantidad_inicial'  => $variante['cantidad'],
                'cantidad_restante' => $variante['cantidad'],
            ]);
            $totalCantidad += $variante['cantidad'];
        }

        $this->increment('stock', $totalCantidad);
        $this->recalcularPrecioCompra();

        return $lote;
    }

    /**
     * Descuenta `$cantidad` unidades de las variantes (color/almacenamiento/ram)
     * que coincidan EXACTO con `$variante`, empezando por el lote más antiguo (FIFO),
     * partiendo entre varias variantes/lotes si hace falta. Devuelve el detalle de
     * qué se consumió de cada variante, para registrar `detalle_venta_lote`.
     * Lanza una excepción si no hay stock suficiente de esa variante específica.
     */
    public function consumirStockFifo(int $cantidad, array $variante = []): Collection
    {
        $colorId          = $variante['color_id'] ?? null;
        $almacenamientoId = $variante['almacenamiento_id'] ?? null;
        $ramId            = $variante['ram_id'] ?? null;

        $porConsumir = $cantidad;
        $consumos = collect();

        $variantesDisponibles = LoteVariante::query()
            ->join('lotes_producto', 'lotes_producto.id', '=', 'lote_variantes.lote_id')
            ->where('lotes_producto.producto_id', $this->id)
            ->where('lote_variantes.cantidad_restante', '>', 0)
            ->when($colorId, fn ($q) => $q->where('lote_variantes.color_id', $colorId), fn ($q) => $q->whereNull('lote_variantes.color_id'))
            ->when($almacenamientoId, fn ($q) => $q->where('lote_variantes.almacenamiento_id', $almacenamientoId), fn ($q) => $q->whereNull('lote_variantes.almacenamiento_id'))
            ->when($ramId, fn ($q) => $q->where('lote_variantes.ram_id', $ramId), fn ($q) => $q->whereNull('lote_variantes.ram_id'))
            ->orderBy('lotes_producto.fecha_ingreso')
            ->orderBy('lote_variantes.id')
            ->select('lote_variantes.*', 'lotes_producto.costo_unitario as lote_costo_unitario')
            ->get();

        foreach ($variantesDisponibles as $lv) {
            if ($porConsumir <= 0) {
                break;
            }

            $tomar = min($lv->cantidad_restante, $porConsumir);
            $lv->decrement('cantidad_restante', $tomar);
            $porConsumir -= $tomar;

            $consumos->push([
                'lote_variante_id' => $lv->id,
                'cantidad'         => $tomar,
                'costo_unitario'   => $lv->lote_costo_unitario,
            ]);
        }

        if ($porConsumir > 0) {
            throw new \Exception("Stock insuficiente para: {$this->nombre}");
        }

        $this->decrement('stock', $cantidad);
        $this->recalcularPrecioCompra();

        return $consumos;
    }

    /**
     * Reversa exacta de consumirStockFifo(): devuelve cada cantidad a su variante de
     * origen (no a "cualquier variante actual"). Usado al editar/cancelar una venta.
     * Recibe una colección de registros con `lote_variante_id` y `cantidad` (ej. las
     * filas de `detalle_venta_lote` de la venta que se está revirtiendo).
     */
    public function devolverStockFifo(iterable $consumos): void
    {
        $totalDevuelto = 0;

        foreach ($consumos as $consumo) {
            $loteVarianteId = is_array($consumo) ? $consumo['lote_variante_id'] : $consumo->lote_variante_id;
            $cant           = is_array($consumo) ? $consumo['cantidad'] : $consumo->cantidad;

            LoteVariante::where('id', $loteVarianteId)->increment('cantidad_restante', $cant);
            $totalDevuelto += $cant;
        }

        if ($totalDevuelto > 0) {
            $this->increment('stock', $totalDevuelto);
            $this->recalcularPrecioCompra();
        }
    }

    /** Costo del lote más antiguo con alguna variante en existencia (el "frente" FIFO) — no cambia si ya no queda stock. */
    private function recalcularPrecioCompra(): void
    {
        $costoFrente = LoteVariante::query()
            ->join('lotes_producto', 'lotes_producto.id', '=', 'lote_variantes.lote_id')
            ->where('lotes_producto.producto_id', $this->id)
            ->where('lote_variantes.cantidad_restante', '>', 0)
            ->orderBy('lotes_producto.fecha_ingreso')
            ->orderBy('lote_variantes.id')
            ->value('lotes_producto.costo_unitario');

        if ($costoFrente !== null) {
            $this->update(['precio_compra' => $costoFrente]);
        }
    }
}
