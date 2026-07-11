@extends('layouts.app')
@section('title', 'Nueva Venta')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" style="color:#a855f7;">Ventas</a></li>
    <li class="breadcrumb-item active">Nueva Venta</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- ── Formulario principal ──────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Registrar Venta</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Selecciona los productos y completa los datos</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)
                            <div style="font-size:13px;"><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form id="formVenta" action="{{ route('ventas.store') }}" method="POST">
                    @csrf

                    {{-- Cliente --}}
                    <div class="mb-4 position-relative">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <input type="text" id="buscadorCliente" class="form-control"
                               placeholder="Buscar por nombre o número de documento..." autocomplete="off">
                        <input type="hidden" name="cliente_id" id="clienteIdInput" value="{{ old('cliente_id') }}" required>

                        <div id="clienteResultados" class="list-group position-absolute w-100 shadow-sm"
                             style="z-index:1000; max-height:260px; overflow-y:auto; display:none;"></div>

                        <div id="clienteSeleccionado" class="mt-2 p-2 rounded-3 d-flex align-items-center justify-content-between"
                             style="background:var(--table-head-bg); font-size:13px; display:none;">
                            <span><i class="fas fa-user me-1" style="color:#a855f7;"></i><span id="clienteSeleccionadoTexto"></span></span>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="quitarClienteSeleccionado()">Cambiar</button>
                        </div>

                        <div id="clienteCumpleanio" class="mt-2 p-2 rounded-3" style="background:#fdf2f8; border:1px solid #fbcfe8; font-size:12.5px; color:#9d174d; display:none;">
                            🎂 Este cliente cumple años este mes — considera aplicar un descuento de fidelización.
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
                                        <option value="{{ $col }}">{{ $col }}</option>
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
                                    <option value="{{ $mp->id }}" {{ old('metodo_pago_id')==$mp->id ? 'selected' : '' }}>{{ $mp->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Venta</label>
                            <div class="d-flex gap-3 mt-2">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="es_credito" value="0" checked onchange="toggleCredito(false)">
                                    <span style="font-size:13px;">Contado</span>
                                </label>
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="es_credito" value="1" onchange="toggleCredito(true)">
                                    <span style="font-size:13px;">Crédito</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="campoFechaVencimiento">
                            <label class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_vencimiento" id="fechaVencimiento"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 d-none" id="campoAbonoInicial">
                            <label class="form-label">Abono Inicial ({{ $config->simbolo_moneda }})</label>
                            <input type="number" class="form-control" name="abono_inicial"
                                   id="abonoInicial" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descuento General ({{ $config->simbolo_moneda }})</label>
                            <input type="number" class="form-control" name="descuento_general"
                                   id="descuentoGeneral" min="0" step="0.01" value="0"
                                   oninput="calcularTotales()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Modo de Precio</label>
                            <div class="d-flex gap-3 mt-2 flex-wrap">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="modo_precio" value="incluido" checked onchange="calcularTotales()">
                                    <span style="font-size:13px;">Impuesto incluido</span>
                                </label>
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="modo_precio" value="sin_impuesto" onchange="calcularTotales()">
                                    <span style="font-size:13px;">Sin impuesto</span>
                                </label>
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="modo_precio" value="subtotal_impuesto" onchange="calcularTotales()">
                                    <span style="font-size:13px;">Subtotal + impuesto</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" rows="2"
                                      placeholder="Observaciones de la venta..."></textarea>
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

                <div class="mb-3 p-3 rounded-3" style="background:var(--table-head-bg); font-size:13px;">
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
                    <i class="fas fa-cash-register me-2"></i>Registrar Venta
                </button>

                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary w-100 mt-2 py-2">
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
let productosSeleccionados = {};
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
                <div style="font-size:11px; color:var(--text-muted-2);">${c.tipo_documento ? c.tipo_documento + ': ' : 'Doc: '}${c.dni ?? '—'} · ${c.telefono ?? ''}</div>
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

@if(old('cliente_id'))
seleccionarCliente({{ old('cliente_id') }});
@endif

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
        if (fColor && p.color !== fColor) return false;
        if (fAlmacenamiento && String(p.almacenamiento_id) !== fAlmacenamiento) return false;
        if (fRam && String(p.ram_id) !== fRam) return false;
        if (fCondicion && String(p.condicion_id) !== fCondicion) return false;
        return true;
    }).slice(0, 30);

    if (coincidencias.length === 0) {
        productoResultados.innerHTML = '<div class="list-group-item text-muted" style="font-size:13px;">Sin coincidencias</div>';
    } else {
        productoResultados.innerHTML = coincidencias.map(p => `
            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="font-size:13px;" onclick="agregarProducto(${p.id})">
                <span>
                    <div style="font-weight:500;">${p.nombre}</div>
                    <div style="font-size:11px; color:var(--text-muted-2);">
                        ${p.codigo ?? ''}${p.marca_nombre ? ' · ' + p.marca_nombre : ''}${p.color ? ' · ' + p.color : ''}${p.almacenamiento_nombre ? ' · ' + p.almacenamiento_nombre : ''}${p.ram_nombre ? ' · ' + p.ram_nombre : ''}
                    </div>
                </span>
                <span class="text-end">
                    <div style="font-weight:600;">${MONEDA} ${p.precio_venta.toFixed(2)}</div>
                    <div style="font-size:11px; color:var(--text-muted-2);">Stock: ${p.stock}</div>
                </span>
            </button>
        `).join('');
    }
    productoResultados.style.display = 'block';
}

