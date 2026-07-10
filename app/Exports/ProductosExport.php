<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductosExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
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
            'Precio Compra',
            'Precio Venta',
            'Margen %',
            'Stock',
            'Stock Mínimo',
            'Estado Stock',
            'Activo',
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->codigo,
            $producto->nombre,
            $producto->categoria->nombre ?? '—',
            $producto->marca->nombre ?? '—',
            $producto->modelo ?? '—',
            $producto->color ?? '—',
            $producto->almacenamiento->nombre ?? '—',
            $producto->ram->nombre ?? '—',
            $producto->condicion->nombre ?? '—',
            (float) $producto->precio_compra,
            (float) $producto->precio_venta,
            round($producto->margen, 1),
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
