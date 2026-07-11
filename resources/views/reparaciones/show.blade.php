@extends('layouts.app')
@section('title', 'Orden '.$reparacion->numero_orden)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.index') }}" style="color:#a855f7;">Reparaciones</a></li>
    <li class="breadcrumb-item active">{{ $reparacion->numero_orden }}</li>
@endsection

@push('styles')
<style>
.timeline-item { position:relative; padding-left:28px; margin-bottom:20px; }
.timeline-item::before { content:''; position:absolute; left:8px; top:20px; bottom:-20px; width:2px; background:#e5e7eb; }
.timeline-item:last-child::before { display:none; }
.timeline-dot { position:absolute; left:0; top:6px; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
@media print {
    .sidebar,.topbar,.breadcrumb,.btn-acciones { display:none!important; }
    .main-wrapper { margin-left:0!important; }
    .page-content { padding:0!important; }
}
</style>
@endpush

@section('content')
@php
    $stColors = ['recibido'=>['#ede9fe','#6d28d9'],'en_diagnostico'=>['#e0f2fe','#0369a1'],'esperando_repuesto'=>['#fef9c3','#92400e'],'en_reparacion'=>['#dbeafe','#1d4ed8'],'listo'=>['#d1fae5','#065f46'],'entregado'=>['#f3f4f6','#374151'],'no_reparable'=>['#fee2e2','#991b1b']];
@endphp
<div class="d-flex align-items-center justify-content-between mb-4 btn-acciones">
    <div>
        <h4 class="mb-1 fw-bold">{{ $reparacion->numero_orden }}</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            Recibido el {{ optional($reparacion->fecha_recepcion)->format('d/m/Y H:i') ?? '—' }} ·
            Técnico: <strong>{{ $reparacion->tecnico->name ?? '—' }}</strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($reparacion->estado === 'listo' && $reparacion->cliente && $reparacion->cliente->numeroWhatsapp())
        @php
            $mensajeListo = "Hola {$reparacion->cliente->nombre}, te saludamos de *" . ($config->nombre_tienda ?? 'la tienda') . "*"
                . ". Tu equipo {$reparacion->dispositivo}" . ($reparacion->modelo ? " {$reparacion->modelo}" : '')
                . " (orden {$reparacion->numero_orden}) ya está listo para recoger. ¡Te esperamos!";
        @endphp
        <a href="{{ $reparacion->cliente->whatsappUrl($mensajeListo) }}" target="_blank" rel="noopener"
           class="btn px-4" style="background:#25D366; color:#fff;">
            <i class="fab fa-whatsapp me-2"></i>Avisar por WhatsApp
        </a>
        @endif
        <a href="{{ route('reparaciones.recibo', $reparacion) }}" class="btn btn-outline-primary px-4">
            <i class="fas fa-receipt me-2"></i>Recibo
        </a>
        <a href="{{ route('reparaciones.edit', $reparacion) }}" class="btn btn-primary px-4">
            <i class="fas fa-edit me-2"></i>Actualizar Estado
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Detalle principal --}}
    <div class="col-lg-8">
        {{-- Dispositivo --}}
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-mobile-alt me-2" style="color:#a855f7;"></i>Datos del Equipo</h6>
                <div class="row g-3" style="font-size:13.5px;">
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">DISPOSITIVO</span>
                        <strong>{{ $reparacion->dispositivo }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">MARCA / MODELO</span>
                        <strong>{{ $reparacion->marca ?: '—' }} {{ $reparacion->modelo }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block" style="font-size:11px;">COLOR</span>
                        <strong>{{ $reparacion->color ?: '—' }}</strong>
                    </div>
                    @if($reparacion->imei)
                    <div class="col-md-6">
                        <span class="text-muted d-block" style="font-size:11px;">IMEI / SERIE</span>
                        <strong>{{ $reparacion->imei }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Falla / Diagnóstico / Solución --}}
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-stethoscope me-2" style="color:#a855f7;"></i>Diagnóstico</h6>
                <div class="mb-3">
                    <div style="font-size:11px; color:var(--text-muted-2); margin-bottom:4px;">FALLA REPORTADA POR EL CLIENTE</div>
                    <div class="p-3 rounded-3" style="background:#fef3c7; font-size:13.5px;">
                        {{ $reparacion->falla_reportada }}
                    </div>
                </div>
                @if($reparacion->diagnostico)
                <div class="mb-3">
                    <div style="font-size:11px; color:var(--text-muted-2); margin-bottom:4px;">DIAGNÓSTICO TÉCNICO</div>
                    <div class="p-3 rounded-3" style="background:#e0f2fe; font-size:13.5px;">
                        {{ $reparacion->diagnostico }}
                    </div>
                </div>
                @endif
                @if($reparacion->solucion)
                <div>
                    <div style="font-size:11px; color:var(--text-muted-2); margin-bottom:4px;">SOLUCIÓN APLICADA</div>
                    <div class="p-3 rounded-3" style="background:#d1fae5; font-size:13.5px;">
                        {{ $reparacion->solucion }}
                    </div>
                </div>
                @endif
                @if($reparacion->notas)
                <div class="mt-3 p-3 rounded-3" style="background:#f9fafb; font-size:13px; color:var(--text-muted);">
                    <i class="fas fa-sticky-note me-1"></i>{{ $reparacion->notas }}
                </div>
                @endif
            </div>
        </div>

        {{-- Costos --}}
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-dollar-sign me-2" style="color:#a855f7;"></i>Costos</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#f8f5ff; text-align:center;">
                            <div style="font-size:11px; color:var(--text-muted-2); margin-bottom:4px;">PRESUPUESTO</div>
                            <div style="font-size:24px; font-weight:700; color:#7c3aed;">
                                {{ $config->simbolo_moneda }} {{ number_format($reparacion->presupuesto, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#d1fae5; text-align:center;">
                            <div style="font-size:11px; color:#065f46; margin-bottom:4px;">COSTO FINAL</div>
                            <div style="font-size:24px; font-weight:700; color:#059669;">
                                {{ $config->simbolo_moneda }} {{ number_format($reparacion->costo_final, 2) }}
                            </div>
                        </div>
                    </div>
                    @if($reparacion->garantia)
                    <div class="col-12">
                        <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:#e0f2fe;">
                            <i class="fas fa-shield-alt" style="color:#0369a1; font-size:20px;"></i>
                            <div>
                                <div style="font-weight:600; color:#0369a1;">Garantía incluida</div>
                                <div style="font-size:12px; color:#0369a1;">{{ $reparacion->dias_garantia }} días de garantía</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Historial de la orden --}}
        <div class="card mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-history me-2" style="color:#a855f7;"></i>Historial de la Orden</h6>
                @forelse($reparacion->historial as $h)
                    @php
                        $scH = $stColors[$h->estado_nuevo] ?? ['#f3f4f6','#374151'];
                    @endphp
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:{{ $scH[0] }};">
                            <i class="fas fa-circle" style="font-size:6px; color:{{ $scH[1] }};"></i>
                        </div>
                        <div style="font-size:12.5px;">
                            <div class="d-flex justify-content-between">
                                <strong>
                                    @if($h->estado_anterior)
                                        {{ str_replace('_',' ',ucfirst($h->estado_anterior)) }} → {{ str_replace('_',' ',ucfirst($h->estado_nuevo)) }}
                                    @else
                                        Orden creada — {{ str_replace('_',' ',ucfirst($h->estado_nuevo)) }}
                                    @endif
                                </strong>
                                <span class="text-muted">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-muted" style="font-size:11.5px;">{{ $h->usuario->name ?? 'Usuario eliminado' }}</div>
                            @if($h->nota)
                                <div class="mt-1 p-2 rounded-3" style="background:#f9fafb; color:#4b5563;">{{ $h->nota }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3" style="font-size:13px;">Sin historial todavía.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Panel lateral --}}
    <div class="col-lg-4">
        {{-- Estado y cliente --}}
        <div class="card mb-3">
            <div class="card-body p-4">
                {{-- Estado actual --}}
                @php
                    $sc = $stColors[$reparacion->estado] ?? ['#f3f4f6','#374151'];
                    $priCol = ['urgente'=>['#fee2e2','#991b1b','🔴'],'alta'=>['#ffedd5','#9a3412','🟠'],'media'=>['#fef9c3','#713f12','🟡'],'baja'=>['#d1fae5','#065f46','🟢']];
                    $pr = $priCol[$reparacion->prioridad] ?? ['#f3f4f6','#374151','⚪'];
                @endphp

                <div class="text-center mb-3">
                    <span style="background:{{ $sc[0] }}; color:{{ $sc[1] }}; border-radius:20px; padding:8px 20px; font-size:13px; font-weight:600; display:inline-block;">
                        {{ str_replace('_',' ',ucfirst($reparacion->estado)) }}
                    </span>
                </div>

                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Prioridad</span>
                    <span style="background:{{ $pr[0] }}; color:{{ $pr[1] }}; border-radius:20px; padding:2px 10px; font-size:12px;">
                        {{ $pr[2] }} {{ ucfirst($reparacion->prioridad) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Recibido</span>
                    <span>{{ optional($reparacion->fecha_recepcion)->format('d/m/Y') ?? '—' }}</span>
                </div>
                @if($reparacion->fecha_estimada)
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Fecha estimada</span>
                    <span>{{ $reparacion->fecha_estimada->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($reparacion->fecha_entrega)
                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                    <span class="text-muted">Entregado</span>
                    <span style="color:#059669; font-weight:600;">{{ $reparacion->fecha_entrega->format('d/m/Y') }}</span>
                </div>
                @endif

                <hr>
                <h6 class="fw-bold mb-2" style="font-size:13px;">Cliente</h6>
                <div style="font-weight:600; font-size:13.5px;">{{ $reparacion->cliente->nombre_completo ?? '—' }}</div>
                <div style="font-size:12px; color:var(--text-muted-2);">{{ $reparacion->cliente->telefono ?? '' }}</div>
                @if($reparacion->cliente->email)
                    <div style="font-size:12px; color:var(--text-muted-2);">{{ $reparacion->cliente->email }}</div>
                @endif

                <div class="mt-3 d-grid gap-2">
                    <a href="{{ route('reparaciones.edit', $reparacion) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Actualizar Estado
                    </a>
                    <a href="{{ route('clientes.show', $reparacion->cliente_id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-user me-1"></i>Ver Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