buscadorProducto.addEventListener('input', filtrarProductos);
filtrosProducto.forEach(el => el.addEventListener('change', filtrarProductos));

function agregarProducto(id) {
    const datos = productosData.find(p => p.id === id);
    if (!datos) return;

    const nombre = datos.nombre;
    const precio = datos.precio_venta;
    const stock  = datos.stock;

    if (productosSeleccionados[id]) {
        // Ya existe: incrementar cantidad
        const fila = document.getElementById('fila-' + id);
        const cantInput = fila.querySelector('.cant-input');
        const nuevaCant = parseInt(cantInput.value) + 1;
        if (nuevaCant > stock) { alert('Stock insuficiente'); return; }
        cantInput.value = nuevaCant;
        calcularFila(id);
    } else {
        productosSeleccionados[id] = { nombre, precio, stock };
        document.getElementById('filaVacia').style.display = 'none';

        const tbody = document.getElementById('productosBody');
        const tr = document.createElement('tr');
        tr.id = 'fila-' + id;
        tr.innerHTML = `
            <td>
                <input type="hidden" name="productos[${id}][id]" value="${id}">
                <div style="font-size:13.5px; font-weight:500;">${nombre}</div>
                <div style="font-size:11px; color:var(--text-muted-2);">Stock: ${stock}</div>
            </td>
            <td>
                <input type="number" name="productos[${id}][cantidad]" value="1" min="1" max="${stock}"
                       class="form-control form-control-sm cant-input" style="width:65px;"
                       oninput="calcularFila('${id}')">
            </td>
            <td style="font-size:13.5px; font-weight:500;">${MONEDA} ${precio.toFixed(2)}</td>
            <td>
                <input type="number" name="productos[${id}][descuento]" value="0" min="0" step="0.01"
                       class="form-control form-control-sm desc-input" style="width:80px;"
                       oninput="calcularFila('${id}')">
            </td>
            <td id="sub-${id}" style="font-size:13.5px; font-weight:600; color:var(--text-dark);">
                ${MONEDA} ${precio.toFixed(2)}
            </td>
            <td>
                <button type="button" class="btn btn-sm"
                        style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:4px 8px;"
                        onclick="quitarProducto('${id}')">
                    <i class="fas fa-times fa-xs"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);

        if (datos.requiere_imei || datos.requiere_serial) {
            const trExtra = document.createElement('tr');
            trExtra.id = 'fila-extra-' + id;
            trExtra.innerHTML = `
                <td colspan="6" class="pt-0">
                    <div class="row g-2">
                        ${datos.requiere_imei ? `
                        <div class="col-md-6">
                            <input type="text" name="productos[${id}][imei]" class="form-control form-control-sm"
                                   placeholder="IMEI (requerido para este producto)" required oninput="calcularTotales()">
                        </div>` : ''}
                        ${datos.requiere_serial ? `
                        <div class="col-md-6">
                            <input type="text" name="productos[${id}][serial]" class="form-control form-control-sm"
                                   placeholder="Serial (requerido para este producto)" required oninput="calcularTotales()">
                        </div>` : ''}
                    </div>
                </td>
            `;
            tbody.appendChild(trExtra);
        }
    }

    buscadorProducto.value = '';
    productoResultados.style.display = 'none';
    productoResultados.innerHTML = '';
    calcularTotales();
}

function calcularFila(id) {
    const fila  = document.getElementById('fila-' + id);
    const cant  = parseFloat(fila.querySelector('.cant-input').value) || 0;
    const desc  = parseFloat(fila.querySelector('.desc-input').value) || 0;
    const sub   = (productosSeleccionados[id].precio * cant) - desc;
    document.getElementById('sub-' + id).textContent = MONEDA + ' ' + Math.max(sub, 0).toFixed(2);
    calcularTotales();
}

function toggleCredito(esCredito) {
    const campoFecha  = document.getElementById('campoFechaVencimiento');
    const campoAbono  = document.getElementById('campoAbonoInicial');
    const fechaInput  = document.getElementById('fechaVencimiento');

    campoFecha.classList.toggle('d-none', !esCredito);
    campoAbono.classList.toggle('d-none', !esCredito);
    fechaInput.required = esCredito;
    if (!esCredito) {
        fechaInput.value = '';
        document.getElementById('abonoInicial').value = 0;
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
    const modoPrecio       = document.querySelector('input[name="modo_precio"]:checked').value;
    const baseConDescuento = Math.max(subtotal - descGen, 0);

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
    document.getElementById('resDescuento').textContent   = '— ' + MONEDA + ' ' + descGen.toFixed(2);
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
</script>
@endpush
