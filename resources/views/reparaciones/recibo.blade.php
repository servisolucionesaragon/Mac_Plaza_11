@extends('layouts.app')
@section('title', 'Recibo '.$reparacion->numero_orden)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.index') }}" style="color:#a855f7;">Reparaciones</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.show', $reparacion) }}" style="color:#a855f7;">{{ $reparacion->numero_orden }}</a></li>
    <li class="breadcrumb-item active">Recibo</li>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .breadcrumb, .btn-acciones { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
    .recibo { box-shadow: none !important; border: none !important; }
    @page { size: auto; margin: 10mm; }
}
.recibo-box { background:#f9fafb; border-radius:10px; padding:10px 14px; }
.recibo-label { font-size:10.5px; color:#9ca3af; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
.recibo-value { font-size:13.5px; font-weight:600; color:#1e1b4b; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 btn-acciones">
    <h4 class="mb-0 fw-bold">Recibo de Reparación</h4>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary px-4">
            <i class="fas fa-print me-2"></i>Imprimir
        </button>
        <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card recibo">
            <div class="card-body p-4">

                {{-- Cabecera: negocio + orden --}}
                <div class="d-flex align-items-start justify-content-between mb-4 pb-3" style="border-bottom:2px solid #e9d5ff;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:52px; height:52px; border-radius:12px; overflow:hidden; background:linear-gradient(135deg,#a855f7,#ec4899); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            @if($config->logo)
                                <img src="{{ asset('storage/' . $config->logo) }}" alt="Logo" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <i class="fas fa-mobile-alt" style="color:#fff; font-size:22px;"></i>
                            @endif
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:17px;">{{ $config->nombre_tienda ?? 'CRM Celulares' }}</div>
                            <div style="font-size:12px; color:#9ca3af;">Recibo de Orden de Reparación</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:20px; font-weight:700; color:#a855f7;">{{ $reparacion->numero_orden }}</div>
                        <div style="font-size:12px; color:#9ca3af;">Impreso: {{ now()->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                {{-- Cliente y Técnico --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Cliente</div>
                            <div class="recibo-value">{{ $reparacion->cliente->nombre_completo ?? '—' }}</div>
                            <div style="font-size:12px; color:#6b7280;">{{ $reparacion->cliente->telefono ?? '' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Técnico</div>
                            <div class="recibo-value">{{ $reparacion->tecnico->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Fechas --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Fecha Recibido</div>
                            <div class="recibo-value">{{ optional($reparacion->fecha_recepcion)->format('d/m/Y H:i') ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Fecha Entregado</div>
                            <div class="recibo-value">{{ optional($reparacion->fecha_entrega)->format('d/m/Y H:i') ?? 'Pendiente' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Datos del equipo --}}
                <div class="mb-3">
                    <div class="recibo-label mb-1">Datos del Equipo</div>
                    <div class="recibo-box">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="recibo-label">Dispositivo</div>
                                <div class="recibo-value">{{ $reparacion->dispositivo }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="recibo-label">Marca / Modelo</div>
                                <div class="recibo-value">{{ $reparacion->marca ?: '—' }} {{ $reparacion->modelo }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="recibo-label">Color</div>
                                <div class="recibo-value">{{ $reparacion->color ?: '—' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="recibo-label">IMEI / Serie</div>
                                <div class="recibo-value">{{ $reparacion->imei ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Diagnóstico --}}
                <div class="mb-3">
                    <div class="recibo-label mb-1">Diagnóstico</div>
                    <div class="recibo-box" style="font-size:13px; color:#374151;">
                        {{ $reparacion->diagnostico ?: 'Pendiente de diagnóstico.' }}
                    </div>
                </div>

                {{-- Costo y garantía --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-{{ $reparacion->garantia ? '6' : '12' }}">
                        <div class="p-3 rounded-3 text-center" style="background:#d1fae5;">
                            <div style="font-size:11px; color:#065f46; margin-bottom:2px;">COSTO FINAL</div>
                            <div style="font-size:24px; font-weight:700; color:#059669;">
                                {{ $config->simbolo_moneda }} {{ number_format($reparacion->costo_final, 2) }}
                            </div>
                        </div>
                    </div>
                    @if($reparacion->garantia)
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 text-center" style="background:#e0f2fe;">
                            <div style="font-size:11px; color:#0369a1; margin-bottom:2px;"><i class="fas fa-shield-alt me-1"></i>GARANTÍA INCLUIDA</div>
                            <div style="font-size:20px; font-weight:700; color:#0369a1;">{{ $reparacion->dias_garantia }} días</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Firma de recibido --}}
                <div class="row g-3 mt-4 pt-3" style="border-top:1px solid #e5e7eb;">
                    <div class="col-md-6 offset-md-6">
                        <div style="border-top:1px solid #374151; margin-top:50px; padding-top:6px; text-align:center; font-size:12px; color:#6b7280;">
                            Firma de conformidad de recibido
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
