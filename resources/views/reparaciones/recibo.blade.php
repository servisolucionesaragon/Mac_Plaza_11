@extends($layout ?? 'layouts.app')
@section('title', 'Recibo '.$reparacion->numero_orden)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.index') }}" style="color:#a855f7;">Reparaciones</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.show', $reparacion) }}" style="color:#a855f7;">{{ $reparacion->numero_orden }}</a></li>
    <li class="breadcrumb-item active">Recibo</li>
@endsection

@push('styles')
<style id="printPageStyle">
@page { size: auto; margin: 10mm; }
</style>
<style>
@media print {
    .sidebar, .topbar, .breadcrumb, .btn-acciones { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
    .recibo { box-shadow: none !important; border: none !important; }
}
.recibo-box { background:#f9fafb; border-radius:10px; padding:10px 14px; }
.recibo-label { font-size:10.5px; color:var(--text-muted-2); text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
.recibo-value { font-size:13.5px; font-weight:600; color:var(--text-dark); }

.formato-toggle .btn.active { background:#a855f7; color:#fff; border-color:#a855f7; }

/* ---- Tirilla térmica 80mm ---- */
.recibo-tirilla-wrap { display:flex; justify-content:center; }
.recibo-tirilla { width:80mm; max-width:100%; font-family:'Courier New', Consolas, monospace; font-size:11.5px; color:#111; padding:6px 4px; }
.recibo-tirilla hr { border:none; border-top:1px dashed #111; margin:6px 0; }
.recibo-tirilla .t-center { text-align:center; }
.recibo-tirilla .t-bold { font-weight:700; }
.recibo-tirilla .t-row { display:flex; justify-content:space-between; gap:6px; }
.recibo-tirilla .t-total { font-size:14px; }

.d-none-recibo { display:none !important; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 btn-acciones flex-wrap gap-2">
    <h4 class="mb-0 fw-bold">Recibo de Reparación</h4>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="btn-group formato-toggle" role="group">
            <button type="button" id="btnFormatoHoja" class="btn btn-outline-secondary btn-sm" onclick="setFormatoRecibo('hoja')">
                <i class="fas fa-file me-1"></i>Hoja Carta
            </button>
            <button type="button" id="btnFormatoTirilla" class="btn btn-outline-secondary btn-sm" onclick="setFormatoRecibo('tirilla')">
                <i class="fas fa-receipt me-1"></i>Tirilla
            </button>
        </div>
        <button onclick="window.print()" class="btn btn-primary px-4">
            <i class="fas fa-print me-2"></i>Imprimir
        </button>
        @if(!($publico ?? false))
            @if($reparacion->cliente && $reparacion->cliente->numeroWhatsapp())
            @php
                $mensajeReciboRep = "Hola {$reparacion->cliente->nombre}, te saludamos de *" . ($config->nombre_tienda ?? 'la tienda') . "*"
                    . ". Aquí tienes el recibo de tu orden de reparación {$reparacion->numero_orden}: "
                    . URL::signedRoute('publico.reparacion.recibo', ['reparacion' => $reparacion->id]);
            @endphp
            <a href="{{ $reparacion->cliente->whatsappUrl($mensajeReciboRep) }}" target="_blank" rel="noopener"
               class="btn px-4" style="background:#25D366; color:#fff;">
                <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
            </a>
            @endif
            <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        @endif
    </div>
</div>

{{-- ============ FORMATO HOJA CARTA ============ --}}
<div class="row justify-content-center" id="reciboHoja">
    <div class="col-lg-9">
        <div class="card recibo">
            <div class="card-body p-4">

                {{-- Cabecera: negocio + orden --}}
                <div class="d-flex align-items-start justify-content-between mb-4 pb-3" style="border-bottom:2px solid #e9d5ff;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:70px; height:70px; border-radius:14px; overflow:hidden; background:linear-gradient(135deg,#a855f7,#ec4899); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            @if($config->logo)
                                <img src="{{ asset('storage/' . $config->logo) }}" alt="Logo" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <i class="fas fa-mobile-alt" style="color:#fff; font-size:30px;"></i>
                            @endif
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:17px;">{{ $config->nombre_tienda ?? 'CRM Celulares' }}</div>
                            <div style="font-size:12px; color:var(--text-muted-2);">Recibo de Orden de Reparación</div>
                            <div style="font-size:11px; color:var(--text-muted); line-height:1.5;">
                                @if($config->ruc) NIT: {{ $config->ruc }} @endif
                                @if($config->telefono) · Tel: {{ $config->telefono }} @endif
                                @if($config->direccion || $config->ciudad) <br>{{ $config->direccion }}{{ $config->direccion && $config->ciudad ? ', ' : '' }}{{ $config->ciudad }}{{ $config->departamento ? ' - '.$config->departamento : '' }} @endif
                                @if($config->email || $config->pagina_web) <br>{{ $config->email }}{{ $config->email && $config->pagina_web ? ' · ' : '' }}{{ $config->pagina_web }} @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:20px; font-weight:700; color:#a855f7;">{{ $reparacion->numero_orden }}</div>
                        <div style="font-size:12px; color:var(--text-muted-2);">Impreso: {{ now()->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                {{-- Cliente y Técnico --}}
                <div class="row g-3 mb-3">
                    <div class="col-8">
                        <div class="recibo-box">
                            <div class="recibo-label">Cliente</div>
                            <div class="recibo-value">{{ $reparacion->cliente->nombre_completo ?? '—' }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">
                                {{ $reparacion->cliente->telefono ?? '—' }}
                                @if($reparacion->cliente->direccion ?? null) · {{ $reparacion->cliente->direccion }} @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="recibo-box">
                            <div class="recibo-label">Técnico</div>
                            <div class="recibo-value">{{ $reparacion->tecnico->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Fechas --}}
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Fecha Recibido</div>
                            <div class="recibo-value">{{ optional($reparacion->fecha_recepcion)->format('d/m/Y H:i') ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-6">
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
                            <div class="col-3">
                                <div class="recibo-label">Dispositivo</div>
                                <div class="recibo-value">{{ $reparacion->dispositivo }}</div>
                            </div>
                            <div class="col-3">
                                <div class="recibo-label">Marca / Modelo</div>
                                <div class="recibo-value">{{ $reparacion->marca ?: '—' }} {{ $reparacion->modelo }}</div>
                            </div>
                            <div class="col-3">
                                <div class="recibo-label">Color</div>
                                <div class="recibo-value">{{ $reparacion->color ?: '—' }}</div>
                            </div>
                            <div class="col-3">
                                <div class="recibo-label">IMEI / Serie</div>
                                <div class="recibo-value">{{ $reparacion->imei ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Diagnóstico --}}
                <div class="mb-3">
                    <div class="recibo-box p-0" style="overflow:hidden;">
                        <div class="px-3 py-2" style="border-bottom:1px solid #e5e7eb;">
                            <div class="recibo-label mb-1">Falla Reportada por el Cliente</div>
                            <div style="font-size:13px; color:#374151;">{{ $reparacion->falla_reportada ?: '—' }}</div>
                        </div>
                        <div class="px-3 py-2" style="border-bottom:1px solid #e5e7eb;">
                            <div class="recibo-label mb-1">Diagnóstico Técnico</div>
                            <div style="font-size:13px; color:#374151;">{{ $reparacion->diagnostico ?: 'Pendiente de diagnóstico.' }}</div>
                        </div>
                        <div class="px-3 py-2">
                            <div class="recibo-label mb-1">Solución Aplicada</div>
                            <div style="font-size:13px; color:#374151;">{{ $reparacion->solucion ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Costo y garantía --}}
                <div class="row g-3 mb-3">
                    <div class="col-{{ $reparacion->garantia ? '6' : '12' }}">
                        <div class="p-3 rounded-3 text-center" style="background:#d1fae5;">
                            <div style="font-size:11px; color:#065f46; margin-bottom:2px;">COSTO FINAL</div>
                            <div style="font-size:24px; font-weight:700; color:#059669;">
                                {{ $config->simbolo_moneda }} {{ number_format($reparacion->costo_final, 2) }}
                            </div>
                        </div>
                    </div>
                    @if($reparacion->garantia)
                    @php
                        $fechaFinGarantia = ($reparacion->fecha_entrega ?? $reparacion->fecha_recepcion ?? now())
                            ->copy()->addDays($reparacion->dias_garantia);
                    @endphp
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background:#e0f2fe;">
                            <div style="font-size:11px; color:#0369a1; margin-bottom:2px;"><i class="fas fa-shield-alt me-1"></i>GARANTÍA INCLUIDA</div>
                            <div style="font-size:20px; font-weight:700; color:#0369a1;">{{ $reparacion->dias_garantia }} días</div>
                            <div style="font-size:11px; color:#0369a1;">Vence: {{ $fechaFinGarantia->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Firma de recibido --}}
                <div class="row g-3 mt-3 pt-2" style="border-top:1px solid #e5e7eb;">
                    <div class="col-6 offset-6">
                        <div style="border-top:1px solid #374151; margin-top:30px; padding-top:6px; text-align:center; font-size:12px; color:var(--text-muted);">
                            Firma de conformidad de recibido
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ============ FORMATO TIRILLA 80MM ============ --}}
<div class="recibo-tirilla-wrap d-none-recibo" id="reciboTirilla">
    <div class="recibo-tirilla">
        @if($config->logo)
        <div class="t-center mb-1">
            <img src="{{ asset('storage/' . $config->logo) }}" alt="Logo" style="max-width:120px; max-height:60px; object-fit:contain;">
        </div>
        @endif
        <div class="t-center t-bold" style="font-size:13px;">{{ $config->nombre_tienda ?? 'CRM Celulares' }}</div>
        @if($config->ruc)<div class="t-center">NIT: {{ $config->ruc }}</div>@endif
        @if($config->direccion || $config->ciudad)<div class="t-center">{{ $config->direccion }}{{ $config->direccion && $config->ciudad ? ', ' : '' }}{{ $config->ciudad }}</div>@endif
        @if($config->telefono)<div class="t-center">Tel: {{ $config->telefono }}</div>@endif
        <hr>
        <div class="t-center t-bold">RECIBO DE REPARACIÓN</div>
        <div>Orden: <span class="t-bold">{{ $reparacion->numero_orden }}</span></div>
        <div>Impreso: {{ now()->format('d/m/Y H:i') }}</div>
        <div>Cliente: {{ $reparacion->cliente->nombre_completo ?? '—' }}</div>
        <div>Técnico: {{ $reparacion->tecnico->name ?? '—' }}</div>
        <hr>
        <div>Recibido: {{ optional($reparacion->fecha_recepcion)->format('d/m/Y H:i') ?? '—' }}</div>
        <div>Entregado: {{ optional($reparacion->fecha_entrega)->format('d/m/Y H:i') ?? 'Pendiente' }}</div>
        <hr>
        <div>Dispositivo: {{ $reparacion->dispositivo }}</div>
        <div>Marca/Modelo: {{ $reparacion->marca ?: '—' }} {{ $reparacion->modelo }}</div>
        <div>Color: {{ $reparacion->color ?: '—' }}</div>
        <div>IMEI/Serie: {{ $reparacion->imei ?: '—' }}</div>
        <hr>
        <div>Falla reportada:</div>
        <div>{{ $reparacion->falla_reportada ?: '—' }}</div>
        <div class="mt-1">Diagnóstico:</div>
        <div>{{ $reparacion->diagnostico ?: 'Pendiente de diagnóstico.' }}</div>
        @if($reparacion->solucion)
        <div class="mt-1">Solución:</div>
        <div>{{ $reparacion->solucion }}</div>
        @endif
        <hr>
        <div class="t-row t-bold t-total"><span>COSTO FINAL</span><span>{{ $config->simbolo_moneda }} {{ number_format($reparacion->costo_final, 2) }}</span></div>
        @if($reparacion->garantia)
        @php
            $fechaFinGarantiaTirilla = ($reparacion->fecha_entrega ?? $reparacion->fecha_recepcion ?? now())
                ->copy()->addDays($reparacion->dias_garantia);
        @endphp
        <div class="t-row"><span>Garantía</span><span>{{ $reparacion->dias_garantia }} días</span></div>
        <div class="t-row"><span>Vence</span><span>{{ $fechaFinGarantiaTirilla->format('d/m/Y') }}</span></div>
        @endif
        <hr>
        <div class="t-center" style="margin-top:20px;">_____________________________</div>
        <div class="t-center">Firma de conformidad de recibido</div>
    </div>
</div>

<script>
let formatoActual = 'hoja';

function actualizarAltoTirilla() {
    if (formatoActual !== 'tirilla') return;
    const pageStyle = document.getElementById('printPageStyle');
    const contenido = document.querySelector('#reciboTirilla .recibo-tirilla');
    const alturaPx = contenido.getBoundingClientRect().height;
    const alturaMm = Math.ceil(alturaPx / 3.7795275591) + 10;
    pageStyle.textContent = '@page { size: 80mm ' + alturaMm + 'mm; margin: 2mm 3mm; }';
}

function setFormatoRecibo(formato) {
    const hoja = document.getElementById('reciboHoja');
    const tirilla = document.getElementById('reciboTirilla');
    const btnHoja = document.getElementById('btnFormatoHoja');
    const btnTirilla = document.getElementById('btnFormatoTirilla');
    const pageStyle = document.getElementById('printPageStyle');

    formatoActual = formato;

    if (formato === 'tirilla') {
        hoja.classList.add('d-none-recibo');
        tirilla.classList.remove('d-none-recibo');
        btnHoja.classList.remove('active');
        btnTirilla.classList.add('active');
        actualizarAltoTirilla();
    } else {
        hoja.classList.remove('d-none-recibo');
        tirilla.classList.add('d-none-recibo');
        btnHoja.classList.add('active');
        btnTirilla.classList.remove('active');
        pageStyle.textContent = '@page { size: auto; margin: 10mm; }';
    }
    localStorage.setItem('recibo_venta_formato', formato);
}

window.addEventListener('beforeprint', actualizarAltoTirilla);

document.addEventListener('DOMContentLoaded', function () {
    setFormatoRecibo(localStorage.getItem('recibo_venta_formato') || 'hoja');
});
</script>
@endsection
