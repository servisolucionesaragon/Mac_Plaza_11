@extends('layouts.app')
@section('title', 'Detalle Venta '.$venta->numero_venta)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" style="color:#a855f7;">Ventas</a></li>
    <li class="breadcrumb-item active">{{ $venta->numero_venta }}</li>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .breadcrumb, .btn-acciones, .page-content > .d-flex { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
    .ticket { box-shadow: none !important; border: 1px solid #ddd; }
}
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 btn-acciones">
    <div>
        <h4 class="mb-1 fw-bold">{{ $venta->numero_venta }}</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            {{ $venta->fecha_venta->format('d/m/Y H:i') }} ·
            Atendido por <strong>{{ $venta->vendedor->name ?? '—' }}</strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-primary px-4">
            <i class="fas fa-print me-2"></i>Imprimir
        </button>
        @if($venta->estado === 'completada')
        <form action="{{ route('ventas.cancelar', $venta) }}" method="POST"
              onsubmit="return confirm('¿Cancelar esta venta y restaurar el stock?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-outline-danger px-4">
                <i class="fas fa-ban me-2"></i>Cancelar Venta
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">
    {{-- Comprobante --}}
    <div class="col-lg-8">
        <div class="card ticket">
            <div class="card-body p-4">
                {{-- Cabecera del ticket --}}
                <div class="d-flex align-items-start justify-content-between mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div style="width:42px; height:42px; background:linear-gradient(135deg,#a855f7,#ec4899);
                                border-radius:10px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-mobile-alt" style="color:#fff;"></i>
                            </div>
                            <div>
                                <div style="font-weight:700; font-size:16px;">CRM Tienda Celulares</div>
                                <div style="font-size:12px; color:#9ca3af;">Comprobante de Venta</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:20px; font-weight:700; color:#a855f7;">{{ $venta->numero_venta }}</div>
                        <div style="font-size:12px; color:#9ca3af;">{{ $venta->fecha_venta->format('d/m/Y H:i') }}</div>
                        @php $cfg=['completada'=>['#d1fae5','#065f46'],'cancelada'=>['#fee2e2','#991b1b'],'pendiente'=>['#fef3c7','#92400e'],'devuelta'=>['#f3f4f6','#374151']]; $c=$cfg[$venta->estado]??['#f3f4f6','#374151']; @endphp
                        <span style="background:{{ $c[0] }}; color:{{ $c[1] }}; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600; display:inline-block; margin-top:4px;">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </div>
                </div>

                {{-- Datos del cliente --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#f8f5ff;">
                            <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">CLIENTE</div>
                            <div style="font-weight:600;">{{ $venta->cliente->nombre_completo ?? '—' }}</div>
                            <div style="font-size:12px; color:#6b7280;">{{ $venta->cliente->telefono ?? '' }}</div>
                            @if($venta->cliente->email)
                                <div style="font-size:12px; color:#6b7280;">{{ $venta->cliente->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#f8f5ff;">
                            <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">PAGO</div>
                            <div style="font-weight:600;">{{ $venta->metodoPago->nombre ?? '—' }}</div>
                            <div style="font-size:12px; color:#6b7280;">Vendedor: {{ $venta->vendedor->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Detalle de productos --}}
                <div class="table-responsive mb-4">
                    <table class="table mb-0" style="font-size:13.5px;">
                        <thead>
                            <tr style="border-bottom:2px solid #e9d5ff;">
                                <th style="padding:8px 0; color:#6b7280; font-size:12px; text-transform:uppercase;">Producto</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:12px; text-transform:uppercase; text-align:center;">Cant.</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:12px; text-transform:uppercase; text-align:right;">P. Unit.</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:12px; text-transform:uppercase; text-align:right;">Descto.</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:12px; text-transform:uppercase; text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $det)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:10px 0;">
                                    <div style="font-weight:500;">{{ $det->producto->nombre ?? '—' }}</div>
                                    @if($det->producto && $det->producto->marca)
                                        <div style="font-size:11px; color:#9ca3af;">{{ $det->producto->marca->nombre }}</div>
                                    @endif
                                    @if($det->imei_vendido)
                                        <div style="font-size:11px; color:#9ca3af;">IMEI: {{ $det->imei_vendido }}</div>
                                    @endif
                                </td>
                                <td style="padding:10px 0; text-align:center;">{{ $det->cantidad }}</td>
                                <td style="padding:10px 0; text-align:right;">{{ $config->simbolo_moneda }} {{ number_format($det->precio_unitario, 2) }}</td>
                                <td style="padding:10px 0; text-align:right; color:#dc2626;">
                                    {{ $det->descuento > 0 ? '— '.$config->simbolo_moneda.' '.number_format($det->descuento,2) : '—' }}
                                </td>
                                <td style="padding:10px 0; text-align:right; font-weight:600;">{{ $config->simbolo_moneda }} {{ number_format($det->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totales --}}
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                            <span class="text-muted">Subtotal</span>
                            <span>{{ $config->simbolo_moneda }} {{ number_format($venta->subtotal, 2) }}</span>
                        </div>
                        @if($venta->descuento > 0)
                        <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                            <span class="text-muted">Descuento</span>
                            <span class="text-danger">— {{ $config->simbolo_moneda }} {{ number_format($venta->descuento, 2) }}</span>
                        </div>
                        @endif
                        @if($venta->modo_precio !== 'sin_impuesto')
                        <div class="d-flex justify-content-between mb-1" style="font-size:13.5px;">
                            <span class="text-muted">Impuesto ({{ $config->igv }}%)</span>
                            <span>{{ $config->simbolo_moneda }} {{ number_format($venta->impuesto, 2) }}</span>
                        </div>
                        @endif
                        <div class="text-end mb-2" style="font-size:11px; color:#9ca3af;">
                            @php
                                $modoPrecioLabel = [
                                    'incluido'          => 'Precios con impuesto incluido',
                                    'sin_impuesto'       => 'Venta sin impuesto',
                                    'subtotal_impuesto' => 'Subtotal + impuesto',
                                ][$venta->modo_precio] ?? '';
                            @endphp
                            {{ $modoPrecioLabel }}
                        </div>
                        <div class="d-flex justify-content-between p-3 rounded-3"
                             style="background:linear-gradient(135deg,#a855f7,#ec4899);">
                            <span style="color:#fff; font-weight:700; font-size:16px;">TOTAL</span>
                            <span style="color:#fff; font-weight:700; font-size:20px;">{{ $config->simbolo_moneda }} {{ number_format($venta->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($venta->notas)
                <div class="mt-3 p-3 rounded-3" style="background:#f9fafb; font-size:13px; color:#6b7280;">
                    <i class="fas fa-sticky-note me-1"></i><strong>Notas:</strong> {{ $venta->notas }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Panel lateral --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Acciones Rápidas</h6>
                <div class="d-grid gap-2">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print me-2"></i>Imprimir Comprobante
                    </button>
                    <a href="{{ route('clientes.show', $venta->cliente_id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-user me-2"></i>Ver Perfil del Cliente
                    </a>
                    <a href="{{ route('ventas.create') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-plus me-2"></i>Nueva Venta
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Resumen</h6>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Productos</span>
                    <span class="fw-500">{{ $venta->detalles->count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Unidades</span>
                    <span class="fw-500">{{ $venta->detalles->sum('cantidad') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Método de Pago</span>
                    <span class="fw-500">{{ $venta->metodoPago->nombre ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Fecha</span>
                    <span class="fw-500">{{ $venta->fecha_venta->format('d/m/Y') }}</span>
                </div>
                <div class="d-flex justify-content-between" style="font-size:13px;">
                    <span class="text-muted">Hora</span>
                    <span class="fw-500">{{ $venta->fecha_venta->format('H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
