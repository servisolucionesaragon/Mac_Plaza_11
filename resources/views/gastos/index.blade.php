@extends('layouts.app')
@section('title', 'Gastos')

@section('breadcrumb')
    <li class="breadcrumb-item active">Gastos</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Gastos</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            Total del período: <strong style="color:#dc2626;">{{ $config->simbolo_moneda }} {{ number_format($totalPeriodo, 2) }}</strong>
        </p>
    </div>
    @if($cajaAbierta)
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoGasto">
        <i class="fas fa-plus me-2"></i>Nuevo Gasto
    </button>
    @else
    <a href="{{ route('caja.index') }}" class="btn btn-warning px-4">
        <i class="fas fa-exclamation-triangle me-2"></i>Abrir Caja para Registrar Gastos
    </a>
    @endif
</div>

@unless($cajaAbierta)
<div class="card mb-4" style="border:1px solid #fde68a;background:#fffbeb;">
    <div class="card-body p-3" style="font-size:13.5px;color:#92400e;">
        <i class="fas fa-exclamation-triangle me-2"></i>No hay caja abierta. No se pueden registrar nuevos gastos hasta abrir la caja del día.
    </div>
</div>
@endunless

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-size:12px;">Desde</label>
                <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:12px;">Hasta</label>
                <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:12px;">Método de pago</label>
                <select class="form-select" name="metodo_pago_id">
                    <option value="">Todos</option>
                    @foreach($metodosPago as $m)
                        <option value="{{ $m->id }}" {{ request('metodo_pago_id')==$m->id?'selected':'' }}>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Fecha</th>
                        <th>Descripción</th>
                        <th>Método</th>
                        <th>Registrado por</th>
                        <th>Monto</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gastos as $gasto)
                    <tr>
                        <td class="ps-4" style="font-size:12px;color:#6b7280;">{{ $gasto->fecha_gasto->format('d/m/Y H:i') }}</td>
                        <td style="font-size:13.5px;">{{ $gasto->descripcion }}</td>
                        <td style="font-size:13px;">{{ $gasto->metodoPago->nombre ?? '—' }}</td>
                        <td style="font-size:13px;color:#6b7280;">{{ $gasto->usuario->name ?? '—' }}</td>
                        <td style="font-size:13.5px;font-weight:600;color:#dc2626;">
                            − {{ $config->simbolo_moneda }} {{ number_format($gasto->monto, 2) }}
                        </td>
                        <td class="text-end pe-4">
                            @if(Auth::user()->esAdmin())
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:4px 10px;"
                                        title="Editar gasto"
                                        onclick="abrirModalEditar({{ $gasto->id }}, @json($gasto->fecha_gasto->format('Y-m-d\TH:i')), @json($gasto->descripcion), {{ $gasto->monto }}, {{ $gasto->metodo_pago_id }}, @json($gasto->notas))">
                                    <i class="fas fa-edit" style="font-size:12px;"></i>
                                </button>
                                <form action="{{ route('gastos.destroy', $gasto) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este gasto? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px;">
                                        <i class="fas fa-trash" style="font-size:12px;"></i>
                                    </button>
                                </form>
                            </div>
                            @else
                                <span class="text-muted" style="font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4" style="font-size:13px;">
                            No hay gastos registrados en el período seleccionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $gastos->links() }}
</div>

<!-- ══════════ MODAL: Nuevo Gasto ══════════ -->
<div class="modal fade" id="modalNuevoGasto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-arrow-circle-down me-2" style="color:#dc2626;"></i>Nuevo Gasto
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('gastos.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Descripción <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}"
                                   placeholder="Ej: Compra de insumos de aseo" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                            <input type="number" name="monto" class="form-control" value="{{ old('monto') }}"
                                   min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago_id" class="form-select" required>
                                <option value="">— Seleccionar —</option>
                                @foreach($metodosPago as $m)
                                    <option value="{{ $m->id }}" {{ old('metodo_pago_id')==$m->id?'selected':'' }}>{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" class="form-control" rows="2">{{ old('notas') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Registrar Gasto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(Auth::user()->esAdmin())
<!-- ══════════ MODAL: Editar Gasto ══════════ -->
<div class="modal fade" id="modalEditarGasto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2" style="color:#dc2626;"></i>Editar Gasto
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarGasto" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Fecha</label>
                            <input type="datetime-local" name="fecha_gasto" id="editFecha" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion" id="editDescripcion" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                            <input type="number" name="monto" id="editMonto" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago_id" id="editMetodoPago" class="form-select" required>
                                @foreach($metodosPago as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" id="editNotas" class="form-control" rows="2"></textarea>
                        </div>
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
@endif

@endsection

@push('scripts')
<script>
const baseGastosUrl = '{{ url('/gastos') }}';

function abrirModalEditar(id, fecha, descripcion, monto, metodoPagoId, notas) {
    document.getElementById('editFecha').value = fecha;
    document.getElementById('editDescripcion').value = descripcion;
    document.getElementById('editMonto').value = monto;
    document.getElementById('editMetodoPago').value = metodoPagoId;
    document.getElementById('editNotas').value = notas || '';
    document.getElementById('formEditarGasto').action = baseGastosUrl + '/' + id;
    new bootstrap.Modal(document.getElementById('modalEditarGasto')).show();
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalNuevoGasto')).show();
    });
@endif
</script>
@endpush
