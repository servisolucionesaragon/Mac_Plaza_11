@extends('layouts.app')
@section('title', $producto->nombre)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" style="color:#a855f7;">Inventario</a></li>
    <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- Panel izquierdo --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body p-4 text-center">
                @if($producto->imagen)
                    <img src="{{ asset('storage/'.$producto->imagen) }}"
                         style="width:100%; max-height:240px; object-fit:cover; border-radius:14px; margin-bottom:16px;">
                @else
                    <div style="width:100%; height:180px; background:linear-gradient(135deg,#a855f7,#ec4899);
                                border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
                        <i class="fas fa-mobile-alt" style="font-size:64px; color:rgba(255,255,255,.6);"></i>
                    </div>
                @endif

                <h5 class="fw-bold mb-1">{{ $producto->nombre }}</h5>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span style="background:#ede9fe; color:#7c3aed; border-radius:20px; padding:3px 10px; font-size:12px;">
                        {{ $producto->marca->nombre ?? '—' }}
                    </span>
                    <span style="background:#f3f4f6; color:#374151; border-radius:20px; padding:3px 10px; font-size:12px;">
                        {{ $producto->categoria->nombre ?? '—' }}
                    </span>
                    @php
                        $cond=['Nuevo'=>['#d1fae5','#065f46'],'Reacondicionado'=>['#e0f2fe','#0369a1'],'Usado'=>['#f3f4f6','#374151']];
                        $c=$cond[$producto->condicion->nombre ?? 'Nuevo']??['#f3f4f6','#374151'];
                    @endphp
                    <span style="background:{{ $c[0] }}; color:{{ $c[1] }}; border-radius:20px; padding:3px 10px; font-size:12px;">
                        {{ $producto->condicion->nombre ?? '—' }}
                    </span>
                </div>

                <hr>

                {{-- Stock indicator --}}
                <div class="mb-3">
                    <div style="font-size:11px; color:#9ca3af; margin-bottom:6px;">STOCK DISPONIBLE</div>
                    @if($producto->stock <= 0)
                        <div style="font-size:28px; font-weight:700; color:#dc2626;">0</div>
                        <div style="font-size:12px; color:#dc2626;">Sin stock</div>
                    @elseif($producto->tieneStockBajo())
                        <div style="font-size:28px; font-weight:700; color:#d97706;">{{ $producto->stock }}</div>
                        <div style="font-size:12px; color:#d97706;">⚠️ Stock bajo (mín. {{ $producto->stock_minimo }})</div>
                    @else
                        <div style="font-size:28px; font-weight:700; color:#059669;">{{ $producto->stock }}</div>
                        <div style="font-size:12px; color:#059669;">Stock óptimo</div>
                    @endif
                    <div class="progress mt-2" style="height:6px; border-radius:4px;">
                        @php $pct = $producto->stock_minimo > 0 ? min(($producto->stock/$producto->stock_minimo)*50, 100) : 100; @endphp
                        <div class="progress-bar" style="width:{{ $pct }}%; background:{{ $producto->stock>$producto->stock_minimo?'#10b981':'#f59e0b' }};"></div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar Producto
                    </a>
                    <a href="{{ route('ventas.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Registrar Venta
                    </a>
                </div>
            </div>
        </div>

        {{-- Precios --}}
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Precios</h6>
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Precio Compra</span>
                    <span>{{ $config->simbolo_moneda }} {{ number_format($producto->precio_compra, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Precio Venta</span>
                    <span style="font-weight:700; color:#1e1b4b;">{{ $config->simbolo_moneda }} {{ number_format($producto->precio_venta, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between" style="font-size:13.5px;">
                    <span class="text-muted">Margen de ganancia</span>
                    <span style="font-weight:700; color:#10b981;">{{ number_format($producto->margen, 1) }}%</span>
                </div>
                <div class="d-flex justify-content-between mt-1" style="font-size:13px;">
                    <span class="text-muted">Ganancia unitaria</span>
                    <span style="color:#10b981;">{{ $config->simbolo_moneda }} {{ number_format($producto->precio_venta - $producto->precio_compra, 2) }}</span>
                </div>
                <div class="mt-3 p-2 rounded-3 text-center" style="background:#f8f5ff; font-size:12px; color:#6b7280;">
                    Valor en stock: <strong style="color:#7c3aed;">{{ $config->simbolo_moneda }} {{ number_format($producto->stock * $producto->precio_venta, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel derecho --}}
    <div class="col-lg-8">
        {{-- Detalles técnicos --}}
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Especificaciones</h6>
                <div class="row g-3" style="font-size:13.5px;">
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">CÓDIGO SKU</span>
                        <strong>{{ $producto->codigo }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">MODELO</span>
                        <strong>{{ $producto->modelo ?: '—' }}</strong>
                    </div>
                    @php
                        $variantesStock = [];
                        foreach ($producto->lotes as $lote) {
                            foreach ($lote->variantes as $v) {
                                if ($v->cantidad_restante <= 0) continue;
                                $etiqueta = collect([$v->color->nombre ?? null, $v->almacenamiento->nombre ?? null, $v->ram->nombre ?? null])
                                    ->filter()->implode(' / ') ?: 'Estándar';
                                $variantesStock[$etiqueta] = ($variantesStock[$etiqueta] ?? 0) + $v->cantidad_restante;
                            }
                        }
                    @endphp
                    <div class="col-md-8">
                        <span class="text-muted d-block" style="font-size:11px;">VARIANTES EN STOCK (COLOR / ALMACENAMIENTO / RAM)</span>
                        @if(count($variantesStock))
                            <strong>{{ collect($variantesStock)->map(fn($cant, $et) => "{$et} ({$cant})")->implode(', ') }}</strong>
                        @else
                            <strong>—</strong>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">REQUIERE IMEI</span>
                        <strong>{{ $producto->requiere_imei ? 'Sí' : 'No' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">REQUIERE SERIAL</span>
                        <strong>{{ $producto->requiere_serial ? 'Sí' : 'No' }}</strong>
                    </div>
                    @if($producto->descripcion)
                    <div class="col-12">
                        <span class="text-muted d-block" style="font-size:11px;">DESCRIPCIÓN</span>
                        <p style="margin:0; color:#374151;">{{ $producto->descripcion }}</p>
                    </div>
                    @endif
                </div>

                @if($producto->catalogoValores->isNotEmpty())
                <hr class="my-3">
                <div class="row g-3" style="font-size:13.5px;">
                    @foreach($producto->catalogoValores->groupBy('tipo.nombre') as $nombreTipo => $valores)
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">
                            <i class="fas {{ $valores->first()->tipo->icono ?: 'fa-list' }} me-1"></i>{{ strtoupper($nombreTipo) }}
                        </span>
                        <strong>{{ $valores->pluck('nombre')->implode(', ') }}</strong>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Lotes de inventario (FIFO) --}}
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Lotes de Inventario</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarLote">
                        <i class="fas fa-plus me-1"></i>Agregar Lote
                    </button>
                </div>

                @if($producto->lotes->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>Fecha Ingreso</th>
                                <th>Variante</th>
                                <th>Cant. Inicial</th>
                                <th>Cant. Restante</th>
                                <th>Costo Unitario</th>
                                <th>Proveedor</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($producto->lotes as $lote)
                                @foreach($lote->variantes as $v)
                                <tr>
                                    <td style="color:#9ca3af;">{{ $lote->fecha_ingreso->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{ collect([$v->color->nombre ?? null, $v->almacenamiento->nombre ?? null, $v->ram->nombre ?? null])->filter()->implode(' / ') ?: 'Estándar' }}
                                    </td>
                                    <td>{{ $v->cantidad_inicial }}</td>
                                    <td>
                                        @if($v->cantidad_restante <= 0)
                                            <span class="text-muted">Agotado</span>
                                        @else
                                            <strong>{{ $v->cantidad_restante }}</strong>
                                        @endif
                                    </td>
                                    <td>{{ $config->simbolo_moneda }} {{ number_format($lote->costo_unitario, 2) }}</td>
                                    <td>{{ $lote->proveedor ?: '—' }}</td>
                                    <td>{{ $lote->notas ?: '—' }}</td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted" style="font-size:13px;">
                    <i class="fas fa-boxes fa-2x mb-2 d-block opacity-40"></i>
                    Este producto aún no tiene lotes registrados
                </div>
                @endif
            </div>
        </div>

        {{-- Historial de ventas --}}
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Historial de Ventas</h6>
                    <span style="background:#ede9fe; color:#7c3aed; border-radius:20px; padding:3px 12px; font-size:12px;">
                        {{ $producto->detalleVentas->count() }} ventas
                    </span>
                </div>

                @if($producto->detalleVentas->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>N° Venta</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Cant.</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($producto->detalleVentas->sortByDesc('created_at') as $det)
                            <tr>
                                <td>
                                    <a href="{{ route('ventas.show', $det->venta) }}"
                                       style="color:#a855f7; font-weight:500;">
                                        {{ $det->venta->numero_venta ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $det->venta->cliente->nombre_completo ?? '—' }}</td>
                                <td style="color:#9ca3af;">{{ $det->venta->fecha_venta?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $det->cantidad }}</td>
                                <td>{{ $config->simbolo_moneda }} {{ number_format($det->precio_unitario, 2) }}</td>
                                <td style="font-weight:600;">{{ $config->simbolo_moneda }} {{ number_format($det->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted" style="font-size:13px;">
                    <i class="fas fa-shopping-cart fa-2x mb-2 d-block opacity-40"></i>
                    Este producto aún no ha sido vendido
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal: Agregar Lote --}}
<div class="modal fade" id="modalAgregarLote" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('productos.lotes.store', $producto) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Agregar Lote de Inventario</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:12px;">
                        Registra unidades nuevas de <strong>{{ $producto->nombre }}</strong>. Si el costo es distinto
                        al de lotes anteriores, se conserva por separado (costeo FIFO). Si llegaron varias
                        combinaciones de color/almacenamiento/RAM en esta compra, agrega una fila de variante por cada una.
                    </p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Costo Unitario del Lote ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="costo_unitario" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select" name="proveedor">
                                <option value="">— Sin especificar —</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->nombre }}">{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                            @if($proveedores->isEmpty())
                                <div style="font-size:11px; color:#9ca3af; margin-top:2px;">
                                    No hay proveedores registrados. <a href="{{ route('catalogos.index') }}">Agregar en Catálogos</a>.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Variantes recibidas <span class="text-danger">*</span></label>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarVarianteLoteModal()">
                            <i class="fas fa-plus me-1"></i>Variante
                        </button>
                    </div>
                    <div id="variantesLoteModal"></div>

                    <div class="mb-1 mt-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" rows="2" placeholder="Opcional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Lote
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function construirOpcionesLote(lista, placeholder) {
    return `<option value="">${placeholder}</option>` + lista.map(x => `<option value="${x.id}">${x.nombre}</option>`).join('');
}
const COLOR_OPTIONS_LOTE = construirOpcionesLote(@json($colores->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre])), '— Sin especificar —');
const ALMACENAMIENTO_OPTIONS_LOTE = construirOpcionesLote(@json($almacenamientos->map(fn($a) => ['id' => $a->id, 'nombre' => $a->nombre])), '— Sin especificar —');
const RAM_OPTIONS_LOTE = construirOpcionesLote(@json($rams->map(fn($r) => ['id' => $r->id, 'nombre' => $r->nombre])), '— Sin especificar —');

let varianteLoteModalIndex = 0;

function agregarVarianteLoteModal() {
    const idx = varianteLoteModalIndex++;
    const div = document.createElement('div');
    div.className = 'variante-lote-row row g-2 align-items-end mb-1';
    div.innerHTML = `
        <div class="col-md-2">
            <label class="form-label mb-1" style="font-size:11px;">Cantidad <span class="text-danger">*</span></label>
            <input type="number" class="form-control form-control-sm" name="variantes[${idx}][cantidad]" min="1" required>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1" style="font-size:11px;">Color</label>
            <select class="form-select form-select-sm" name="variantes[${idx}][color_id]">${COLOR_OPTIONS_LOTE}</select>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1" style="font-size:11px;">Almacenamiento</label>
            <select class="form-select form-select-sm" name="variantes[${idx}][almacenamiento_id]">${ALMACENAMIENTO_OPTIONS_LOTE}</select>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1" style="font-size:11px;">RAM</label>
            <select class="form-select form-select-sm" name="variantes[${idx}][ram_id]">${RAM_OPTIONS_LOTE}</select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="quitarVarianteLoteModal(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
    document.getElementById('variantesLoteModal').appendChild(div);
}

function quitarVarianteLoteModal(btn) {
    const filas = document.querySelectorAll('#variantesLoteModal .variante-lote-row');
    if (filas.length <= 1) return;
    btn.closest('.variante-lote-row').remove();
}

agregarVarianteLoteModal();
</script>
@endpush
