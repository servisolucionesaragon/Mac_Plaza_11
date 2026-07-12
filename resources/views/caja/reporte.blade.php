@extends('layouts.app')
@section('title', 'Reporte de Cierre — ' . $caja->fecha->format('d/m/Y'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('caja.index') }}" style="color:#a855f7;">Control de Caja</a></li>
    <li class="breadcrumb-item"><a href="{{ route('caja.show', $caja) }}" style="color:#a855f7;">{{ $caja->fecha->format('d/m/Y') }}</a></li>
    <li class="breadcrumb-item active">Reporte</li>
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
    .reporte-box { box-shadow: none !important; border: none !important; }
    .reporte-seccion-print { margin-bottom: 8px !important; }
    .row.reporte-seccion-print { --bs-gutter-y: 6px; }
    .resumen-item { padding: 6px 10px !important; }
}
.formato-toggle .btn.active { background:#a855f7; color:#fff; border-color:#a855f7; }
.resumen-item { background:#f9fafb; border-radius:10px; padding:12px 14px; }
.resumen-label { font-size:10.5px; color:#9ca3af; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
.resumen-value { font-size:16px; font-weight:700; color:#1e1b4b; }

.reporte-tirilla-wrap { display:flex; justify-content:center; }
.reporte-tirilla { width:80mm; max-width:100%; font-family:'Courier New', Consolas, monospace; font-size:11.5px; color:#111; padding:6px 4px; }
.reporte-tirilla hr { border:none; border-top:1px dashed #111; margin:6px 0; }
.reporte-tirilla .t-center { text-align:center; }
.reporte-tirilla .t-bold { font-weight:700; }
.reporte-tirilla .t-row { display:flex; justify-content:space-between; gap:6px; }

.d-none-reporte { display:none !important; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 btn-acciones flex-wrap gap-2">
    <h4 class="mb-0 fw-bold">Reporte de Cierre de Caja</h4>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="btn-group formato-toggle" role="group">
            <button type="button" id="btnFormatoHoja" class="btn btn-outline-secondary btn-sm" onclick="setFormatoReporte('hoja')">
                <i class="fas fa-file me-1"></i>Hoja Carta
            </button>
            <button type="button" id="btnFormatoTirilla" class="btn btn-outline-secondary btn-sm" onclick="setFormatoReporte('tirilla')">
                <i class="fas fa-receipt me-1"></i>Tirilla
            </button>
        </div>
        <button onclick="window.print()" class="btn btn-primary px-4">
            <i class="fas fa-print me-2"></i>Imprimir
        </button>
        <a href="{{ route('caja.reportePdf', $caja) }}" class="btn px-4" style="background:#dc2626;color:#fff;">
            <i class="fas fa-file-pdf me-2"></i>Descargar PDF
        </a>
        <a href="{{ route('caja.show', $caja) }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

{{-- ============ FORMATO HOJA CARTA ============ --}}
<div class="row justify-content-center" id="reporteHoja">
    <div class="col-lg-10">
        <div class="card reporte-box">
            <div class="card-body p-4">

                <div class="d-flex align-items-start justify-content-between mb-4 pb-3" style="border-bottom:2px solid #e9d5ff;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:60px;height:60px;border-radius:14px;overflow:hidden;background:linear-gradient(135deg,#a855f7,#ec4899);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            @if($config->logo)
                                <img src="{{ asset('storage/' . $config->logo) }}" alt="Logo" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="fas fa-mobile-alt" style="color:#fff;font-size:26px;"></i>
                            @endif
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:16px;">{{ $config->nombre_tienda ?? 'CRM Celulares' }}</div>
                            <div style="font-size:12px;color:#9ca3af;">Reporte de Cierre de Caja</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:18px;font-weight:700;color:#a855f7;">{{ $caja->fecha->format('d/m/Y') }}</div>
                        <span class="badge {{ $caja->estaAbierta() ? 'bg-success' : 'bg-secondary' }}" style="border-radius:20px;font-size:11px;padding:4px 10px;">
                            {{ $caja->estaAbierta() ? 'Abierta' : 'Cerrada' }}
                        </span>
                    </div>
                </div>

                <div class="row g-2 mb-3" style="font-size:12px;color:#6b7280;">
                    <div class="col-6">Abierta por: <strong>{{ $caja->usuarioApertura->name ?? '—' }}</strong> ({{ $caja->fecha_apertura->format('d/m/Y H:i') }})</div>
                    <div class="col-6 text-end">
                        @if($caja->fecha_cierre)
                            Cerrada por: <strong>{{ $caja->usuarioCierre->name ?? '—' }}</strong> ({{ $caja->fecha_cierre->format('d/m/Y H:i') }})
                        @endif
                    </div>
                </div>

                {{-- Resumen General --}}
                <div class="row g-3 mb-4 reporte-seccion-print">
                    <div class="col-4">
                        <div class="resumen-item">
                            <div class="resumen-label">Fondo Inicial</div>
                            <div class="resumen-value">{{ $config->simbolo_moneda }} {{ number_format($caja->monto_inicial, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="resumen-item">
                            <div class="resumen-label">Ventas de Contado ({{ $cantidadVentasContado }})</div>
                            <div class="resumen-value">{{ $config->simbolo_moneda }} {{ number_format($totalVentasContado, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="resumen-item">
                            <div class="resumen-label">Descuentos Aplicados</div>
                            <div class="resumen-value" style="color:#dc2626;">− {{ $config->simbolo_moneda }} {{ number_format($totalDescuentos, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="resumen-item">
                            <div class="resumen-label">Abonos de Crédito Cobrados</div>
                            <div class="resumen-value">{{ $config->simbolo_moneda }} {{ number_format($totalAbonos, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="resumen-item">
                            <div class="resumen-label">Ingresos</div>
                            <div class="resumen-value" style="color:#16a34a;">+ {{ $config->simbolo_moneda }} {{ number_format($totalIngresos, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="resumen-item">
                            <div class="resumen-label">Gastos</div>
                            <div class="resumen-value" style="color:#dc2626;">− {{ $config->simbolo_moneda }} {{ number_format($totalGastos, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-4 reporte-seccion-print"
                     style="background:linear-gradient(135deg,#a855f7,#ec4899);">
                    <span style="color:#fff;font-weight:700;font-size:16px;">TOTAL QUE DEBE HABER EN CAJA</span>
                    <span style="color:#fff;font-weight:700;font-size:22px;">{{ $config->simbolo_moneda }} {{ number_format($totalEsperado, 2) }}</span>
                </div>

                {{-- Desglose por método de pago --}}
                <div class="mb-4 reporte-seccion-print">
                    <div class="resumen-label mb-2">Desglose por Método de Pago</div>
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="border-bottom:2px solid #e9d5ff;">
                                <th style="padding:8px 0;color:#6b7280;font-size:11px;text-transform:uppercase;">Método</th>
                                <th style="padding:8px 0;color:#6b7280;font-size:11px;text-transform:uppercase;text-align:right;">Esperado</th>
                                @if(!$caja->estaAbierta())
                                    <th style="padding:8px 0;color:#6b7280;font-size:11px;text-transform:uppercase;text-align:right;">Contado</th>
                                    <th style="padding:8px 0;color:#6b7280;font-size:11px;text-transform:uppercase;text-align:right;">Diferencia</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($esperadoPorMetodo as $fila)
                                @php
                                    $conteo = $caja->conteos->firstWhere('metodo_pago_id', $fila['metodo_pago_id']);
                                    $contado = $conteo->monto_contado ?? null;
                                    $diferencia = $contado !== null ? $contado - $fila['esperado'] : null;
                                @endphp
                                <tr style="border-bottom:1px solid #f3f4f6;">
                                    <td style="padding:8px 0;font-weight:500;">{{ $fila['nombre'] }}</td>
                                    <td style="padding:8px 0;text-align:right;">{{ $config->simbolo_moneda }} {{ number_format($fila['esperado'], 2) }}</td>
                                    @if(!$caja->estaAbierta())
                                        <td style="padding:8px 0;text-align:right;">{{ $config->simbolo_moneda }} {{ number_format($contado ?? 0, 2) }}</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:{{ $diferencia < 0 ? '#dc2626' : ($diferencia > 0 ? '#16a34a' : '#6b7280') }};">
                                            {{ $diferencia >= 0 ? '+' : '' }}{{ number_format($diferencia ?? 0, 2) }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($gastosDelDia->isNotEmpty())
                <div class="mb-4">
                    <div class="resumen-label mb-2">Detalle de Gastos</div>
                    <table class="table mb-0" style="font-size:12.5px;">
                        <thead>
                            <tr style="border-bottom:2px solid #e9d5ff;">
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;">Hora</th>
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;">Descripción</th>
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;">Método</th>
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;text-align:right;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gastosDelDia as $gasto)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:6px 0;">{{ $gasto->fecha_gasto->format('H:i') }}</td>
                                <td style="padding:6px 0;">{{ $gasto->descripcion }}</td>
                                <td style="padding:6px 0;">{{ $gasto->metodoPago->nombre ?? '—' }}</td>
                                <td style="padding:6px 0;text-align:right;color:#dc2626;">− {{ $config->simbolo_moneda }} {{ number_format($gasto->monto, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($ingresosDelDia->isNotEmpty())
                <div class="mb-4">
                    <div class="resumen-label mb-2">Detalle de Ingresos</div>
                    <table class="table mb-0" style="font-size:12.5px;">
                        <thead>
                            <tr style="border-bottom:2px solid #e9d5ff;">
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;">Hora</th>
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;">Descripción</th>
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;">Método</th>
                                <th style="padding:6px 0;color:#6b7280;font-size:10.5px;text-transform:uppercase;text-align:right;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ingresosDelDia as $ingreso)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:6px 0;">{{ $ingreso->fecha_ingreso->format('H:i') }}</td>
                                <td style="padding:6px 0;">{{ $ingreso->descripcion }}</td>
                                <td style="padding:6px 0;">{{ $ingreso->metodoPago->nombre ?? '—' }}</td>
                                <td style="padding:6px 0;text-align:right;color:#16a34a;">+ {{ $config->simbolo_moneda }} {{ number_format($ingreso->monto, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($caja->notas_apertura || $caja->notas_cierre)
                <div class="p-3 rounded-3" style="background:#f9fafb;font-size:12.5px;color:#6b7280;">
                    @if($caja->notas_apertura)<div><strong>Notas de apertura:</strong> {{ $caja->notas_apertura }}</div>@endif
                    @if($caja->notas_cierre)<div><strong>Notas de cierre:</strong> {{ $caja->notas_cierre }}</div>@endif
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- ============ FORMATO TIRILLA 80MM ============ --}}
<div class="reporte-tirilla-wrap d-none-reporte" id="reporteTirilla">
    <div class="reporte-tirilla">
        @if($config->logo)
        <div class="t-center mb-1">
            <img src="{{ asset('storage/' . $config->logo) }}" alt="Logo" style="max-width:120px; max-height:60px; object-fit:contain;">
        </div>
        @endif
        <div class="t-center t-bold" style="font-size:13px;">{{ $config->nombre_tienda ?? 'CRM Celulares' }}</div>
        <div class="t-center">Reporte de Cierre de Caja</div>
        <div class="t-center">{{ $caja->fecha->format('d/m/Y') }}</div>
        <hr>
        <div>Abrió: {{ $caja->usuarioApertura->name ?? '—' }}</div>
        @if($caja->fecha_cierre)<div>Cerró: {{ $caja->usuarioCierre->name ?? '—' }}</div>@endif
        <hr>
        <div class="t-row"><span>Fondo Inicial</span><span>{{ $config->simbolo_moneda }} {{ number_format($caja->monto_inicial, 2) }}</span></div>
        <div class="t-row"><span>Ventas Contado ({{ $cantidadVentasContado }})</span><span>{{ $config->simbolo_moneda }} {{ number_format($totalVentasContado, 2) }}</span></div>
        <div class="t-row"><span>Descuentos</span><span>- {{ $config->simbolo_moneda }} {{ number_format($totalDescuentos, 2) }}</span></div>
        <div class="t-row"><span>Abonos Cobrados</span><span>{{ $config->simbolo_moneda }} {{ number_format($totalAbonos, 2) }}</span></div>
        <div class="t-row"><span>Ingresos</span><span>+ {{ $config->simbolo_moneda }} {{ number_format($totalIngresos, 2) }}</span></div>
        <div class="t-row"><span>Gastos</span><span>- {{ $config->simbolo_moneda }} {{ number_format($totalGastos, 2) }}</span></div>
        <hr>
        <div class="t-row t-bold" style="font-size:13px;"><span>TOTAL EN CAJA</span><span>{{ $config->simbolo_moneda }} {{ number_format($totalEsperado, 2) }}</span></div>
        <hr>
        <div class="t-bold">Por Método de Pago</div>
        @foreach($esperadoPorMetodo as $fila)
            @php
                $conteo = $caja->conteos->firstWhere('metodo_pago_id', $fila['metodo_pago_id']);
                $contado = $conteo->monto_contado ?? null;
            @endphp
            <div class="t-row"><span>{{ $fila['nombre'] }}</span><span>{{ $config->simbolo_moneda }} {{ number_format($fila['esperado'], 2) }}</span></div>
            @if(!$caja->estaAbierta())
                <div class="t-row"><span>&nbsp;&nbsp;Contado</span><span>{{ $config->simbolo_moneda }} {{ number_format($contado ?? 0, 2) }}</span></div>
            @endif
        @endforeach
        @if($gastosDelDia->isNotEmpty())
        <hr>
        <div class="t-bold">Gastos</div>
        @foreach($gastosDelDia as $gasto)
        <div class="t-row"><span>{{ $gasto->descripcion }}</span><span>-{{ number_format($gasto->monto, 2) }}</span></div>
        @endforeach
        @endif
        @if($ingresosDelDia->isNotEmpty())
        <hr>
        <div class="t-bold">Ingresos</div>
        @foreach($ingresosDelDia as $ingreso)
        <div class="t-row"><span>{{ $ingreso->descripcion }}</span><span>+{{ number_format($ingreso->monto, 2) }}</span></div>
        @endforeach
        @endif
        <hr>
        <div class="t-center" style="font-size:10px;color:#555;">Generado {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<script>
let formatoActual = 'hoja';

function actualizarAltoTirilla() {
    if (formatoActual !== 'tirilla') return;
    const pageStyle = document.getElementById('printPageStyle');
    const contenido = document.querySelector('#reporteTirilla .reporte-tirilla');
    const alturaPx = contenido.getBoundingClientRect().height;
    const alturaMm = Math.ceil(alturaPx / 3.7795275591) + 10;
    pageStyle.textContent = '@page { size: 80mm ' + alturaMm + 'mm; margin: 2mm 3mm; }';
}

function setFormatoReporte(formato) {
    const hoja = document.getElementById('reporteHoja');
    const tirilla = document.getElementById('reporteTirilla');
    const btnHoja = document.getElementById('btnFormatoHoja');
    const btnTirilla = document.getElementById('btnFormatoTirilla');
    const pageStyle = document.getElementById('printPageStyle');

    formatoActual = formato;

    if (formato === 'tirilla') {
        hoja.classList.add('d-none-reporte');
        tirilla.classList.remove('d-none-reporte');
        btnHoja.classList.remove('active');
        btnTirilla.classList.add('active');
        actualizarAltoTirilla();
    } else {
        hoja.classList.remove('d-none-reporte');
        tirilla.classList.add('d-none-reporte');
        btnHoja.classList.add('active');
        btnTirilla.classList.remove('active');
        pageStyle.textContent = '@page { size: auto; margin: 10mm; }';
    }
    localStorage.setItem('reporte_caja_formato', formato);
}

window.addEventListener('beforeprint', actualizarAltoTirilla);

document.addEventListener('DOMContentLoaded', function () {
    setFormatoReporte(localStorage.getItem('reporte_caja_formato') || 'hoja');
});
</script>
@endsection
