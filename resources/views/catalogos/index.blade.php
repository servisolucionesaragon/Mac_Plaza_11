@extends('layouts.app')
@section('title', 'Catálogos')

@section('breadcrumb')
    <li class="breadcrumb-item active">Catálogos</li>
@endsection

@section('content')

@if($errors->any())
    <div class="alert alert-danger" style="border-radius:12px;">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li style="font-size:13px;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- ── Header ── -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e1b4b;">Catálogos</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Gestión de categorías, marcas, condición, almacenamiento y RAM usados en el inventario</p>
    </div>
</div>

@php
    $tabsFijos = [
        ['tipo' => 'categorias',      'label' => 'Categorías',     'singular' => 'Categoría',      'icon' => 'fa-layer-group', 'items' => $categorias],
        ['tipo' => 'marcas',          'label' => 'Marcas',         'singular' => 'Marca',           'icon' => 'fa-copyright',   'items' => $marcas],
        ['tipo' => 'condiciones',     'label' => 'Condición',      'singular' => 'Condición',       'icon' => 'fa-certificate', 'items' => $condiciones],
        ['tipo' => 'almacenamientos', 'label' => 'Almacenamiento', 'singular' => 'Almacenamiento',  'icon' => 'fa-sd-card',     'items' => $almacenamientos],
        ['tipo' => 'rams',            'label' => 'RAM',            'singular' => 'RAM',             'icon' => 'fa-memory',      'items' => $rams],
        ['tipo' => 'colores',         'label' => 'Colores',        'singular' => 'Color',           'icon' => 'fa-palette',     'items' => $colores],
        ['tipo' => 'metodos_pago',    'label' => 'Métodos de Pago', 'singular' => 'Método de pago', 'icon' => 'fa-credit-card', 'items' => $metodosPago],
    ];

    $tabsDinamicos = $catalogoTipos->map(fn($ct) => [
        'tipo'        => 'dyn-' . $ct->id,
        'tipoId'      => $ct->id,
        'label'       => $ct->nombre,
        'singular'    => $ct->nombre,
        'icon'        => $ct->icono ?: 'fa-list',
        'items'       => $ct->valores,
        'dinamico'    => true,
        'activo'      => $ct->activo,
        'descripcion' => $ct->descripcion,
    ])->all();

    $tabs = array_merge($tabsFijos, $tabsDinamicos);

    $iconosDisponibles = [
        'fa-list' => 'Lista', 'fa-tags' => 'Etiquetas', 'fa-tag' => 'Etiqueta', 'fa-boxes' => 'Cajas',
        'fa-truck' => 'Camión/Proveedor', 'fa-store' => 'Tienda', 'fa-warehouse' => 'Almacén',
        'fa-building' => 'Edificio/Sucursal', 'fa-map-marker-alt' => 'Ubicación', 'fa-globe' => 'Global',
        'fa-users' => 'Personas', 'fa-handshake' => 'Acuerdo', 'fa-clipboard-list' => 'Lista de control',
        'fa-layer-group' => 'Capas', 'fa-shield-alt' => 'Garantía', 'fa-star' => 'Destacado',
        'fa-gift' => 'Promoción', 'fa-wrench' => 'Servicio técnico', 'fa-folder' => 'Carpeta',
        'fa-database' => 'Base de datos',
    ];
@endphp

