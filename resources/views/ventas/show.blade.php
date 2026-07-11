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
        <a href="{{ route('ventas.recibo', $venta) }}" class="btn btn-outline-primary px-4">
            <i class="fas fa-receipt me-2"></i>Recibo
        </a>
        @if(Auth::user()->esAdmin() && !in_array($venta->estado, ['cancelada', 'devuelta']))
        <a href="{{ route('ventas.edit', $venta) }}" class="btn btn-outline-primary px-4">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
        @endif
        @if($venta->estado === 'completada' && Auth::user()->esAdmin())
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
                                <div style="font-size:12px; color:var(--text-muted-2);">Comprobante de Venta</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:20px; font-weight:700; color:#a855f7;">{{ $venta->numero_venta }}</div>
                        <div style="font-size:12px; color:var(--text-muted-2);">{{ $venta->fecha_venta->format('d/m/Y H:i') }}</div>
                        @php $cfg=['completada'=>['#d1fae5','#065f46'],'cancelada'=>['#fee2e2','#991b1b'],'pendiente'=>['#fef3c7','#92400e'],'devuelta'=>['#f3f4f6','#374151']]; $c=$cfg[$venta->estado]??['#f3f4f6','#374151']; @endphp
                        <span style="background:{{ $c[0] }}; color:{{ $c[1] }}; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600; display:inline-block; margin-top:4px;">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </div>
                </div>

                {{-- Datos del cliente --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:var(--table-head-bg);">
                            <div style="font-size:11px; color:var(--text-muted-2); margin-bottom:4px;">CLIENTE</div>
                            <div style="font-weight:600;">{{ $venta->cliente->nombre_completo ?? '—' }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">{{ $venta->cliente->telefono ?? '' }}</div>
                            @if($venta->cliente->email)
                                <div style="font-size:12px; color:var(--text-muted);">{{ $venta->cliente->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:var(--table-head-bg);">
                            <div style="font-size:11px; color:var(--text-muted-2); margin-bottom:4px;">PAGO</div>
                            <div style="font-weight:600;">{{ $venta->metodoPago->nombre ?? '—' }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">Vendedor: {{ $venta->vendedor->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Detalle de productos --}}
                <div class="table-responsive mb-4">
                    <table class="table mb-0" style="font-size:13.5px;">
                        <thead>
                            <tr style="border-bottom:2px solid #e9d5ff;">
                                <th style="padding:8px 0; color:var(--text-muted); font-size:12px; text-transform:uppercase;">Producto</th>
                                <th style="padding:8px 0; color:var(--text-muted); font-size:12px; text-transform:uppercase; text-align:center;">Cant.</th>
                                <th style="padding:8px 0; color:var(--text-muted); font-size:12px; text-transform:uppercase; text-align:right;">P. Unit.</th>
                                <th style="padding:8px 0; color:var(--text-muted); font-size:12px; text-transform:uppercase; text-align:right;">Descto.</th>
                                <th style="padding:8px 0; color:var(--text-muted); font-size:12px; text-transform:uppercase; text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $det)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:10px 0;">
                                    <div style="font-weight:500;">{{ $det->producto->nombre ?? '—' }}</div>
                                    @if($det->producto && $det->producto->marca)
                                        <div style="font-size:11px; color:var(--text-muted-2);">{{ $det->producto->marca->nombre }}</div>
                                    @endif
                                    @if($det->imei_vendido)
                                        <div style="font-size:11px; color:var(--text-muted-2);">IMEI: {{ $det->imei_vendido }}</div>
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
                        <div class="text-end mb-2" style="font-size:11px; color:var(--text-muted-2);">
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
                <div class="mt-3 p-3 rounded-3" style="background:var(--input-bg); font-size:13px; color:var(--text-muted);">
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
                    <a href="{{ route('ventas.recibo', $venta) }}" class="btn btn-primary">
                        <i class="fas fa-receipt me-2"></i>Recibo
                    </a>
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

@if($venta->es_credito)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="fw-bold mb-0">Estado de Crédito</h6>
                    @if($venta->saldo_pendiente <= 0)
                        <span class="badge" style="background:#d1fae5; color:#065f46; font-size:12px; border-radius:20px; padding:5px 12px;">
                            <i class="fas fa-check-circle me-1"></i>Pagada
                        </span>
                    @elseif($venta->estaAtrasada())
                        <span class="badge" style="background:#fee2e2; color:#991b1b; font-size:12px; border-radius:20px; padding:5px 12px;">
                            <i class="fas fa-exclamation-circle me-1"></i>Atrasado
                        </span>
                    @else
                        <span class="badge" style="background:#fef3c7; color:#92400e; font-size:12px; border-radius:20px; padding:5px 12px;">
                            <i class="fas fa-clock me-1"></i>Al día
                        </span>
                    @endif
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="recibo-box p-3 rounded-3" style="background:var(--table-head-bg);">
                            <div style="font-size:11px; color:var(--text-muted-2);">SALDO PENDIENTE</div>
                            <div style="font-size:18px; font-weight:700; color:var(--text-dark);">
                                {{ $config->simbolo_moneda }} {{ number_format($venta->saldo_pendiente, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:var(--table-head-bg);">
                            <div style="font-size:11px; color:var(--text-muted-2);">FECHA DE VENCIMIENTO</div>
                            <div style="font-size:18px; font-weight:700; color:var(--text-dark);">
                                {{ optional($venta->fecha_vencimiento)->format('d/m/Y') ?? '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:var(--table-head-bg);">
                            <div style="font-size:11px; color:var(--text-muted-2);">TOTAL ABONADO</div>
                            <div style="font-size:18px; font-weight:700; color:var(--text-dark);">
                                {{ $config->simbolo_moneda }} {{ number_format($venta->total - $venta->saldo_pendiente, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-2" style="font-size:13px;">Historial de Abonos</h6>
                @if($venta->abonos->count())
                <div class="table-responsive mb-4">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="border-bottom:2px solid #e9d5ff;">
                                <th style="padding:8px 0; color:var(--text-muted); font-size:11px; text-transform:uppercase;">Fecha</th>
                                <th style="padding:8px 0; color:var(--text-muted); font-size:11px; text-transform:uppercase;">Método de Pago</th>
                                <th style="padding:8px 0; color:var(--text-muted); font-size:11px; text-transform:uppercase;">Registrado por</th>
                                <th style="padding:8px 0; color:var(--text-muted); font-size:11px; text-transform:uppercase; text-align:right;">Monto</th>
                                <th style="padding:8px 0;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->abonos->sortByDesc('fecha_abono') as $abono)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:8px 0;">{{ $abono->fecha_abono->format('d/m/Y H:i') }}</td>
                                <td style="padding:8px 0;">{{ $abono->metodoPago->nombre ?? '—' }}</td>
                                <td style="padding:8px 0;">{{ $abono->usuario->name ?? '—' }}</td>
                                <td style="padding:8px 0; text-align:right; font-weight:600;">{{ $config->simbolo_moneda }} {{ number_format($abono->monto, 2) }}</td>
                                <td style="padding:8px 0; text-align:right;">
                                    <a href="{{ route('ventas.abonos.recibo', [$venta, $abono]) }}" style="color:#a855f7; font-size:12px; text-decoration:none;">
                                        <i class="fas fa-receipt me-1"></i>Recibo
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3 text-muted mb-4" style="font-size:13px;">Aún no se han registrado abonos.</div>
                @endif

                @if($venta->saldo_pendiente > 0)
                <h6 class="fw-bold mb-2" style="font-size:13px;">Registrar Abono</h6>
                <form action="{{ route('ventas.abonos.store', $venta) }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Monto <span class="text-danger">*</span></label>
                            <input type="number" name="monto" class="form-control @error('monto') is-invalid @enderror"
                                   min="0.01" max="{{ $venta->saldo_pendiente }}" step="0.01" required>
                            @error('monto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago_id" class="form-select @error('metodo_pago_id') is-invalid @enderror" required>
                                <option value="">— Seleccionar —</option>
                                @foreach(\App\Models\MetodoPago::where('activo', true)->orderBy('nombre')->get() as $mp)
                                    <option value="{{ $mp->id }}">{{ $mp->nombre }}</option>
                                @endforeach
                            </select>
                            @error('metodo_pago_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Notas</label>
                            <input type="text" name="notas" class="form-control" placeholder="Opcional">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check me-2"></i>Registrar
                            </button>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
