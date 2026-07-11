@extends($layout ?? 'layouts.app')
@section('title', 'Recibo de Abono '.$venta->numero_venta)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" style="color:#a855f7;">Ventas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ventas.show', $venta) }}" style="color:#a855f7;">{{ $venta->numero_venta }}</a></li>
    <li class="breadcrumb-item active">Recibo de Abono</li>
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
    <h4 class="mb-0 fw-bold">Recibo de Abono</h4>
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
                $mensajeReciboAbono = "Hola {$venta->cliente->nombre}, te saludamos de *" . ($config->nombre_tienda ?? 'la tienda') . "*"
                    . ". Aquí tienes el recibo de tu abono a la compra {$venta->numero_venta}: "
                    . URL::signedRoute('publico.abono.recibo', ['venta' => $venta->id, 'abono' => $abono->id]);
            @endphp
            <a href="{{ $venta->cliente->whatsappUrl($mensajeReciboAbono) }}" target="_blank" rel="noopener"
               class="btn px-4" style="background:#25D366; color:#fff;">
                <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
            </a>
            @endif
            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver a la Venta
            </a>
        @endif
    </div>
</div>

{{-- ============ FORMATO HOJA CARTA ============ --}}
<div class="row justify-content-center" id="reciboHoja">
    <div class="col-lg-8">
        <div class="card recibo">
            <div class="card-body p-4">

                {{-- Cabecera: negocio + abono --}}
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
                            <div style="font-size:12px; color:var(--text-muted-2);">Recibo de Abono a Crédito</div>
                            <div style="font-size:11px; color:var(--text-muted); line-height:1.5;">
                                @if($config->ruc) NIT: {{ $config->ruc }} @endif
                                @if($config->telefono) · Tel: {{ $config->telefono }} @endif
                                @if($config->direccion || $config->ciudad) <br>{{ $config->direccion }}{{ $config->direccion && $config->ciudad ? ', ' : '' }}{{ $config->ciudad }}{{ $config->departamento ? ' - '.$config->departamento : '' }} @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:20px; font-weight:700; color:#a855f7;">Abono #{{ $abono->id }}</div>
                        <div style="font-size:12px; color:var(--text-muted-2);">{{ $abono->fecha_abono->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                {{-- Venta relacionada y cliente --}}
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Venta</div>
                            <div class="recibo-value">{{ $venta->numero_venta }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Cliente</div>
                            <div class="recibo-value">{{ $venta->cliente->nombre_completo ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Método de Pago</div>
                            <div class="recibo-value">{{ $abono->metodoPago->nombre ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="recibo-box">
                            <div class="recibo-label">Registrado por</div>
                            <div class="recibo-value">{{ $abono->usuario->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                @if($abono->notas)
                <div class="mb-3 p-3 rounded-3" style="background:#f9fafb; font-size:12.5px; color:var(--text-muted);">
                    <i class="fas fa-sticky-note me-1"></i><strong>Notas:</strong> {{ $abono->notas }}
                </div>
                @endif

                {{-- Monto y saldo --}}
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background:#d1fae5;">
                            <div style="font-size:11px; color:#065f46; margin-bottom:2px;">MONTO ABONADO</div>
                            <div style="font-size:24px; font-weight:700; color:#059669;">
                                {{ $config->simbolo_moneda }} {{ number_format($abono->monto, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background:{{ $venta->saldo_pendiente > 0 ? '#fee2e2' : '#e0f2fe' }};">
                            <div style="font-size:11px; color:{{ $venta->saldo_pendiente > 0 ? '#991b1b' : '#0369a1' }}; margin-bottom:2px;">SALDO RESTANTE</div>
                            <div style="font-size:24px; font-weight:700; color:{{ $venta->saldo_pendiente > 0 ? '#dc2626' : '#0369a1' }};">
                                {{ $config->simbolo_moneda }} {{ number_format($venta->saldo_pendiente, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($venta->saldo_pendiente <= 0)
                <div class="mt-3 p-3 rounded-3 text-center" style="background:#d1fae5; color:#065f46; font-weight:600; font-size:13px;">
                    <i class="fas fa-check-circle me-1"></i>Crédito totalmente pagado
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
        <div class="t-center t-bold">RECIBO DE ABONO</div>
        <div>Abono: <span class="t-bold">#{{ $abono->id }}</span></div>
        <div>Venta: {{ $venta->numero_venta }}</div>
        <div>Fecha: {{ $abono->fecha_abono->format('d/m/Y H:i') }}</div>
        <div>Cliente: {{ $venta->cliente->nombre_completo ?? '—' }}</div>
        <div>Registrado por: {{ $abono->usuario->name ?? '—' }}</div>
        <hr>
        <div class="t-row t-bold t-total"><span>ABONADO</span><span>{{ $config->simbolo_moneda }} {{ number_format($abono->monto, 2) }}</span></div>
        <div class="t-row"><span>Saldo restante</span><span>{{ $config->simbolo_moneda }} {{ number_format($venta->saldo_pendiente, 2) }}</span></div>
        <hr>
        <div>Pago: {{ $abono->metodoPago->nombre ?? '—' }}</div>
        @if($abono->notas)
        <div>Notas: {{ $abono->notas }}</div>
        @endif
        @if($venta->saldo_pendiente <= 0)
        <hr>
        <div class="t-center t-bold">¡CRÉDITO TOTALMENTE PAGADO!</div>
        @endif
        <hr>
        <div class="t-center">¡Gracias por su pago!</div>
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
