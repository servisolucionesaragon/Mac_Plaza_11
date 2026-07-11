@extends('layouts.app')
@section('title', 'Ventas')

@section('breadcrumb')
    <li class="breadcrumb-item active">Ventas</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Ventas</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            Total del mes: <strong style="color:#a855f7;">{{ $config->simbolo_moneda }} {{ number_format($totalMes, 2) }}</strong>
        </p>
    </div>
    <a href="{{ route('ventas.create') }}" class="btn btn-primary px-4">
        <i class="fas fa-plus me-2"></i>Nueva Venta
    </a>
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <input type="text" class="form-control" name="buscar"
                       placeholder="N° venta o cliente..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="completada" {{ request('estado')=='completada'?'selected':'' }}>Completada</option>
                    <option value="pendiente"  {{ request('estado')=='pendiente'?'selected':'' }}>Pendiente</option>
                    <option value="cancelada"  {{ request('estado')=='cancelada'?'selected':'' }}>Cancelada</option>
                    <option value="devuelta"   {{ request('estado')=='devuelta'?'selected':'' }}>Devuelta</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="tipo_venta">
                    <option value="">Contado y Crédito</option>
                    <option value="credito" {{ request('tipo_venta')=='credito'?'selected':'' }}>Solo Crédito</option>
                    <option value="contado" {{ request('tipo_venta')=='contado'?'selected':'' }}>Solo Contado</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
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
                        <th class="ps-4">N° Venta</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                    <tr>
                        <td class="ps-4">
                            <span style="font-weight:600; color:#a855f7;">{{ $venta->numero_venta }}</span>
                        </td>
                        <td style="font-size:13px;">
                            <div>{{ $venta->cliente->nombre_completo ?? '—' }}</div>
                            <div style="font-size:11px; color:var(--text-muted-2);">{{ $venta->cliente->telefono ?? '' }}</div>
                        </td>
                        <td style="font-size:13px; color:var(--text-muted);">{{ $venta->vendedor->name ?? '—' }}</td>
                        <td style="font-size:12px;">
                            <div>{{ $venta->fecha_venta->format('d/m/Y') }}</div>
                            <div style="color:var(--text-muted-2);">{{ $venta->fecha_venta->format('H:i') }}</div>
                        </td>
                        <td>
                            <span style="font-size:12px;">
                                {{ $venta->metodoPago->nombre ?? '—' }}
                            </span>
                        </td>
                        <td style="font-weight:700; color:var(--text-dark);">
                            {{ $config->simbolo_moneda }} {{ number_format($venta->total, 2) }}
                        </td>
                        <td>
                            @php
                                $cfg = [
                                    'completada' => ['bg'=>'#d1fae5','color'=>'#065f46'],
                                    'pendiente'  => ['bg'=>'#fef3c7','color'=>'#92400e'],
                                    'cancelada'  => ['bg'=>'#fee2e2','color'=>'#991b1b'],
                                    'devuelta'   => ['bg'=>'#e5e7eb','color'=>'#374151'],
                                ];
                                $c = $cfg[$venta->estado] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                            @endphp
                            <span style="background:{{ $c['bg'] }}; color:{{ $c['color'] }};
                                border-radius:20px; padding:4px 10px; font-size:11px; font-weight:500;">
                                {{ ucfirst($venta->estado) }}
                            </span>
                            @if($venta->es_credito)
                                <div style="margin-top:4px;">
                                    <span style="background:#ede9fe; color:#7c3aed; border-radius:20px; padding:3px 8px; font-size:10.5px; font-weight:600;">
                                        <i class="fas fa-hand-holding-usd fa-xs"></i> Crédito
                                        @if($venta->saldo_pendiente > 0)
                                            · {{ $config->simbolo_moneda }} {{ number_format($venta->saldo_pendiente, 2) }} pend.
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('ventas.show', $venta) }}"
                                   class="btn btn-sm" style="background:#ede9fe; color:#7c3aed; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                @if(Auth::user()->esAdmin() && !in_array($venta->estado, ['cancelada', 'devuelta']))
                                <a href="{{ route('ventas.edit', $venta) }}"
                                   class="btn btn-sm" style="background:#e0f2fe; color:#0369a1; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-edit fa-sm"></i>
                                </a>
                                @endif
                                @if($venta->estado === 'completada' && Auth::user()->esAdmin())
                                <form action="{{ route('ventas.cancelar', $venta) }}" method="POST"
                                      onsubmit="return confirm('¿Cancelar esta venta?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm"
                                            style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:5px 10px;">
                                        <i class="fas fa-ban fa-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                            <p class="text-muted mb-2">No hay ventas registradas</p>
                            <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Registrar primera venta
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ventas->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px;">
                Mostrando {{ $ventas->firstItem() }}–{{ $ventas->lastItem() }} de {{ $ventas->total() }} ventas
            </span>
            {{ $ventas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
