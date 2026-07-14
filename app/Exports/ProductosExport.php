<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exporta una fila por cada variante (color/almacenamiento/ram) con stock
 * restante, no una fila por producto — es el único formato que refleja el
 * stock real por combinación bajo el costeo FIFO por lotes.
 */
class ProductosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection(): Collection
    {
        $filas = collect();

        $this->query->with(['lotes.variantes.color', 'lotes.variantes.almacenamiento', 'lotes.variantes.ram'])
            ->get()
            ->each(function ($producto) use ($filas) {
                $variantesConStock = $producto->lotes
                    ->flatMap(fn ($lote) => $lote->variantes->map(fn ($v) => (object) [
                        'lote'     => $lote,
                        'variante' => $v,
                    ]))
                    ->filter(fn ($item) => $item->variante->cantidad_restante > 0);

                if ($variantesConStock->isEmpty()) {
                    $filas->push((object) ['producto' => $producto, 'lote' => null, 'variante' => null]);
                    return;
                }

                foreach ($variantesConStock as $item) {
                    $filas->push((object) ['producto' => $producto, 'lote' => $item->lote, 'variante' => $item->variante]);
                }
            });

        return $filas;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
            'Categoría',
            'Marca',
            'Modelo',
            'Color',
            'Almacenamiento',
            'RAM',
            'Condición',
            'Costo Unitario Lote',
            'Precio Venta',
            'Margen %',
            'Cantidad Restante',
            'Stock Total Producto',
            'Stock Mínimo',
            'Estado Stock',
            'Activo',
        ];
    }

    public function map($fila): array
    {
        $producto = $fila->producto;
        $variante = $fila->variante;
        $lote     = $fila->lote;

        return [
            $producto->codigo,
            $producto->nombre,
            $producto->categoria->nombre ?? '—',
            $producto->marca->nombre ?? '—',
            $producto->modelo ?? '—',
            $variante?->color->nombre ?? '—',
            $variante?->almacenamiento->nombre ?? '—',
            $variante?->ram->nombre ?? '—',
            $producto->condicion->nombre ?? '—',
            $lote ? (float) $lote->costo_unitario : (float) $producto->precio_compra,
            (float) $producto->precio_venta,
            round($producto->margen, 1),
            $variante?->cantidad_restante ?? 0,
            $producto->stock,
            $producto->stock_minimo,
            $producto->tieneStockBajo() ? 'Stock Bajo' : 'OK',
            $producto->activo ? 'Sí' : 'No',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
