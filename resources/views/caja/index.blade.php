@extends('layouts.app')
@section('title', 'Control de Caja')

@section('breadcrumb')
    <li class="breadcrumb-item active">Control de Caja</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Control de Caja</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Apertura y cierre diario de caja</p>
    </div>
    @if(!$cajaActual)
        <a href="{{ route('caja.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-door-open me-2"></i>Abrir Caja
        </a>
    @endif
</div>

@if($cajaActual)
    <div class="card mb-4" style="border:1px solid #bbf7d0;background:#f0fdf4;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span style="background:#22c55e;color:#fff;border-radius:20px;padding:3px 12px;font-size:12px;font-weight:600;">
                        <i class="fas fa-circle fa-xs me-1"></i>Caja Abierta
                    </span>
                    <span style="font-size:13px;color:#166534;">Desde {{ $cajaActual->fecha_apertura->format('d/m/Y H:i') }}</span>
                </div>
                <div style="font-size:13px;color:#374151;">
                    Fondo inicial: <strong>{{ $config->simbolo_moneda }} {{ number_format($cajaActual->monto_inicial, 2) }}</strong>
                    &nbsp;·&nbsp; Abierta por {{ $cajaActual->usuarioApertura->name ?? '—' }}
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('caja.show', $cajaActual) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-eye me-1"></i>Ver detalle
                </a>
                <a href="{{ route('caja.cierreForm', $cajaActual) }}" class="btn btn-danger">
                    <i class="fas fa-door-closed me-1"></i>Cerrar Caja
                </a>
            </div>
        </div>
    </div>
@else
    <div class="card mb-4" style="border:1px solid #fde68a;background:#fffbeb;">
        <div class="card-body p-4" style="font-size:13.5px;color:#92400e;">
            <i class="fas fa-exclamation-triangle me-2"></i>No hay caja abierta. Debes abrir la caja del día antes de registrar ventas, abonos, gastos o ingresos.
        </div>
    </div>
@endif

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
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="{{ route('caja.index') }}" class="btn btn-outline-secondary">
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
                        <th>Fondo Inicial</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cajas as $caja)
                    <tr>
                        <td class="ps-4" style="font-size:13px;font-weight:500;">{{ $caja->fecha->format('d/m/Y') }}</td>
                        <td style="font-size:13px;">{{ $config->simbolo_moneda }} {{ number_format($caja->monto_inicial, 2) }}</td>
                        <td style="font-size:12px;color:#6b7280;">
                            {{ $caja->fecha_apertura->format('H:i') }} — {{ $caja->usuarioApertura->name ?? '—' }}
                        </td>
                        <td style="font-size:12px;color:#6b7280;">
                            @if($caja->fecha_cierre)
                                {{ $caja->fecha_cierre->format('H:i') }} — {{ $caja->usuarioCierre->name ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $caja->estaAbierta() ? 'bg-success' : 'bg-secondary' }}"
                                  style="border-radius:20px;font-size:11px;padding:4px 10px;">
                                {{ $caja->estaAbierta() ? 'Abierta' : 'Cerrada' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('caja.show', $caja) }}" class="btn btn-sm"
                               style="background:#f3f4f6;color:#374151;border-radius:8px;padding:5px 10px;">
                                <i class="fas fa-eye fa-sm"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4" style="font-size:13px;">
                            Aún no se ha abierto ninguna caja.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $cajas->links() }}
</div>
@endsection
