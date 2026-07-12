@extends('layouts.app')
@section('title', 'Nueva Reparación')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.index') }}" style="color:#a855f7;">Reparaciones</a></li>
    <li class="breadcrumb-item active">Nueva Orden</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Nueva Orden de Reparación</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Registra un nuevo equipo para servicio técnico</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reparaciones.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        {{-- Cliente y Técnico --}}
                        <div class="col-12">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:#1e1b4b;">
                                <i class="fas fa-users me-2" style="color:#a855f7;"></i>Asignación
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6 position-relative">
                                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <input type="text" id="buscadorCliente" class="form-control @error('cliente_id') is-invalid @enderror"
                                           placeholder="Buscar por nombre o número de documento..." autocomplete="off">
                                    <input type="hidden" name="cliente_id" id="clienteIdInput"
                                           value="{{ old('cliente_id', request('cliente')) }}" required>
                                    @error('cliente_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                                    <div id="clienteResultados" class="list-group position-absolute w-100 shadow-sm"
                                         style="z-index:1000; max-height:260px; overflow-y:auto; display:none;"></div>

                                    <div id="clienteSeleccionado" class="mt-2 p-2 rounded-3 d-flex align-items-center justify-content-between"
                                         style="background:#f8f5ff; font-size:13px; display:none;">
                                        <span><i class="fas fa-user me-1" style="color:#a855f7;"></i><span id="clienteSeleccionadoTexto"></span></span>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="quitarClienteSeleccionado()">Cambiar</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Técnico Asignado <span class="text-danger">*</span></label>
                                    <select name="tecnico_id" class="form-select @error('tecnico_id') is-invalid @enderror" required>
                                        <option value="">— Seleccionar técnico —</option>
                                        @foreach($tecnicos as $t)
                                            <option value="{{ $t->id }}" {{ old('tecnico_id')==$t->id?'selected':'' }}>
                                                {{ $t->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tecnico_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Prioridad <span class="text-danger">*</span></label>
                                    <select name="prioridad" class="form-select" required>
                                        <option value="baja" {{ old('prioridad')=='baja'?'selected':'' }}>🟢 Baja</option>
                                        <option value="media" {{ old('prioridad','media')=='media'?'selected':'' }}>🟡 Media</option>
                                        <option value="alta" {{ old('prioridad')=='alta'?'selected':'' }}>🟠 Alta</option>
                                        <option value="urgente" {{ old('prioridad')=='urgente'?'selected':'' }}>🔴 Urgente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Equipo --}}
                        <div class="col-12">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:#1e1b4b;">
                                <i class="fas fa-mobile-alt me-2" style="color:#a855f7;"></i>Datos del Equipo
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Dispositivo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('dispositivo') is-invalid @enderror"
                                           name="dispositivo" value="{{ old('dispositivo') }}"
                                           placeholder="Ej: Smartphone, Tablet...">
                                    @error('dispositivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Marca</label>
                                    <input type="text" class="form-control" name="marca"
                                           value="{{ old('marca') }}" placeholder="Samsung, Apple, Xiaomi...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Modelo</label>
                                    <input type="text" class="form-control" name="modelo"
                                           value="{{ old('modelo') }}" placeholder="Galaxy A54, iPhone 15...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">IMEI / Serie</label>
                                    <input type="text" class="form-control" name="imei"
                                           value="{{ old('imei') }}" placeholder="123456789012345">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="color"
                                           value="{{ old('color') }}" placeholder="Negro, Blanco...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Estimada de Entrega</label>
                                    <input type="date" class="form-control" name="fecha_estimada"
                                           value="{{ old('fecha_estimada') }}" min="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Falla y Presupuesto --}}
                        <div class="col-12">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:#1e1b4b;">
                                <i class="fas fa-exclamation-triangle me-2" style="color:#a855f7;"></i>Falla y Presupuesto
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Falla Reportada por el Cliente <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('falla_reportada') is-invalid @enderror"
                                              name="falla_reportada" rows="4"
                                              placeholder="Describe exactamente qué problema reporta el cliente...">{{ old('falla_reportada') }}</textarea>
                                    @error('falla_reportada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Presupuesto Estimado ({{ $config->simbolo_moneda }})</label>
                                    <input type="number" class="form-control" name="presupuesto"
                                           value="{{ old('presupuesto', 0) }}" min="0" step="0.01">
                                    <div style="font-size:12px; color:#9ca3af; margin-top:4px;">
                                        Dejar en 0 si aún no se determinó
                                    </div>

                                    <label class="form-label mt-3">Notas Adicionales</label>
                                    <textarea class="form-control" name="notas" rows="4"
                                              placeholder="Accesorios recibidos, observaciones al recibir el equipo...">{{ old('notas') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('reparaciones.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Registrar Orden
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const clientesData = @json($clientesJson);

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
    buscadorCliente.value = '';
    buscadorCliente.style.display = 'none';
    clienteResultados.style.display = 'none';
    clienteResultados.innerHTML = '';
}

function quitarClienteSeleccionado() {
    clienteIdInput.value = '';
    clienteSeleccionado.style.display = 'none';
    buscadorCliente.style.display = 'block';
    buscadorCliente.value = '';
    buscadorCliente.focus();
}

buscadorCliente.addEventListener('input', filtrarClientes);
document.addEventListener('click', function (e) {
    if (!e.target.closest('#buscadorCliente') && !e.target.closest('#clienteResultados')) {
        clienteResultados.style.display = 'none';
    }
});

@if(old('cliente_id', request('cliente')))
seleccionarCliente({{ old('cliente_id', request('cliente')) }});
@endif
</script>
@endpush
