@extends('layouts.app')
@section('title', 'Inventario')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inventario</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Inventario de Productos</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            {{ $productos->total() }} productos ·
            <span class="text-danger fw-500">{{ $productos->where('stock', '<=', 0)->count() }} sin stock</span>
        </p>
    </div>
    <a href="{{ route('productos.create') }}" class="btn btn-primary px-4">
        <i class="fas fa-plus me-2"></i>Nuevo Producto
    </a>
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    <input type="text" class="form-control" name="buscar"
                           placeholder="Nombre, código, modelo..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="categoria_id">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id')==$cat->id?'selected':'' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="marca_id">
                    <option value="">Todas las marcas</option>
                    @foreach($marcas as $m)
                        <option value="{{ $m->id }}" {{ request('marca_id')==$m->id?'selected':'' }}>
                            {{ $m->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="condicion_id">
                    <option value="">Condición</option>
                    @foreach($condiciones as $c)
                        <option value="{{ $c->id }}" {{ request('condicion_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
        <div class="mt-2">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="stock_bajo" id="stockBajo"
                       value="1" {{ request('stock_bajo')?'checked':'' }}
                       onchange="this.form.submit()">
                <label class="form-check-label" for="stockBajo" style="font-size:13px;">
                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>Solo con stock bajo
                </label>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Producto</th>
                        <th>Categoría / Marca</th>
                        <th>Especificaciones</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Condición</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/'.$producto->imagen) }}"
                                         style="width:44px; height:44px; border-radius:10px; object-fit:cover;">
                                @else
                                    <div style="width:44px; height:44px; border-radius:10px;
                                        background:linear-gradient(135deg,#a855f7,#ec4899);
                                        display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-mobile-alt" style="color:#fff;"></i>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight:500; font-size:13.5px;">{{ $producto->nombre }}</div>
                                    <div style="font-size:11px; color:#9ca3af;">{{ $producto->codigo }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;">
                            <div>{{ $producto->categoria->nombre ?? '—' }}</div>
                            <div style="font-size:11px; color:#9ca3af;">{{ $producto->marca->nombre ?? '—' }}</div>
                        </td>
                        <td style="font-size:12px; color:#6b7280;">
                            @if($producto->almacenamiento) <span class="badge bg-light text-dark me-1">{{ $producto->almacenamiento->nombre }}</span> @endif
                            @if($producto->ram) <span class="badge bg-light text-dark">{{ $producto->ram->nombre }}</span> @endif
                            @if($producto->color) <div class="mt-1">{{ $producto->color }}</div> @endif
                        </td>
                        <td style="font-size:13px; color:#6b7280;">{{ $config->simbolo_moneda }} {{ number_format($producto->precio_compra, 2) }}</td>
                        <td style="font-size:13px; font-weight:600; color:#1e1b4b;">
                            {{ $config->simbolo_moneda }} {{ number_format($producto->precio_venta, 2) }}
                            <div style="font-size:11px; color:#10b981; font-weight:400;">
                                +{{ number_format($producto->margen, 1) }}% margen
                            </div>
                        </td>
                        <td>
                            @if($producto->stock <= 0)
                                <span style="background:#fee2e2; color:#dc2626; border-radius:20px; padding:4px 10px; font-size:12px; font-weight:600;">
                                    Sin stock
                                </span>
                            @elseif($producto->tieneStockBajo())
                                <span style="background:#fef3c7; color:#92400e; border-radius:20px; padding:4px 10px; font-size:12px; font-weight:600;">
                                    <i class="fas fa-exclamation-triangle fa-xs me-1"></i>{{ $producto->stock }}
                                </span>
                            @else
                                <span style="background:#d1fae5; color:#065f46; border-radius:20px; padding:4px 10px; font-size:12px; font-weight:600;">
                                    {{ $producto->stock }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $cond = ['Nuevo'=>['#d1fae5','#065f46'],'Reacondicionado'=>['#e0f2fe','#0369a1'],'Usado'=>['#f3f4f6','#374151']];
                                $c = $cond[$producto->condicion->nombre ?? 'Nuevo'] ?? ['#f3f4f6','#374151'];
                            @endphp
                            <span style="background:{{ $c[0] }}; color:{{ $c[1] }}; border-radius:20px; padding:4px 10px; font-size:11px; font-weight:500;">
                                {{ $producto->condicion->nombre ?? '—' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('productos.show', $producto) }}"
                                   class="btn btn-sm" style="background:#f3f4f6; color:#374151; border-radius:8px; padding:5px 10px;" title="Ver">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                <a href="{{ route('productos.edit', $producto) }}"
                                   class="btn btn-sm" style="background:#ede9fe; color:#7c3aed; border-radius:8px; padding:5px 10px;" title="Editar">
                                    <i class="fas fa-edit fa-sm"></i>
                                </a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($producto->nombre) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm"
                                            style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:5px 10px;" title="Eliminar">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-box-open fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                            <p class="text-muted mb-2">No hay productos registrados</p>
                            <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Agregar producto
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productos->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px;">
                Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }} de {{ $productos->total() }} productos
            </span>
            {{ $productos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
