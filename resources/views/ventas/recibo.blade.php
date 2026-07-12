@extends($layout ?? 'layouts.app')
@section('title', 'Recibo '.$venta->numero_venta)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" style="color:#a855f7;">Ventas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ventas.show', $venta) }}" style="color:#a855f7;">{{ $venta->numero_venta }}</a></li>
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
.recibo-label { font-size:10.5px; color:#9ca3af; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
.recibo-value { font-size:13.5px; font-weight:600; color:#1e1b4b; }

.formato-toggle .btn.active { background:#a855f7; color:#fff; border-color:#a855f7; }

/* ---- Tirilla térmica 80mm ---- */
.recibo-tirilla-wrap { display:flex; justify-content:center; }
.recibo-tirilla { width:80mm; max-width:100%; font-family:'Courier New', Consolas, monospace; font-size:11.5px; color:#111; padding:6px 4px; }
.recibo-tirilla hr { border:none; border-top:1px dashed #111; margin:6px 0; }
.recibo-tirilla .t-center { text-align:center; }
.recibo-tirilla .t-bold { font-weight:700; }
.recibo-tirilla .t-row { display:flex; justify-content:space-between; gap:6px; }
.recibo-tirilla .t-item-nombre { flex:1; }
.recibo-tirilla .t-total { font-size:14px; }

.d-none-recibo { display:none !important; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 btn-acciones flex-wrap gap-2">
    <h4 class="mb-0 fw-bold">Recibo de Venta</h4>
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
            @if($venta->cliente && $venta->cliente->numeroWhatsapp())
            @php
                $mensajeReciboVenta = "Hola {$venta->cliente->nombre}, te saludamos de *" . ($config->nombre_tienda ?? 'la tienda') . "*"
                    . ". Aquí tienes el recibo de tu compra {$venta->numero_venta}: "
                    . URL::signedRoute('publico.venta.recibo', ['venta' => $venta->id]);
            @endphp
            <a href="{{ $venta->cliente->whatsappUrl($mensajeReciboVenta) }}" target="_blank" rel="noopener"
               class="btn px-4" style="background:#25D366; color:#fff;">
                <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
            </a>
            @endif
            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-outline-secondary px-4">
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

                {{-- Cabecera: negocio + venta --}}
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
                            <div style="font-size:12px; color:#9ca3af;">Comprobante de Venta</div>
                            <div style="font-size:11px; color:#6b7280; line-height:1.5;">
                                @if($config->ruc) NIT: {{ $config->ruc }} @endif
                                @if($config->telefono) · Tel: {{ $config->telefono }} @endif
                                @if($config->direccion || $config->ciudad) <br>{{ $config->direccion }}{{ $config->direccion && $config->ciudad ? ', ' : '' }}{{ $config->ciudad }}{{ $config->departamento ? ' - '.$config->departamento : '' }} @endif
                                @if($config->email || $config->pagina_web) <br>{{ $config->email }}{{ $config->email && $config->pagina_web ? ' · ' : '' }}{{ $config->pagina_web }} @endif
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

                {{-- Cliente y método de pago --}}
                <div class="row g-3 mb-3">
                    <div class="col-8">
                        <div class="recibo-box">
                            <div class="recibo-label">Cliente</div>
                            <div class="recibo-value">{{ $venta->cliente->nombre_completo ?? '—' }}</div>
                            <div style="font-size:12px; color:#6b7280;">
                                {{ $venta->cliente->telefono ?? '—' }}
                                @if($venta->cliente->direccion ?? null) · {{ $venta->cliente->direccion }} @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="recibo-box">
                            <div class="recibo-label">Método de Pago</div>
                            <div class="recibo-value">{{ $venta->metodoPago->nombre ?? '—' }}</div>
                            <div style="font-size:11px; color:#6b7280;">Vendedor: {{ $venta->vendedor->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Detalle de productos --}}
                <div class="mb-3">
                    <div class="recibo-label mb-1">Productos</div>
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="border-bottom:2px solid #e9d5ff;">
                                <th style="padding:8px 0; color:#6b7280; font-size:11px; text-transform:uppercase;">Producto</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:11px; text-transform:uppercase; text-align:center;">Cant.</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:11px; text-transform:uppercase; text-align:right;">P. Unit.</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:11px; text-transform:uppercase; text-align:right;">Descto.</th>
                                <th style="padding:8px 0; color:#6b7280; font-size:11px; text-transform:uppercase; text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $det)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:8px 0;">
                                    <div style="font-weight:500;">{{ $det->producto->nombre ?? '—' }}</div>
                                    @if($det->producto && $det->producto->marca)
                                        <div style="font-size:10.5px; color:#9ca3af;">{{ $det->producto->marca->nombre }}</div>
                                    @endif
                                    @if($det->imei_vendido)
                                        <div style="font-size:10.5px; color:#9ca3af;">IMEI: {{ $det->imei_vendido }}</div>
                                    @endif
                                    @if($det->serial_vendido)
                                        <div style="font-size:10.5px; color:#9ca3af;">Serial: {{ $det->serial_vendido }}</div>
                                    @endif
                                </td>
                                <td style="padding:8px 0; text-align:center;">{{ $det->cantidad }}</td>
                                <td style="padding:8px 0; text-align:right;">{{ $config->simbolo_moneda }} {{ number_format($det->precio_unitario, 2) }}</td>
                                <td style="padding:8px 0; text-align:right; color:#dc2626;">
                                    {{ $det->descuento > 0 ? '— '.$config->simbolo_moneda.' '.number_format($det->descuento,2) : '—' }}
                                </td>
                                <td style="padding:8px 0; text-align:right; font-weight:600;">{{ $config->simbolo_moneda }} {{ number_format($det->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totales --}}
                <div class="row justify-content-end mb-3">
                    <div class="col-5">
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                            <span class="text-muted">Subtotal</span>
                            <span>{{ $config->simbolo_moneda }} {{ number_format($venta->subtotal, 2) }}</span>
                        </div>
                        @if($venta->descuento > 0)
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                            <span class="text-muted">Descuento</span>
                            <span class="text-danger">— {{ $config->simbolo_moneda }} {{ number_format($venta->descuento, 2) }}</span>
                        </div>
                        @endif
                        @if($venta->modo_precio !== 'sin_impuesto')
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                            <span class="text-muted">Impuesto ({{ $config->igv }}%)</span>
                            <span>{{ $config->simbolo_moneda }} {{ number_format($venta->impuesto, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between p-3 rounded-3 mt-2"
                             style="background:linear-gradient(135deg,#a855f7,#ec4899);">
                            <span style="color:#fff; font-weight:700; font-size:16px;">TOTAL</span>
                            <span style="color:#fff; font-weight:700; font-size:20px;">{{ $config->simbolo_moneda }} {{ number_format($venta->total, 2) }}</span>
                        </div>
                        @if($venta->es_credito)
                        <div class="d-flex justify-content-between mt-2" style="font-size:13px;">
                            <span class="text-muted">Saldo pendiente</span>
                            <span class="fw-bold" style="color:{{ $venta->saldo_pendiente > 0 ? '#dc2626' : '#059669' }};">
                                {{ $config->simbolo_moneda }} {{ number_format($venta->saldo_pendiente, 2) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:13px;">
                            <span class="text-muted">Fecha de vencimiento</span>
                            <span class="fw-bold">{{ optional($venta->fecha_vencimiento)->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($venta->notas)
                <div class="mb-3 p-3 rounded-3" style="background:#f9fafb; font-size:12.5px; color:#6b7280;">
                    <i class="fas fa-sticky-note me-1"></i><strong>Notas:</strong> {{ $venta->notas }}
                </div>
                @endif

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
        <div>Venta: <span class="t-bold">{{ $venta->numero_venta }}</span></div>
        <div>Fecha: {{ $venta->fecha_venta->format('d/m/Y H:i') }}</div>
        <div>Cliente: {{ $venta->cliente->nombre_completo ?? '—' }}</div>
        <div>Vendedor: {{ $venta->vendedor->name ?? '—' }}</div>
        <hr>
        @foreach($venta->detalles as $det)
        <div class="t-row">
            <span class="t-item-nombre">{{ $det->cantidad }}x {{ $det->producto->nombre ?? '—' }}</span>
        </div>
        <div class="t-row">
            <span>&nbsp;&nbsp;{{ $config->simbolo_moneda }} {{ number_format($det->precio_unitario, 2) }} c/u</span>
            <span class="t-bold">{{ $config->simbolo_moneda }} {{ number_format($det->subtotal, 2) }}</span>
        </div>
        @if($det->imei_vendido)
        <div style="font-size:10px;">&nbsp;&nbsp;IMEI: {{ $det->imei_vendido }}</div>
        @endif
        @if($det->serial_vendido)
        <div style="font-size:10px;">&nbsp;&nbsp;Serial: {{ $det->serial_vendido }}</div>
        @endif
        @endforeach
        <hr>
        <div class="t-row"><span>Subtotal</span><span>{{ $config->simbolo_moneda }} {{ number_format($venta->subtotal, 2) }}</span></div>
        @if($venta->descuento > 0)
        <div class="t-row"><span>Descuento</span><span>- {{ $config->simbolo_moneda }} {{ number_format($venta->descuento, 2) }}</span></div>
        @endif
        @if($venta->modo_precio !== 'sin_impuesto')
        <div class="t-row"><span>Impuesto ({{ $config->igv }}%)</span><span>{{ $config->simbolo_moneda }} {{ number_format($venta->impuesto, 2) }}</span></div>
        @endif
        <hr>
        <div class="t-row t-bold t-total"><span>TOTAL</span><span>{{ $config->simbolo_moneda }} {{ number_format($venta->total, 2) }}</span></div>
        @if($venta->es_credito)
        <div class="t-row"><span>Saldo pendiente</span><span>{{ $config->simbolo_moneda }} {{ number_format($venta->saldo_pendiente, 2) }}</span></div>
        <div class="t-row"><span>Vence</span><span>{{ optional($venta->fecha_vencimiento)->format('d/m/Y') ?? '—' }}</span></div>
        @endif
        <hr>
        <div>Pago: {{ $venta->metodoPago->nombre ?? '—' }}</div>
        @if($venta->notas)
        <div>Notas: {{ $venta->notas }}</div>
        @endif
        <hr>
        <div class="t-center">¡Gracias por su compra!</div>
        <div class="t-center" style="font-size:10px; color:#555;">{{ now()->format('d/m/Y H:i') }}</div>
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
