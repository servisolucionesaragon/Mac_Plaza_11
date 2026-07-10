@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $paletaGraficos = [$config->color_grafico_1, $config->color_grafico_2, $config->color_grafico_3];
@endphp

{{-- ── Encabezado ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-700" style="font-weight:700;">Panel de Control</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('ventas.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus me-2"></i>Nueva Venta
        </a>
        <a href="{{ route('reparaciones.create') }}" class="btn btn-outline-primary px-4">
            <i class="fas fa-tools me-2"></i>Nueva Reparación
        </a>
    </div>
</div>

{{-- ── KPI Cards ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-purple">
            <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="kpi-value">{{ $config->simbolo_moneda }} {{ number_format($ventasHoy, 0) }}</div>
            <div class="kpi-label">Ventas de Hoy</div>
            <span class="kpi-badge">
                <i class="fas fa-calendar-day fa-xs"></i>
                {{ $config->simbolo_moneda }} {{ number_format($ventasMes, 0) }} este mes
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-pink">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            <div class="kpi-value">{{ number_format($totalClientes) }}</div>
            <div class="kpi-label">Clientes Registrados</div>
            <span class="kpi-badge">
                <i class="fas fa-user-plus fa-xs"></i>
                +{{ $clientesNuevosMes }} este mes
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-cyan">
            <div class="kpi-icon"><i class="fas fa-box"></i></div>
            <div class="kpi-value">{{ number_format($totalProductos) }}</div>
            <div class="kpi-label">Productos en Stock</div>
            <span class="kpi-badge">
                @if($stockBajo > 0)
                    <i class="fas fa-exclamation-triangle fa-xs"></i>
                    {{ $stockBajo }} con stock bajo
                @else
                    <i class="fas fa-check fa-xs"></i>
                    Stock óptimo
                @endif
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-green">
            <div class="kpi-icon"><i class="fas fa-tools"></i></div>
            <div class="kpi-value">{{ $reparacionesPendientes }}</div>
            <div class="kpi-label">Reparaciones Activas</div>
            <span class="kpi-badge">
                <i class="fas fa-check-circle fa-xs"></i>
                {{ $reparacionesListas }} listas para entregar
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-orange">
            <div class="kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="kpi-value">{{ $config->simbolo_moneda }} {{ number_format($carteraPendiente, 0) }}</div>
            <div class="kpi-label">Cartera Pendiente</div>
            <span class="kpi-badge">
                @if($carteraAtrasadaCount > 0)
                    <i class="fas fa-exclamation-triangle fa-xs"></i>
                    {{ $carteraAtrasadaCount }} atrasado(s)
                @else
                    <i class="fas fa-check fa-xs"></i>
                    Sin atrasos
                @endif
            </span>
        </div>
    </div>
</div>

{{-- ── Gráficas principales ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Ventas por día --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="mb-0 fw-600" style="font-weight:600;">Ventas — Últimos 7 días</h6>
                        <span class="text-muted" style="font-size:12px;">Ingresos diarios en soles</span>
                    </div>
                    @if($crecimientoVentas >= 0)
                        <span class="badge" style="background:#d1fae5; color:#065f46; font-size:12px; border-radius:20px; padding:5px 12px;">
                            <i class="fas fa-arrow-up fa-xs"></i> {{ number_format(abs($crecimientoVentas), 1) }}% vs mes anterior
                        </span>
                    @else
                        <span class="badge" style="background:#fee2e2; color:#991b1b; font-size:12px; border-radius:20px; padding:5px 12px;">
                            <i class="fas fa-arrow-down fa-xs"></i> {{ number_format(abs($crecimientoVentas), 1) }}% vs mes anterior
                        </span>
                    @endif
                </div>
                <canvas id="chartVentasDias" height="90"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Productos --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="mb-1 fw-600" style="font-weight:600;">Top Productos</h6>
                <p class="text-muted mb-3" style="font-size:12px;">Más vendidos este mes</p>

                @forelse($topProductos as $i => $prod)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff;
                            background: {{ $paletaGraficos[$i % count($paletaGraficos)] }};">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1" style="min-width:0; flex:1;">
                            <div style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $prod->nombre }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">{{ $prod->total_vendido }} unidades</div>
                        </div>
                        <div style="font-size:13px; font-weight:600; color:#1e1b4b; white-space:nowrap;">
                            {{ $config->simbolo_moneda }} {{ number_format($prod->ingresos, 0) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4" style="font-size:13px;">
                        <i class="fas fa-box-open fa-2x mb-2 d-block opacity-50"></i>
                        Sin ventas este mes
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ── Fila inferior ─────────────────────────────────────────────────── --}}
<div class="row g-3">
    {{-- Últimas ventas --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-600" style="font-weight:600;">Últimas Ventas</h6>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:12px;">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>N° Venta</th>
                                <th>Cliente</th>
                                <th>Método</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasVentas as $venta)
                            <tr>
                                <td>
                                    <a href="{{ route('ventas.show', $venta) }}"
                                       style="color:#a855f7; font-weight:500; font-size:13px; text-decoration:none;">
                                        {{ $venta->numero_venta }}
                                    </a>
                                    <div style="font-size:11px; color:#9ca3af;">
                                        {{ $venta->fecha_venta->diffForHumans() }}
                                    </div>
                                </td>
                                <td style="font-size:13px;">
                                    {{ $venta->cliente->nombre_completo ?? '—' }}
                                </td>
                                <td>
                                    <span style="font-size:12px; text-transform:capitalize;">
                                        {{ $venta->metodo_pago }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:13px; font-weight:600;">
                                        {{ $config->simbolo_moneda }} {{ number_format($venta->total, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $colores = ['completada'=>'success','pendiente'=>'warning','cancelada'=>'danger','devuelta'=>'secondary'];
                                        $color = $colores[$venta->estado] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }}"
                                          style="font-size:11px; border-radius:20px; padding:4px 10px;">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4" style="font-size:13px;">
                                    <i class="fas fa-shopping-cart fa-2x mb-2 d-block opacity-40"></i>
                                    Sin ventas registradas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Reparaciones recientes --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-600" style="font-weight:600;">Reparaciones Recientes</h6>
                    <a href="{{ route('reparaciones.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:12px;">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                @forelse($ultimasReparaciones as $rep)
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                    @php
                        $bgEstado = [
                            'recibido'           => '#ede9fe',
                            'en_diagnostico'     => '#e0f2fe',
                            'esperando_repuesto' => '#fef9c3',
                            'en_reparacion'      => '#dbeafe',
                            'listo'              => '#d1fae5',
                            'entregado'          => '#f3f4f6',
                            'no_reparable'       => '#fee2e2',
                        ];
                        $icEstado = [
                            'recibido'           => 'fa-inbox',
                            'en_diagnostico'     => 'fa-search',
                            'esperando_repuesto' => 'fa-clock',
                            'en_reparacion'      => 'fa-wrench',
                            'listo'              => 'fa-check',
                            'entregado'          => 'fa-box',
                            'no_reparable'       => 'fa-times',
                        ];
                        $bg = $bgEstado[$rep->estado] ?? '#f3f4f6';
                        $ic = $icEstado[$rep->estado] ?? 'fa-tools';
                    @endphp
                    <div style="width:36px; height:36px; border-radius:10px; background:{{ $bg }};
                                display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas {{ $ic }}" style="font-size:14px; color:#6b7280;"></i>
                    </div>
                    <div class="flex-1" style="min-width:0;">
                        <div style="font-size:13px; font-weight:500;">
                            {{ $rep->dispositivo }}
                            @if($rep->marca) <span style="color:#9ca3af;">— {{ $rep->marca }}</span> @endif
                        </div>
                        <div style="font-size:11px; color:#9ca3af;">
                            {{ $rep->cliente->nombre_completo ?? '—' }} · {{ $rep->numero_orden }}
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @if($rep->estado === 'listo' && $rep->cliente && $rep->cliente->numeroWhatsapp())
                        @php
                            $mensajeListoDash = "Hola {$rep->cliente->nombre}, te saludamos de " . ($config->nombre_tienda ?? 'la tienda')
                                . ". Tu equipo {$rep->dispositivo}" . ($rep->modelo ? " {$rep->modelo}" : '')
                                . " (orden {$rep->numero_orden}) ya está listo para recoger. ¡Te esperamos!";
                        @endphp
                        <a href="{{ $rep->cliente->whatsappUrl($mensajeListoDash) }}" target="_blank" rel="noopener"
                           class="btn btn-sm" style="font-size:11px; padding:3px 8px; border-radius:6px; background:#25D366; color:#fff; text-decoration:none;" title="Avisar por WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif
                        <a href="{{ route('reparaciones.show', $rep) }}"
                           class="btn btn-sm" style="font-size:11px; padding:3px 8px; border-radius:6px;
                           background:#f3f4f6; color:#374151; text-decoration:none;">
                            Ver
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:13px;">
                    <i class="fas fa-tools fa-2x mb-2 d-block opacity-40"></i>
                    Sin reparaciones registradas
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const MONEDA = "{{ $config->simbolo_moneda }}";
const colorGrafico1 = "{{ $config->color_grafico_1 }}";

function hexToRgba(hex, alpha) {
    const h = hex.replace('#', '');
    const r = parseInt(h.substring(0, 2), 16);
    const g = parseInt(h.substring(2, 4), 16);
    const b = parseInt(h.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// ── Gráfica de ventas por día ───────────────────────────────────────────
const diasLabels  = @json($diasSemana->pluck('fecha'));
const diasTotales = @json($diasSemana->pluck('total'));

const ctxDias = document.getElementById('chartVentasDias').getContext('2d');

const gradientFill = ctxDias.createLinearGradient(0, 0, 0, 200);
gradientFill.addColorStop(0, hexToRgba(colorGrafico1, 0.3));
gradientFill.addColorStop(1, hexToRgba(colorGrafico1, 0.03));

new Chart(ctxDias, {
    type: 'line',
    data: {
        labels: diasLabels,
        datasets: [{
            label: 'Ventas (' + MONEDA + ')',
            data: diasTotales,
            borderColor: colorGrafico1,
            backgroundColor: gradientFill,
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colorGrafico1,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e1b4b',
                titleFont: { family: 'Poppins', size: 12 },
                bodyFont: { family: 'Poppins', size: 13 },
                callbacks: {
                    label: ctx => ' ' + MONEDA + ' ' + ctx.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2})
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { family: 'Poppins', size: 11 }, color: '#9ca3af' }
            },
            y: {
                grid: { color: '#f3f4f6' },
                ticks: {
                    font: { family: 'Poppins', size: 11 },
                    color: '#9ca3af',
                    callback: v => MONEDA + ' ' + v.toLocaleString('es-PE')
                }
            }
        }
    }
});
</script>
@endpush
