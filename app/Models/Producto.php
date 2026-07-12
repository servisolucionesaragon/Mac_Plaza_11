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
        'modelo', 'color', 'almacenamiento_id', 'ram_id', 'precio_compra',
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

    public function almacenamiento()
    {
        return $this->belongsTo(Almacenamiento::class);
    }

    public function ram()
    {
        return $this->belongsTo(Ram::class);
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
     * suma su cantidad al stock total y recalcula el costo "de frente" FIFO.
     */
    public function agregarLote(array $datos): LoteProducto
    {
        $lote = $this->lotes()->create([
            'cantidad_inicial'  => $datos['cantidad'],
            'cantidad_restante' => $datos['cantidad'],
            'costo_unitario'    => $datos['costo_unitario'],
            'proveedor'         => $datos['proveedor'] ?? null,
            'fecha_ingreso'     => $datos['fecha_ingreso'] ?? now(),
            'notas'             => $datos['notas'] ?? null,
            'user_id'           => $datos['user_id'],
        ]);

        $this->increment('stock', $datos['cantidad']);
        $this->recalcularPrecioCompra();

        return $lote;
    }

    /**
     * Descuenta `$cantidad` unidades de los lotes con existencia, empezando por el
     * más antiguo (FIFO), partiendo entre varios lotes si hace falta. Devuelve el
     * detalle de qué se consumió de cada lote, para poder registrar `detalle_venta_lote`.
     * Lanza una excepción si no hay stock suficiente entre todos los lotes.
     */
    public function consumirStockFifo(int $cantidad): Collection
    {
        $porConsumir = $cantidad;
        $consumos = collect();

        $lotesDisponibles = $this->lotes()
            ->where('cantidad_restante', '>', 0)
            ->orderBy('fecha_ingreso')
            ->orderBy('id')
            ->get();

        foreach ($lotesDisponibles as $lote) {
            if ($porConsumir <= 0) {
                break;
            }

            $tomar = min($lote->cantidad_restante, $porConsumir);
            $lote->decrement('cantidad_restante', $tomar);
            $porConsumir -= $tomar;

            $consumos->push([
                'lote_id'        => $lote->id,
                'cantidad'       => $tomar,
                'costo_unitario' => $lote->costo_unitario,
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
     * Reversa exacta de consumirStockFifo(): devuelve cada cantidad a su lote de
     * origen (no a "cualquier lote actual"). Usado al editar/cancelar una venta.
     * Recibe una colección de registros con `lote_id` y `cantidad` (ej. las filas
     * de `detalle_venta_lote` de la venta que se está revirtiendo).
     */
    public function devolverStockFifo(iterable $consumos): void
    {
        $totalDevuelto = 0;

        foreach ($consumos as $consumo) {
            $loteId = is_array($consumo) ? $consumo['lote_id'] : $consumo->lote_id;
            $cant   = is_array($consumo) ? $consumo['cantidad'] : $consumo->cantidad;

            LoteProducto::where('id', $loteId)->increment('cantidad_restante', $cant);
            $totalDevuelto += $cant;
        }

        if ($totalDevuelto > 0) {
            $this->increment('stock', $totalDevuelto);
            $this->recalcularPrecioCompra();
        }
    }

    /** Costo del lote más antiguo con existencia (el "frente" FIFO) — no cambia si ya no quedan lotes con stock. */
    private function recalcularPrecioCompra(): void
    {
        $costoFrente = $this->lotes()
            ->where('cantidad_restante', '>', 0)
            ->orderBy('fecha_ingreso')
            ->orderBy('id')
            ->value('costo_unitario');

        if ($costoFrente !== null) {
            $this->update(['precio_compra' => $costoFrente]);
        }
    }
}
