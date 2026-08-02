@extends('layouts.app')
@section('title', 'Denominaciones')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('configuracion.index') }}" style="color:#a855f7;">Configuración</a></li>
    <li class="breadcrumb-item active">Denominaciones</li>
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

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e1b4b;">Denominaciones</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            Billetes y monedas usados para contar el efectivo al cerrar la caja
        </p>
    </div>
    <button class="btn btn-primary px-4" onclick="abrirModalNueva()">
        <i class="fas fa-plus me-2"></i>Nueva Denominación
    </button>
</div>

@foreach(['billete' => ['Billetes', $billetes, 'fa-money-bill-wave'], 'moneda' => ['Monedas', $monedas, 'fa-coins']] as $tipo => [$titulo, $items, $icono])
<div class="card mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="fas {{ $icono }} me-2" style="color:#a855f7;"></i>{{ $titulo }}</h6>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:100px;">Imagen</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $d)
                    <tr>
                        <td>
                            @if($d->imagen)
                                <img src="{{ asset('storage/' . $d->imagen) }}" alt="{{ $d->valor }}"
                                     style="width:80px;height:80px;object-fit:contain;border-radius:8px;background:#f9fafb;">
                            @else
                                <div style="width:80px;height:80px;border-radius:8px;background:#f9fafb;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas {{ $icono }}" style="color:#d1d5db;font-size:24px;"></i>
                                </div>
                            @endif
                        </td>
                        <td style="font-size:14px;font-weight:600;">{{ $config->simbolo_moneda }} {{ number_format($d->valor, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $d->activo ? 'bg-success' : 'bg-secondary' }}" style="border-radius:20px;font-size:11px;padding:4px 10px;">
                                {{ $d->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:4px 10px;"
                                        title="Editar"
                                        onclick="abrirModalEditar({{ $d->id }}, '{{ $d->tipo }}', {{ $d->valor }}, @json($d->imagen ? asset('storage/'.$d->imagen) : null))">
                                    <i class="fas fa-edit" style="font-size:12px;"></i>
                                </button>
                                <form action="{{ route('denominaciones.toggle', $d) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:4px 10px;"
                                            title="{{ $d->activo ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas {{ $d->activo ? 'fa-toggle-on' : 'fa-toggle-off' }}" style="font-size:12px;"></i>
                                    </button>
                                </form>
                                <form action="{{ route('denominaciones.destroy', $d) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta denominación?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px;">
                                        <i class="fas fa-trash" style="font-size:12px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3" style="font-size:13px;">
                            Sin {{ strtolower($titulo) }} registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

<!-- ══════════ MODAL: Nueva Denominación ══════════ -->
<div class="modal fade" id="modalNuevaDenominacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-plus me-2" style="color:#a855f7;"></i>Nueva Denominación
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('denominaciones.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select" required>
                                <option value="billete">Billete</option>
                                <option value="moneda">Moneda</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valor ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                            <input type="number" name="valor" class="form-control" min="1" step="1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*" onchange="previsualizar(this, 'previewNueva')">
                            <img id="previewNueva" style="display:none;max-width:100px;max-height:100px;object-fit:contain;margin-top:8px;border-radius:8px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Editar Denominación ══════════ -->
<div class="modal fade" id="modalEditarDenominacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2" style="color:#a855f7;"></i>Editar Denominación
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarDenominacion" action="" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" id="editTipo" class="form-select" required>
                                <option value="billete">Billete</option>
                                <option value="moneda">Moneda</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valor ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                            <input type="number" name="valor" id="editValor" class="form-control" min="1" step="1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Imagen (dejar vacío para no cambiarla)</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*" onchange="previsualizar(this, 'previewEditar')">
                            <img id="previewEditar" style="display:none;max-width:100px;max-height:100px;object-fit:contain;margin-top:8px;border-radius:8px;">
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

@endsection

@push('scripts')
<script>
const baseDenominacionesUrl = '{{ url('/denominaciones') }}';

function abrirModalNueva() {
    document.getElementById('previewNueva').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modalNuevaDenominacion')).show();
}

function abrirModalEditar(id, tipo, valor, imagenUrl) {
    document.getElementById('editTipo').value = tipo;
    document.getElementById('editValor').value = valor;
    document.getElementById('formEditarDenominacion').action = baseDenominacionesUrl + '/' + id;

    const preview = document.getElementById('previewEditar');
    if (imagenUrl) {
        preview.src = imagenUrl;
        preview.style.display = 'inline-block';
    } else {
        preview.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('modalEditarDenominacion')).show();
}

function previsualizar(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.style.display = 'inline-block';
    }
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalNuevaDenominacion')).show();
    });
@endif
</script>
@endpush
