@extends('layouts.app')
@section('title', 'Editar Venta '.$venta->numero_venta)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" style="color:#a855f7;">Ventas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ventas.show', $venta) }}" style="color:#a855f7;">{{ $venta->numero_venta }}</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- ── Formulario principal ──────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Editar Venta {{ $venta->numero_venta }}</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Modifica los productos y datos de la venta</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)
                            <div style="font-size:13px;"><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form id="formVenta" action="{{ route('ventas.update', $venta) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Cliente --}}
                    <div class="mb-4 position-relative">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <input type="text" id="buscadorCliente" class="form-control"
                               placeholder="Buscar por nombre o número de documento..." autocomplete="off">
                        <input type="hidden" name="cliente_id" id="clienteIdInput" value="{{ old('cliente_id', $venta->cliente_id) }}" required>

                        <div id="clienteResultados" class="list-group position-absolute w-100 shadow-sm"
                             style="z-index:1000; max-height:260px; overflow-y:auto; display:none;"></div>

                        <div id="clienteSeleccionado" class="mt-2 p-2 rounded-3 d-flex align-items-center justify-content-between"
                             style="background:#f8f5ff; font-size:13px; display:none;">
                            <span><i class="fas fa-user me-1" style="color:#a855f7;"></i><span id="clienteSeleccionadoTexto"></span></span>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="quitarClienteSeleccionado()">Cambiar</button>
                        </div>

                        <div id="clienteCumpleanio" class="mt-2 p-2 rounded-3" style="background:#fdf2f8; border:1px solid #fbcfe8; font-size:12.5px; color:#9d174d; display:none;">
                            🎂 Este cliente cumple años este mes — considera aplicar un descuento de fidelización.
                        </div>

                        <div id="clienteDistribuidor" class="mt-2 p-2 rounded-3" style="background:#f5f3ff; border:1px solid #ddd6fe; font-size:12.5px; color:#5b21b6; display:none;">
                            <i class="fas fa-percentage me-1"></i>Cliente distribuidor — se aplicará automáticamente un
                            <span id="clienteDistribuidorPct"></span>% de descuento sobre el subtotal.
                        </div>
                    </div>

                    {{-- Buscador de productos --}}
                    <div class="mb-3 position-relative">
                        <label class="form-label">Agregar Producto</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                            <input type="text" id="buscadorProducto" class="form-control"
                                   placeholder="Buscar por código o nombre..." autocomplete="off">
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <select id="filtroCategoria" class="form-select form-select-sm">
                                    <option value="">Categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select id="filtroMarca" class="form-select form-select-sm">
                                    <option value="">Marca</option>
                                    @foreach($marcas as $m)
                                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select id="filtroColor" class="form-select form-select-sm">
                                    <option value="">Color</option>
                                    @foreach($colores as $col)
                                        <option value="{{ $col->id }}">{{ $col->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select id="filtroAlmacenamiento" class="form-select form-select-sm">
                                    <option value="">Almacenamiento</option>
                                    @foreach($almacenamientos as $a)
                                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select id="filtroRam" class="form-select form-select-sm">
                                    <option value="">RAM</option>
                                    @foreach($rams as $r)
                                        <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select id="filtroCondicion" class="form-select form-select-sm">
                                    <option value="">Condición</option>
                                    @foreach($condiciones as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="productoResultados" class="list-group" style="max-height:280px; overflow-y:auto; display:none;"></div>
                    </div>

                    {{-- Tabla de productos seleccionados --}}
                    <div class="table-responsive mb-3">
                        <table class="table align-middle mb-0" id="tablaProductos">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th style="width:80px;">Cant.</th>
                                    <th style="width:110px;">Precio Unit.</th>
                                    <th style="width:100px;">Descuento</th>
                                    <th style="width:110px;">Subtotal</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="productosBody">
                                <tr id="filaVacia">
                                    <td colspan="6" class="text-center text-muted py-4" style="font-size:13px;">
                                        <i class="fas fa-shopping-basket fa-2x mb-2 d-block opacity-40"></i>
                                        Agrega productos a la venta
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Método de pago y notas --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago_id" class="form-select" required>
                                <option value="">— Seleccionar —</option>
                                @foreach($metodosPago as $mp)
                                    <option value="{{ $mp->id }}" {{ old('metodo_pago_id', $venta->metodo_pago_id)==$mp->id ? 'selected' : '' }}>{{ $mp->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Venta</label>
                            <div class="d-flex gap-3 mt-2">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="es_credito" value="0" {{ $venta->es_credito ? '' : 'checked' }} onchange="toggleCredito(false)">
                                    <span style="font-size:13px;">Contado</span>
                                </label>
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="es_credito" value="1" {{ $venta->es_credito ? 'checked' : '' }} onchange="toggleCredito(true)">
                                    <span style="font-size:13px;">Crédito</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 {{ $venta->es_credito ? '' : 'd-none' }}" id="campoFechaVencimiento">
                            <label class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_vencimiento" id="fechaVencimiento"
                                   value="{{ old('fecha_vencimiento', optional($venta->fecha_vencimiento)->format('Y-m-d')) }}">
                        </div>
                        @if($venta->es_credito)
                        <div class="col-md-6">
                            <div class="p-2 rounded-3" style="background:#f8f5ff; font-size:12.5px;">
                                <div class="text-muted">Ya abonado</div>
                                <div class="fw-bold">{{ $config->simbolo_moneda }} {{ number_format($venta->abonos()->sum('monto'), 2) }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Descuento General ({{ $config->simbolo_moneda }})</label>
                            <input type="number" class="form-control" name="descuento_general"
                                   id="descuentoGeneral" min="0" step="0.01" value="{{ old('descuento_general', $venta->descuento) }}"
                                   oninput="calcularTotales()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Modo de Precio</label>
                            <div class="d-flex gap-3 mt-2 flex-wrap">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="modo_precio" value="incluido" {{ $venta->modo_precio=='incluido' ? 'checked' : '' }} onchange="calcularTotales()">
                                    <span style="font-size:13px;">Impuesto incluido</span>
                                </label>
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="modo_precio" value="sin_impuesto" {{ $venta->modo_precio=='sin_impuesto' ? 'checked' : '' }} onchange="calcularTotales()">
                                    <span style="font-size:13px;">Sin impuesto</span>
                                </label>
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="modo_precio" value="subtotal_impuesto" {{ $venta->modo_precio=='subtotal_impuesto' ? 'checked' : '' }} onchange="calcularTotales()">
                                    <span style="font-size:13px;">Subtotal + impuesto</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" rows="2"
                                      placeholder="Observaciones de la venta...">{{ old('notas', $venta->notas) }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Panel de resumen ──────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card" style="position:sticky; top:90px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Resumen de Venta</h6>

                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Subtotal</span>
                    <span id="resSubtotal" class="fw-500">{{ $config->simbolo_moneda }} 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Descuento</span>
                    <span id="resDescuento" class="text-danger fw-500">— {{ $config->simbolo_moneda }} 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2" id="resImpuestoRow" style="font-size:13.5px;">
                    <span class="text-muted">Impuesto ({{ $config->igv }}%)</span>
                    <span id="resImpuesto" class="fw-500">{{ $config->simbolo_moneda }} 0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold" style="font-size:16px;">Total</span>
                    <span id="resTotal" style="font-size:22px; font-weight:700; color:#a855f7;">{{ $config->simbolo_moneda }} 0.00</span>
                </div>

                <div class="mb-3 p-3 rounded-3" style="background:#f8f5ff; font-size:13px;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Productos</span>
                        <span id="resCantProductos" class="fw-500">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Unidades</span>
                        <span id="resUnidades" class="fw-500">0</span>
                    </div>
                </div>

                <button type="submit" form="formVenta" class="btn btn-primary w-100 py-2" id="btnVenta" disabled>
                    <i class="fas fa-save me-2"></i>Actualizar Venta
                </button>

                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-outline-secondary w-100 mt-2 py-2">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const MONEDA = "{{ $config->simbolo_moneda }}";
const IGV_PORCENTAJE = {{ $config->igv }};
const DESCUENTO_DISTRIBUIDOR = {{ $config->descuento_distribuidor }};
let productosSeleccionados = {};
let clienteEsDistribuidor = false;
let contador = 0;

const clientesData = @json($clientesJson);

const productosData = @json($productosJson);

// ── Buscador de Cliente ──────────────────────────────────────────
const buscadorCliente     = document.getElementById('buscadorCliente');
const clienteResultados   = document.getElementById('clienteResultados');
const clienteIdInput      = document.getElementById('clienteIdInput');
const clienteSeleccionado = document.getElementById('clienteSeleccionado');

function filtrarClientes() {
    const q = buscadorCliente.value.trim().toLowerCase();
    if (!q) { clienteResultados.style.display = 'none'; clienteResultados.innerHTML = ''; return; }

    const coincidencias = clientesData.filter(c =>
        c.nombre.toLowerCase().includes(q) || (c.dni && c.dni.toLowerCase().includes(q))
    ).slice(0, 15);

    if (coincidencias.length === 0) {
        clienteResultados.innerHTML = '<div class="list-group-item text-muted" style="font-size:13px;">Sin coincidencias</div>';
    } else {
        clienteResultados.innerHTML = coincidencias.map(c => `
            <button type="button" class="list-group-item list-group-item-action" style="font-size:13px;" onclick="seleccionarCliente(${c.id})">
                <div style="font-weight:500;">${c.nombre}</div>
                <div style="font-size:11px; color:#9ca3af;">${c.tipo_documento ? c.tipo_documento + ': ' : 'Doc: '}${c.dni ?? '—'} · ${c.telefono ?? ''}</div>
            </button>
        `).join('');
    }
    clienteResultados.style.display = 'block';
}

function seleccionarCliente(id) {
    const c = clientesData.find(c => c.id === id);
    if (!c) return;
    clienteIdInput.value = c.id;
    document.getElementById('clienteSeleccionadoTexto').textContent =
        `${c.nombre} — ${c.tipo_documento ? c.tipo_documento + ' ' : ''}${c.dni ?? ''}`;
    clienteSeleccionado.style.display = 'flex';
    document.getElementById('clienteCumpleanio').style.display = c.cumple_mes ? 'block' : 'none';
    clienteEsDistribuidor = !!c.es_distribuidor;
    document.getElementById('clienteDistribuidorPct').textContent = DESCUENTO_DISTRIBUIDOR;
    document.getElementById('clienteDistribuidor').style.display = clienteEsDistribuidor ? 'block' : 'none';
    buscadorCliente.value = '';
    buscadorCliente.style.display = 'none';
    clienteResultados.style.display = 'none';
    clienteResultados.innerHTML = '';
    calcularTotales();
}

function quitarClienteSeleccionado() {
    clienteIdInput.value = '';
    clienteSeleccionado.style.display = 'none';
    document.getElementById('clienteCumpleanio').style.display = 'none';
    clienteEsDistribuidor = false;
    document.getElementById('clienteDistribuidor').style.display = 'none';
    buscadorCliente.style.display = 'block';
    buscadorCliente.value = '';
    buscadorCliente.focus();
    calcularTotales();
}

buscadorCliente.addEventListener('input', filtrarClientes);
document.addEventListener('click', function (e) {
    if (!e.target.closest('#buscadorCliente') && !e.target.closest('#clienteResultados')) {
        clienteResultados.style.display = 'none';
    }
});

// Precargar cliente de la venta
seleccionarCliente({{ $venta->cliente_id }});

// ── Buscador y filtros de Producto ───────────────────────────────
const buscadorProducto   = document.getElementById('buscadorProducto');
const productoResultados = document.getElementById('productoResultados');
const filtrosProducto    = ['filtroCategoria', 'filtroMarca', 'filtroColor', 'filtroAlmacenamiento', 'filtroRam', 'filtroCondicion']
    .map(id => document.getElementById(id));

function filtrarProductos() {
    const q = buscadorProducto.value.trim().toLowerCase();
    const [fCategoria, fMarca, fColor, fAlmacenamiento, fRam, fCondicion] = filtrosProducto.map(el => el.value);

    const hayFiltroActivo = q || fCategoria || fMarca || fColor || fAlmacenamiento || fRam || fCondicion;
    if (!hayFiltroActivo) { productoResultados.style.display = 'none'; productoResultados.innerHTML = ''; return; }

    const coincidencias = productosData.filter(p => {
        if (q && !p.nombre.toLowerCase().includes(q) && !(p.codigo && p.codigo.toLowerCase().includes(q))) return false;
        if (fCategoria && String(p.categoria_id) !== fCategoria) return false;
        if (fMarca && String(p.marca_id) !== fMarca) return false;
        if (fCondicion && String(p.condicion_id) !== fCondicion) return false;
        if (fColor && !p.variantes.some(v => String(v.color_id) === fColor)) return false;
        if (fAlmacenamiento && !p.variantes.some(v => String(v.almacenamiento_id) === fAlmacenamiento)) return false;
        if (fRam && !p.variantes.some(v => String(v.ram_id) === fRam)) return false;
        return true;
    }).slice(0, 30);

    if (coincidencias.length === 0) {
        productoResultados.innerHTML = '<div class="list-group-item text-muted" style="font-size:13px;">Sin coincidencias</div>';
    } else {
        productoResultados.innerHTML = coincidencias.map(renderResultadoProducto).join('');
    }
    productoResultados.style.display = 'block';
}

/** Etiqueta legible de una variante: "Rojo / 128GB / 8GB" (omite lo que no aplique). */
function etiquetaVariante(v) {
    return [v.color_nombre, v.almacenamiento_nombre, v.ram_nombre].filter(Boolean).join(' / ');
}

/**
 * Si el producto tiene una sola combinación de color/almacenamiento/ram con stock,
 * se agrega directo al hacer clic. Si tiene varias, se listan como sub-botones para
 * que el vendedor elija cuál vender antes de agregarlo al carrito.
 */
function renderResultadoProducto(p) {
    if (p.variantes.length <= 1) {
        const v = p.variantes[0] || { color_id: null, almacenamiento_id: null, ram_id: null, stock: p.stock };
        const etiqueta = etiquetaVariante(v);
        return `
            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="font-size:13px;" onclick="agregarProducto(${p.id}, 0)">
                <span>
                    <div style="font-weight:500;">${p.nombre}</div>
                    <div style="font-size:11px; color:#9ca3af;">
                        ${p.codigo ?? ''}${p.marca_nombre ? ' · ' + p.marca_nombre : ''}${etiqueta ? ' · ' + etiqueta : ''}
                    </div>
                </span>
                <span class="text-end">
                    <div style="font-weight:600;">${MONEDA} ${p.precio_venta.toFixed(2)}</div>
                    <div style="font-size:11px; color:#9ca3af;">Stock: ${v.stock}</div>
                </span>
            </button>`;
    }

    const opciones = p.variantes.map((v, i) => `
        <button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" style="font-size:11px;"
                onclick="event.stopPropagation(); agregarProducto(${p.id}, ${i})">
            ${etiquetaVariante(v) || 'Estándar'} <span class="text-muted">(${v.stock})</span>
        </button>
    `).join('');

    return `
        <div class="list-group-item" style="font-size:13px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span style="font-weight:500;">${p.nombre}</span>
                <span style="font-weight:600;">${MONEDA} ${p.precio_venta.toFixed(2)}</span>
            </div>
            <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Elige la variante a vender:</div>
            <div>${opciones}</div>
        </div>`;
}

buscadorProducto.addEventListener('input', filtrarProductos);
filtrosProducto.forEach(el => el.addEventListener('change', filtrarProductos));

function agregarFilaProducto(productoId, variante, cantidadInicial, descuentoInicial, imeiInicial, serialInicial) {
    const datos = productosData.find(p => p.id == productoId);
    if (!datos) return;

    const claveFila = productoId + '-' + (variante.color_id ?? 'x') + '-' + (variante.almacenamiento_id ?? 'x') + '-' + (variante.ram_id ?? 'x');
    const nombre = datos.nombre;
    const precio = datos.precio_venta;
    const stock  = variante.stock;
    const etiqueta = etiquetaVariante(variante);

    productosSeleccionados[claveFila] = { productoId, nombre, precio, stock };
    document.getElementById('filaVacia').style.display = 'none';

    const tbody = document.getElementById('productosBody');
    const tr = document.createElement('tr');
    tr.id = 'fila-' + claveFila;
    tr.innerHTML = `
        <td>
            <input type="hidden" name="productos[${claveFila}][id]" value="${productoId}">
            <input type="hidden" name="productos[${claveFila}][color_id]" value="${variante.color_id ?? ''}">
            <input type="hidden" name="productos[${claveFila}][almacenamiento_id]" value="${variante.almacenamiento_id ?? ''}">
            <input type="hidden" name="productos[${claveFila}][ram_id]" value="${variante.ram_id ?? ''}">
            <div style="font-size:13.5px; font-weight:500;">${nombre}</div>
            <div style="font-size:11px; color:#9ca3af;">${etiqueta ? etiqueta + ' · ' : ''}Stock: ${stock}</div>
        </td>
        <td>
            <input type="number" name="productos[${claveFila}][cantidad]" value="${cantidadInicial}" min="1" max="${stock}"
                   class="form-control form-control-sm cant-input" style="width:65px;"
                   oninput="calcularFila('${claveFila}')">
        </td>
        <td style="font-size:13.5px; font-weight:500;">${MONEDA} ${precio.toFixed(2)}</td>
        <td>
            <input type="number" name="productos[${claveFila}][descuento]" value="${descuentoInicial}" min="0" step="0.01"
                   class="form-control form-control-sm desc-input" style="width:80px;"
                   oninput="calcularFila('${claveFila}')">
        </td>
        <td id="sub-${claveFila}" style="font-size:13.5px; font-weight:600; color:#1e1b4b;">
            ${MONEDA} ${precio.toFixed(2)}
        </td>
        <td>
            <button type="button" class="btn btn-sm"
                    style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:4px 8px;"
                    onclick="quitarProducto('${claveFila}')">
                <i class="fas fa-times fa-xs"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);

    if (datos.requiere_imei || datos.requiere_serial) {
        const imeisIniciales    = imeiInicial ? imeiInicial.split(',').map(s => s.trim()) : [];
        const serialesIniciales = serialInicial ? serialInicial.split(',').map(s => s.trim()) : [];
        actualizarCamposImeiSerial(productoId, claveFila, imeisIniciales, serialesIniciales);
    }

    calcularFila(claveFila);
}

/** Precarga una línea de la venta existente, resolviendo su variante contra el stock disponible actual. */
function agregarFilaProductoPreload(productoId, colorId, almacenamientoId, ramId, cantidadInicial, descuentoInicial, imeiInicial, serialInicial) {
    const datos = productosData.find(p => p.id == productoId);
    let variante = datos ? datos.variantes.find(v => v.color_id == colorId && v.almacenamiento_id == almacenamientoId && v.ram_id == ramId) : null;
    if (!variante) {
        variante = { color_id: colorId, almacenamiento_id: almacenamientoId, ram_id: ramId, stock: cantidadInicial };
    }
    agregarFilaProducto(productoId, variante, cantidadInicial, descuentoInicial, imeiInicial, serialInicial);
}

function agregarProducto(id, varianteIndex) {
    const datos = productosData.find(p => p.id == id);
    if (!datos) return;

    const variante = datos.variantes[varianteIndex] || { color_id: null, almacenamiento_id: null, ram_id: null, stock: datos.stock };
    const claveFila = id + '-' + (variante.color_id ?? 'x') + '-' + (variante.almacenamiento_id ?? 'x') + '-' + (variante.ram_id ?? 'x');

    if (productosSeleccionados[claveFila]) {
        const fila = document.getElementById('fila-' + claveFila);
        const cantInput = fila.querySelector('.cant-input');
        const nuevaCant = parseInt(cantInput.value) + 1;
        if (nuevaCant > variante.stock) { alert('Stock insuficiente'); return; }
        cantInput.value = nuevaCant;
        calcularFila(claveFila);
    } else {
        agregarFilaProducto(id, variante, 1, 0, null, null);
    }

    buscadorProducto.value = '';
    productoResultados.style.display = 'none';
    productoResultados.innerHTML = '';
    calcularTotales();
}

function calcularFila(claveFila) {
    const fila  = document.getElementById('fila-' + claveFila);
    const cant  = parseFloat(fila.querySelector('.cant-input').value) || 0;
    const desc  = parseFloat(fila.querySelector('.desc-input').value) || 0;
    const sub   = (productosSeleccionados[claveFila].precio * cant) - desc;
    document.getElementById('sub-' + claveFila).textContent = MONEDA + ' ' + Math.max(sub, 0).toFixed(2);
    actualizarCamposImeiSerial(productosSeleccionados[claveFila].productoId, claveFila);
    calcularTotales();
}

/**
 * Genera/regenera un input de IMEI y/o Serial por cada unidad de la cantidad
 * actual de la línea. En la primera generación (al precargar una venta
 * existente o al agregar el producto), usa los valores iniciales recibidos;
 * después, conserva lo que el usuario ya haya escrito en cada posición.
 */
function actualizarCamposImeiSerial(productoId, claveFila, imeisIniciales, serialesIniciales) {
    const datos = productosData.find(p => p.id == productoId);
    if (!datos || (!datos.requiere_imei && !datos.requiere_serial)) return;

    const fila = document.getElementById('fila-' + claveFila);
    const cant = Math.max(parseInt(fila.querySelector('.cant-input').value) || 0, 0);

    let trExtra = document.getElementById('fila-extra-' + claveFila);
    const esNueva = !trExtra;
    if (esNueva) {
        trExtra = document.createElement('tr');
        trExtra.id = 'fila-extra-' + claveFila;
        fila.after(trExtra);
    }

    const imeisPrevios    = esNueva ? (imeisIniciales || []) : Array.from(trExtra.querySelectorAll('.imei-input')).map(i => i.value);
    const serialesPrevios = esNueva ? (serialesIniciales || []) : Array.from(trExtra.querySelectorAll('.serial-input')).map(i => i.value);

    let html = '<td colspan="6" class="pt-0"><div class="row g-2">';

    if (datos.requiere_imei) {
        html += `<div class="col-md-6"><label class="form-label" style="font-size:11px;">IMEI (${cant} unidad${cant===1?'':'es'})</label>`;
        for (let i = 0; i < cant; i++) {
            const val = imeisPrevios[i] ?? '';
            html += `<input type="text" name="productos[${claveFila}][imei][]" class="form-control form-control-sm imei-input mb-1"
                            placeholder="IMEI unidad ${i+1}" value="${val}" required oninput="calcularTotales()">`;
        }
        html += '</div>';
    }

    if (datos.requiere_serial) {
        html += `<div class="col-md-6"><label class="form-label" style="font-size:11px;">Serial (${cant} unidad${cant===1?'':'es'})</label>`;
        for (let i = 0; i < cant; i++) {
            const val = serialesPrevios[i] ?? '';
            html += `<input type="text" name="productos[${claveFila}][serial][]" class="form-control form-control-sm serial-input mb-1"
                            placeholder="Serial unidad ${i+1}" value="${val}" required oninput="calcularTotales()">`;
        }
        html += '</div>';
    }

    html += '</div></td>';
    trExtra.innerHTML = html;
}

function toggleCredito(esCredito) {
    const campoFecha  = document.getElementById('campoFechaVencimiento');
    const fechaInput  = document.getElementById('fechaVencimiento');

    campoFecha.classList.toggle('d-none', !esCredito);
    fechaInput.required = esCredito;
    if (!esCredito) {
        fechaInput.value = '';
    }
}

function quitarProducto(id) {
    document.getElementById('fila-' + id).remove();
    const filaExtra = document.getElementById('fila-extra-' + id);
    if (filaExtra) filaExtra.remove();
    delete productosSeleccionados[id];
    if (Object.keys(productosSeleccionados).length === 0) {
        document.getElementById('filaVacia').style.display = '';
    }
    calcularTotales();
}

function calcularTotales() {
    let subtotal    = 0;
    let unidades    = 0;
    const productos = Object.keys(productosSeleccionados);

    productos.forEach(id => {
        const fila = document.getElementById('fila-' + id);
        if (!fila) return;
        const cant = parseFloat(fila.querySelector('.cant-input').value) || 0;
        const desc = parseFloat(fila.querySelector('.desc-input').value) || 0;
        subtotal  += (productosSeleccionados[id].precio * cant) - desc;
        unidades  += cant;
    });

    const descGen          = parseFloat(document.getElementById('descuentoGeneral').value) || 0;
    const descDistribuidor = clienteEsDistribuidor ? subtotal * (DESCUENTO_DISTRIBUIDOR / 100) : 0;
    const descuentoTotal   = descGen + descDistribuidor;
    const modoPrecio       = document.querySelector('input[name="modo_precio"]:checked').value;
    const baseConDescuento = Math.max(subtotal - descuentoTotal, 0);

    let subtotalNeto, impuesto, total;
    if (modoPrecio === 'incluido') {
        total        = baseConDescuento;
        subtotalNeto = total / (1 + IGV_PORCENTAJE / 100);
        impuesto     = total - subtotalNeto;
    } else if (modoPrecio === 'sin_impuesto') {
        subtotalNeto = baseConDescuento;
        impuesto     = 0;
        total        = subtotalNeto;
    } else {
        subtotalNeto = baseConDescuento;
        impuesto     = subtotalNeto * (IGV_PORCENTAJE / 100);
        total        = subtotalNeto + impuesto;
    }

    document.getElementById('resSubtotal').textContent    = MONEDA + ' ' + subtotalNeto.toFixed(2);
    document.getElementById('resDescuento').textContent   = '— ' + MONEDA + ' ' + descuentoTotal.toFixed(2);
    document.getElementById('resImpuestoRow').style.display = (modoPrecio === 'sin_impuesto') ? 'none' : 'flex';
    document.getElementById('resImpuesto').textContent    = MONEDA + ' ' + impuesto.toFixed(2);
    document.getElementById('resTotal').textContent       = MONEDA + ' ' + total.toFixed(2);
    document.getElementById('resCantProductos').textContent = productos.length;
    document.getElementById('resUnidades').textContent    = unidades;

    const faltaCampoRequerido = Array.from(document.querySelectorAll('#tablaProductos input[required]'))
        .some(input => !input.value.trim());

    document.getElementById('btnVenta').disabled =
        (productos.length === 0 || !clienteIdInput.value || faltaCampoRequerido);
}

// Precargar productos ya vendidos en esta venta
@foreach($venta->detalles as $d)
agregarFilaProductoPreload(
    {{ $d->producto_id }},
    {{ $d->color_id ?? 'null' }},
    {{ $d->almacenamiento_id ?? 'null' }},
    {{ $d->ram_id ?? 'null' }},
    {{ $d->cantidad }},
    {{ $d->descuento }},
    @json($d->imei_vendido),
    @json($d->serial_vendido)
);
@endforeach
calcularTotales();
</script>
@endpush