<div class="card">
    <div class="card-body p-4">

        <ul class="nav nav-tabs" id="catalogosTab" role="tablist" style="border-bottom:1px solid #f3f4f6;">
            @foreach($tabs as $i => $tab)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $i === 0 ? 'active' : '' }}" id="tab-{{ $tab['tipo'] }}-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-{{ $tab['tipo'] }}" type="button"
                            role="tab" aria-controls="tab-{{ $tab['tipo'] }}">
                        <i class="fas {{ $tab['icon'] }} me-2"></i>{{ $tab['label'] }}
                        <span style="background:#ede9fe;color:#7c3aed;border-radius:20px;padding:2px 8px;font-size:11px;margin-left:6px;">
                            {{ $tab['items']->count() }}
                        </span>
                    </button>
                </li>
            @endforeach
            <li class="nav-item ms-auto" role="presentation">
                <button class="nav-link text-primary" type="button" title="Nuevo catálogo" onclick="abrirModalNuevoTipo()">
                    <i class="fas fa-plus"></i>
                </button>
            </li>
        </ul>

        <div class="tab-content pt-4" id="catalogosTabContent">
            @foreach($tabs as $i => $tab)
                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="tab-{{ $tab['tipo'] }}"
                     role="tabpanel" aria-labelledby="tab-{{ $tab['tipo'] }}-btn">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0">
                            {{ $tab['label'] }}
                            @if(($tab['dinamico'] ?? false) && !$tab['activo'])
                                <span style="background:#fee2e2;color:#991b1b;border-radius:20px;padding:2px 8px;font-size:10px;margin-left:6px;">Catálogo inactivo</span>
                            @endif
                        </h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm px-3"
                                @if($tab['dinamico'] ?? false)
                                    onclick="abrirModalNuevoValor({{ $tab['tipoId'] }}, '{{ addslashes($tab['singular']) }}')"
                                @else
                                    onclick="abrirModalNuevo('{{ $tab['tipo'] }}', '{{ $tab['singular'] }}')"
                                @endif
                            >
                                <i class="fas fa-plus me-2"></i>Nuevo
                            </button>

                            @if($tab['dinamico'] ?? false)
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog" style="font-size:12px;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="abrirModalEditarTipo({{ $tab['tipoId'] }}, '{{ addslashes($tab['label']) }}', '{{ addslashes($tab['descripcion'] ?? '') }}', '{{ $tab['icon'] }}'); return false;">
                                        <i class="fas fa-edit me-2"></i>Editar catálogo</a></li>
                                    <li>
                                        <form action="{{ route('catalogos.tipos.toggle', $tab['tipoId']) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-{{ $tab['activo'] ? 'ban' : 'check' }} me-2"></i>{{ $tab['activo'] ? 'Desactivar' : 'Activar' }} catálogo
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('catalogos.tipos.destroy', $tab['tipoId']) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar el catálogo \'{{ addslashes($tab['label']) }}\'? Se eliminarán también sus {{ $tab['items']->count() }} valores. Esta acción no se puede deshacer.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>Eliminar catálogo
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Nombre</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tab['items'] as $item)
                                <tr>
                                    <td class="ps-3" style="font-size:13.5px;font-weight:500;">{{ $item->nombre }}</td>
                                    <td>
                                        @if($item->activo)
                                            <span style="background:#d1fae5;color:#065f46;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:500;">
                                                Activo
                                            </span>
                                        @else
                                            <span style="background:#fee2e2;color:#991b1b;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:500;">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <!-- Editar -->
                                            <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:4px 10px;"
                                                    title="Editar"
                                                    @if($tab['dinamico'] ?? false)
                                                        onclick="abrirModalEditarValor({{ $item->id }}, '{{ addslashes($item->nombre) }}', '{{ addslashes($tab['label']) }}')"
                                                    @else
                                                        onclick="abrirModalEditar('{{ $tab['tipo'] }}', {{ $item->id }}, '{{ addslashes($item->nombre) }}', '{{ $tab['singular'] }}')"
                                                    @endif
                                            >
                                                <i class="fas fa-edit" style="font-size:12px;"></i>
                                            </button>

                                            <!-- Toggle activo -->
                                            <form action="{{ ($tab['dinamico'] ?? false) ? route('catalogos.valores.toggle', $item->id) : route('catalogos.toggle', [$tab['tipo'], $item->id]) }}" method="POST" style="display:inline;">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $item->activo ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        style="border-radius:8px;padding:4px 10px;"
                                                        title="{{ $item->activo ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fas fa-{{ $item->activo ? 'ban' : 'check' }}" style="font-size:12px;"></i>
                                                </button>
                                            </form>

                                            <!-- Eliminar -->
                                            <form action="{{ ($tab['dinamico'] ?? false) ? route('catalogos.valores.destroy', $item->id) : route('catalogos.destroy', [$tab['tipo'], $item->id]) }}" method="POST" style="display:inline;"
                                                  onsubmit="return confirm('¿Eliminar {{ addslashes($item->nombre) }}? Esta acción no se puede deshacer.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        style="border-radius:8px;padding:4px 10px;"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash" style="font-size:12px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="fas {{ $tab['icon'] }} fa-2x mb-3 d-block" style="color:#d1d5db;"></i>
                                        <p class="text-muted mb-0">No hay registros de {{ strtolower($tab['label']) }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<!-- ══════════ MODAL: Nuevo registro (genérico) ══════════ -->
<div class="modal fade" id="modalNuevoCatalogo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-plus me-2" style="color:#a855f7;"></i><span id="nuevoTitulo">Nuevo</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoCatalogo" action="" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nuevoNombre" class="form-control" placeholder="Ej: Smartphones" required maxlength="100">
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Crear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Editar registro (genérico) ══════════ -->
<div class="modal fade" id="modalEditarCatalogo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2" style="color:#a855f7;"></i>Editar <span id="editarTitulo"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarCatalogo" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="editarNombre" class="form-control" required maxlength="100">
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Nuevo catálogo (tipo) ══════════ -->
<div class="modal fade" id="modalNuevoTipo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold"><i class="fas fa-plus me-2" style="color:#a855f7;"></i>Nuevo catálogo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoTipo" action="{{ route('catalogos.tipos.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control mb-3" placeholder="Ej: Proveedores" required maxlength="100">
                    <label class="form-label">Descripción (opcional)</label>
                    <input type="text" name="descripcion" class="form-control mb-3" maxlength="255">
                    <label class="form-label">Ícono</label>
                    <input type="hidden" name="icono" id="iconoNuevoTipo" value="fa-list">
                    <div class="d-flex flex-wrap gap-2" id="iconoPickerNuevoTipo">
                        @foreach($iconosDisponibles as $val => $label)
                            <button type="button" class="btn btn-outline-secondary icono-opcion {{ $val === 'fa-list' ? 'active' : '' }}"
                                    data-valor="{{ $val }}" title="{{ $label }}"
                                    onclick="seleccionarIcono('iconoNuevoTipo', 'iconoPickerNuevoTipo', this)">
                                <i class="fas {{ $val }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Crear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Editar catálogo (tipo) ══════════ -->
<div class="modal fade" id="modalEditarTipo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2" style="color:#a855f7;"></i>Editar catálogo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarTipo" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="editarTipoNombre" class="form-control mb-3" required maxlength="100">
                    <label class="form-label">Descripción (opcional)</label>
                    <input type="text" name="descripcion" id="editarTipoDescripcion" class="form-control mb-3" maxlength="255">
                    <label class="form-label">Ícono</label>
                    <input type="hidden" name="icono" id="editarTipoIcono" value="fa-list">
                    <div class="d-flex flex-wrap gap-2" id="iconoPickerEditarTipo">
                        @foreach($iconosDisponibles as $val => $label)
                            <button type="button" class="btn btn-outline-secondary icono-opcion"
                                    data-valor="{{ $val }}" title="{{ $label }}"
                                    onclick="seleccionarIcono('editarTipoIcono', 'iconoPickerEditarTipo', this)">
                                <i class="fas {{ $val }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirModalNuevo(tipo, singular) {
    document.getElementById('nuevoTitulo').textContent = 'Nuevo: ' + singular;
    document.getElementById('nuevoNombre').value = '';
    document.getElementById('formNuevoCatalogo').action = '{{ url('catalogos') }}/' + tipo;
    var modal = new bootstrap.Modal(document.getElementById('modalNuevoCatalogo'));
    modal.show();
}

function abrirModalEditar(tipo, id, nombre, singular) {
    document.getElementById('editarTitulo').textContent = singular;
    document.getElementById('editarNombre').value = nombre;
    document.getElementById('formEditarCatalogo').action = '{{ url('catalogos') }}/' + tipo + '/' + id;
    var modal = new bootstrap.Modal(document.getElementById('modalEditarCatalogo'));
    modal.show();
}

// Valores de catálogos dinámicos → reusan los modales genéricos existentes
function abrirModalNuevoValor(catalogoTipoId, tipoNombre) {
    document.getElementById('nuevoTitulo').textContent = 'Nuevo valor: ' + tipoNombre;
    document.getElementById('nuevoNombre').value = '';
    document.getElementById('formNuevoCatalogo').action = '{{ url('catalogos/tipos') }}/' + catalogoTipoId + '/valores';
    var modal = new bootstrap.Modal(document.getElementById('modalNuevoCatalogo'));
    modal.show();
}

function abrirModalEditarValor(id, nombre, tipoNombre) {
    document.getElementById('editarTitulo').textContent = 'valor de ' + tipoNombre;
    document.getElementById('editarNombre').value = nombre;
    document.getElementById('formEditarCatalogo').action = '{{ url('catalogos/valores') }}/' + id;
    var modal = new bootstrap.Modal(document.getElementById('modalEditarCatalogo'));
    modal.show();
}

// Selector visual de íconos (con botones que muestran el ícono real, no el nombre en texto)
function seleccionarIcono(hiddenInputId, pickerId, boton) {
    document.getElementById(hiddenInputId).value = boton.dataset.valor;
    document.querySelectorAll('#' + pickerId + ' .icono-opcion').forEach(function (b) {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-outline-secondary');
    });
    boton.classList.remove('btn-outline-secondary');
    boton.classList.add('btn-primary', 'active');
}

function marcarIconoActivo(pickerId, valor) {
    document.querySelectorAll('#' + pickerId + ' .icono-opcion').forEach(function (b) {
        const activo = b.dataset.valor === valor;
        b.classList.toggle('btn-primary', activo);
        b.classList.toggle('active', activo);
        b.classList.toggle('btn-outline-secondary', !activo);
    });
}

// Tipos de catálogo (modales nuevos)
function abrirModalNuevoTipo() {
    document.getElementById('formNuevoTipo').reset();
    document.getElementById('iconoNuevoTipo').value = 'fa-list';
    marcarIconoActivo('iconoPickerNuevoTipo', 'fa-list');
    var modal = new bootstrap.Modal(document.getElementById('modalNuevoTipo'));
    modal.show();
}

function abrirModalEditarTipo(id, nombre, descripcion, icono) {
    var form = document.getElementById('formEditarTipo');
    form.action = '{{ url('catalogos/tipos') }}/' + id;
    document.getElementById('editarTipoNombre').value = nombre;
    document.getElementById('editarTipoDescripcion').value = descripcion;
    document.getElementById('editarTipoIcono').value = icono || 'fa-list';
    marcarIconoActivo('iconoPickerEditarTipo', icono || 'fa-list');
    var modal = new bootstrap.Modal(document.getElementById('modalEditarTipo'));
    modal.show();
}
</script>
@endpush
