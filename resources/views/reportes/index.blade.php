@extends('layouts.app')
@section('title', 'Reportes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Reportes</li>
@endsection

@section('content')

@php
    $paletaGraficos = [$config->color_grafico_1, $config->color_grafico_2, $config->color_grafico_3];
@endphp

{{-- Encabezado + filtro de fechas --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Reportes y Estadísticas</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            Período: <strong>{{ $desde->format('d/m/Y') }}</strong> al <strong>{{ $hasta->format('d/m/Y') }}</strong>
        </p>
    </div>
    <button class="btn btn-outline-primary" onclick="window.print()">
        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
    </button>
</div>

{{-- Filtro --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1" style="font-size:12px;">Período rápido</label>
                <div class="d-flex gap-2">
                    @php
                        $periodos = [
                            'hoy'       => ['Hoy',           now()->format('Y-m-d'), now()->format('Y-m-d')],
                            'semana'    => ['Esta semana',   now()->startOfWeek()->format('Y-m-d'), now()->format('Y-m-d')],
                            'mes'       => ['Este mes',      now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
                            'mes_ant'   => ['Mes anterior',  now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
                            'año'       => ['Este año',      now()->startOfYear()->format('Y-m-d'), now()->format('Y-m-d')],
                        ];
                    @endphp
                    @foreach($periodos as $key => [$label, $d, $h])
                        <a href="{{ route('reportes.index', ['desde'=>$d, 'hasta'=>$h]) }}"
                           class="btn btn-sm {{ request('desde')==$d && request('hasta')==$h ? 'btn-primary' : 'btn-outline-secondary' }}"
                           style="font-size:12px; border-radius:20px;">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1" style="font-size:12px;">Desde</label>
                <input type="date" class="form-control form-control-sm" name="desde"
                       value="{{ request('desde', $desde->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1" style="font-size:12px;">Hasta</label>
                <input type="date" class="form-control form-control-sm" name="hasta"
                       value="{{ request('hasta', $hasta->format('Y-m-d')) }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-filter me-1"></i>Aplicar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-purple">
            <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="kpi-value">{{ $config->simbolo_moneda }} {{ number_format($totalVentas, 0) }}</div>
            <div class="kpi-label">Total Ventas</div>
            <span class="kpi-badge"><i class="fas fa-receipt fa-xs"></i> {{ $cantidadVentas }} transacciones</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-pink">
            <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
            <div class="kpi-value">{{ $config->simbolo_moneda }} {{ number_format($ticketPromedio, 0) }}</div>
            <div class="kpi-label">Ticket Promedio</div>
            <span class="kpi-badge"><i class="fas fa-shopping-bag fa-xs"></i> Por venta</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-cyan">
            <div class="kpi-icon"><i class="fas fa-tools"></i></div>
            <div class="kpi-value">{{ $config->simbolo_moneda }} {{ number_format($totalReparaciones, 0) }}</div>
            <div class="kpi-label">Ingresos Reparaciones</div>
            <span class="kpi-badge"><i class="fas fa-wrench fa-xs"></i> Servicio técnico</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-green">
            <div class="kpi-icon"><i class="fas fa-user-plus"></i></div>
            <div class="kpi-value">{{ $clientesNuevos }}</div>
            <div class="kpi-label">Clientes Nuevos</div>
            <span class="kpi-badge"><i class="fas fa-users fa-xs"></i> En el período</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-orange">
            <div class="kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="kpi-value">{{ $config->simbolo_moneda }} {{ number_format($ingresosPorAbonos, 0) }}</div>
            <div class="kpi-label">Abonos de Crédito Cobrados</div>
            <span class="kpi-badge"><i class="fas fa-coins fa-xs"></i> En el período</span>
        </div>
    </div>
</div>

{{-- Gráficas --}}
<div class="row g-4 mb-4">
    {{-- Ventas por día --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Ventas por Día</h6>
                <p class="text-muted mb-3" style="font-size:12px;">Ingresos diarios en el período seleccionado</p>
                <canvas id="chartDias" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Método de pago --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Métodos de Pago</h6>
                <p class="text-muted mb-3" style="font-size:12px;">Distribución por forma de cobro</p>
                <canvas id="chartPago" height="180"></canvas>
                <div class="mt-3">
                    @foreach($ventasPorPago as $pago)
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">
                        <span class="text-muted">{{ $pago->metodoPago->nombre ?? '—' }}</span>
                        <span class="fw-600">{{ $config->simbolo_moneda }} {{ number_format($pago->monto, 2) }} ({{ $pago->total }})</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tablas de ranking --}}
<div class="row g-4 mb-4">
    {{-- Top Productos --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Top 10 Productos Más Vendidos</h6>
                @if($topProductos->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Producto</th>
                                <th class="text-center">Unid.</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProductos as $i => $p)
                            <tr>
                                <td>
                                    <div style="width:24px; height:24px; border-radius:6px; font-size:11px; font-weight:700; color:#fff; display:flex; align-items:center; justify-content:center;
                                        background:{{ $paletaGraficos[$i % count($paletaGraficos)] }};">
                                        {{ $i+1 }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:500;">{{ $p->nombre }}</div>
                                    <div style="font-size:11px; color:var(--text-muted-2);">{{ $p->codigo }}</div>
                                </td>
                                <td class="text-center">{{ $p->unidades }}</td>
                                <td class="text-end fw-bold" style="color:var(--text-dark);">{{ $config->simbolo_moneda }} {{ number_format($p->ingresos, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted" style="font-size:13px;">Sin ventas en el período</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Clientes --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Top 10 Clientes</h6>
                @if($topClientes->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Cliente</th>
                                <th class="text-center">Compras</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topClientes as $i => $c)
                            <tr>
                                <td>
                                    <div style="width:24px; height:24px; border-radius:6px; font-size:11px; font-weight:700; color:#fff; display:flex; align-items:center; justify-content:center;
                                        background:{{ $paletaGraficos[$i % count($paletaGraficos)] }};">
                                        {{ $i+1 }}
                                    </div>
                                </td>
                                <td style="font-weight:500;">{{ $c->nombre }}</td>
                                <td class="text-center">{{ $c->compras }}</td>
                                <td class="text-end fw-bold" style="color:var(--text-dark);">{{ $config->simbolo_moneda }} {{ number_format($c->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted" style="font-size:13px;">Sin ventas en el período</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Stock bajo + Reparaciones por estado --}}
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">⚠️ Productos con Stock Bajo</h6>
                    <span style="background:#fee2e2; color:#dc2626; border-radius:20px; padding:3px 12px; font-size:12px;">
                        {{ $stockBajo->count() }} productos
                    </span>
                </div>
                @if($stockBajo->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Mínimo</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockBajo as $p)
                            <tr>
                                <td>
                                    <div style="font-weight:500;">{{ $p->nombre }}</div>
                                    <div style="font-size:11px; color:var(--text-muted-2);">{{ $p->marca->nombre ?? '' }}</div>
                                </td>
                                <td style="color:var(--text-muted);">{{ $p->categoria->nombre ?? '—' }}</td>
                                <td class="text-center">
                                    <span style="background:{{ $p->stock<=0?'#fee2e2':'#fef3c7' }}; color:{{ $p->stock<=0?'#dc2626':'#d97706' }}; border-radius:20px; padding:3px 10px; font-size:12px; font-weight:700;">
                                        {{ $p->stock }}
                                    </span>
                                </td>
                                <td class="text-center" style="color:var(--text-muted-2);">{{ $p->stock_minimo }}</td>
                                <td class="text-end">
                                    <a href="{{ route('productos.edit', $p) }}"
                                       style="color:#a855f7; font-size:12px; text-decoration:none;">
                                        Reponer <i class="fas fa-arrow-right fa-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-success" style="font-size:13px;">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                    Todos los productos tienen stock óptimo
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Reparaciones por estado --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Reparaciones por Estado</h6>
                @php
                    $estCfg = ['recibido'=>['📥','#ede9fe','#6d28d9'],'en_diagnostico'=>['🔍','#e0f2fe','#0369a1'],'esperando_repuesto'=>['⏳','#fef9c3','#92400e'],'en_reparacion'=>['🔧','#dbeafe','#1d4ed8'],'listo'=>['✅','#d1fae5','#065f46'],'entregado'=>['📦','#f3f4f6','#374151'],'no_reparable'=>['❌','#fee2e2','#991b1b']];
                    $totalRep = $repPorEstado->sum('total');
                @endphp
                @forelse($repPorEstado->sortByDesc('total') as $rep)
                @php $cfg = $estCfg[$rep->estado] ?? ['🔘','#f3f4f6','#374151']; $pct = $totalRep > 0 ? ($rep->total/$totalRep)*100 : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="font-size:13px;">{{ $cfg[0] }} {{ str_replace('_',' ',ucfirst($rep->estado)) }}</span>
                        <span style="font-size:13px; font-weight:600; color:{{ $cfg[2] }};">{{ $rep->total }}</span>
                    </div>
                    <div class="progress" style="height:6px; border-radius:4px; background:#f3f4f6;">
                        <div class="progress-bar" style="width:{{ $pct }}%; background:{{ $cfg[2] }};"></div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted" style="font-size:13px;">Sin reparaciones en el período</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Estadísticas del Sistema --}}
<div class="row g-4 mt-0">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Estadísticas del Sistema</h6>
                @php
                    $stats = [
                        ['icon'=>'users','color'=>'#a855f7','label'=>'Usuarios activos','value'=>\App\Models\User::where('activo',true)->count()],
                        ['icon'=>'users','color'=>'#06b6d4','label'=>'Total clientes','value'=>\App\Models\Cliente::count()],
                        ['icon'=>'box','color'=>'#10b981','label'=>'Productos en inventario','value'=>\App\Models\Producto::where('activo',true)->count()],
                        ['icon'=>'shopping-cart','color'=>'#ec4899','label'=>'Ventas registradas','value'=>\App\Models\Venta::count()],
                        ['icon'=>'tools','color'=>'#f59e0b','label'=>'Órdenes de reparación','value'=>\App\Models\Reparacion::count()],
                    ];
                @endphp
                @foreach($stats as $s)
                <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid #f3f4f6; font-size:13px;">
                    <div style="width:32px;height:32px;background:{{ $s['color'] }}18;border-radius:8px;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-{{ $s['icon'] }}" style="color:{{ $s['color'] }};font-size:13px;"></i>
                    </div>
                    <span class="text-muted flex-grow-1">{{ $s['label'] }}</span>
                    <strong>{{ $s['value'] }}</strong>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Cartera por Cobrar --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Cartera por Cobrar</h6>
                    <span style="background:#fef3c7; color:#92400e; border-radius:20px; padding:3px 12px; font-size:12px;">
                        {{ $config->simbolo_moneda }} {{ number_format($totalCartera, 2) }}
                    </span>
                </div>
                @if($carteraPendiente->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Saldo</th>
                                <th class="text-center">Vencimiento</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($carteraPendiente as $v)
                            <tr>
                                <td>
                                    <a href="{{ route('ventas.show', $v) }}" style="color:var(--text-dark); text-decoration:none; font-weight:500;">
                                        {{ $v->cliente->nombre_completo ?? '—' }}
                                    </a>
                                    <div style="font-size:11px; color:var(--text-muted-2);">{{ $v->numero_venta }}</div>
                                </td>
                                <td class="text-end">{{ $config->simbolo_moneda }} {{ number_format($v->total, 2) }}</td>
                                <td class="text-end fw-bold" style="color:#dc2626;">{{ $config->simbolo_moneda }} {{ number_format($v->saldo_pendiente, 2) }}</td>
                                <td class="text-center">
                                    @if($v->estaAtrasada())
                                        <span style="background:#fee2e2; color:#991b1b; border-radius:20px; padding:3px 10px; font-size:11px; font-weight:700;">
                                            {{ optional($v->fecha_vencimiento)->format('d/m/Y') }} ({{ (int) now()->diffInDays($v->fecha_vencimiento) }}d atraso)
                                        </span>
                                    @else
                                        <span style="color:var(--text-muted); font-size:12px;">{{ optional($v->fecha_vencimiento)->format('d/m/Y') ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($v->cliente && $v->cliente->numeroWhatsapp())
                                    @php
                                        $tiendaNombreCobro = '*' . ($config->nombre_tienda ?? 'la tienda') . '*';
                                        if ($v->estaAtrasada()) {
                                            $diasAtraso = (int) now()->diffInDays($v->fecha_vencimiento);
                                            $mensajeCobro = "Hola {$v->cliente->nombre}, te saludamos de {$tiendaNombreCobro}."
                                                . " Te contactamos porque tienes {$diasAtraso} día(s) de mora en el saldo pendiente de {$config->simbolo_moneda} " . number_format($v->saldo_pendiente, 2)
                                                . " por la compra {$v->numero_venta} (venció el " . $v->fecha_vencimiento->format('d/m/Y') . ")."
                                                . " Agradecemos ponerte al día lo antes posible. Quedamos atentos para coordinar el pago. ¡Gracias!";
                                        } else {
                                            $mensajeCobro = "Hola {$v->cliente->nombre}, te saludamos de {$tiendaNombreCobro}."
                                                . " Te recordamos que tienes un saldo pendiente de {$config->simbolo_moneda} " . number_format($v->saldo_pendiente, 2)
                                                . " por la compra {$v->numero_venta}"
                                                . ($v->fecha_vencimiento ? (", con vencimiento el " . $v->fecha_vencimiento->format('d/m/Y')) : '')
                                                . ". Quedamos atentos para coordinar el pago. ¡Gracias!";
                                        }
                                    @endphp
                                    <a href="{{ $v->cliente->whatsappUrl($mensajeCobro) }}" target="_blank" rel="noopener"
                                       class="btn btn-sm" style="background:#25D366; color:#fff; border-radius:8px; padding:5px 8px;" title="Gestionar cobro por WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-success" style="font-size:13px;">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                    No hay cartera pendiente por cobrar
                </div>
                @endif
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

// Gráfica ventas por día
const diasLabels  = @json($ventasPorDia->pluck('fecha'));
const diasTotales = @json($ventasPorDia->pluck('total'));

const ctxDias = document.getElementById('chartDias').getContext('2d');
const grad = ctxDias.createLinearGradient(0, 0, 0, 220);
grad.addColorStop(0, hexToRgba(colorGrafico1, 0.3));
grad.addColorStop(1, hexToRgba(colorGrafico1, 0.02));

const temaOscuroActivo = document.documentElement.getAttribute('data-theme') === 'dark';
const chartTickColor = temaOscuroActivo ? '#9691ac' : '#9ca3af';
const chartGridColor = temaOscuroActivo ? 'rgba(255,255,255,.06)' : '#f3f4f6';

const chartVentasDias = new Chart(ctxDias, {
    type: 'bar',
    data: {
        labels: diasLabels,
        datasets: [
            {
                label: 'Ventas (' + MONEDA + ')',
                data: diasTotales,
                backgroundColor: hexToRgba(colorGrafico1, 0.75),
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false },
            tooltip: { callbacks: { label: c => ' ' + MONEDA + ' ' + c.parsed.y.toLocaleString('es-PE', {minimumFractionDigits:2}) } }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 11 }, color: chartTickColor } },
            y: { grid: { color: chartGridColor }, ticks: { font: { family: 'Poppins', size: 11 }, color: chartTickColor, callback: v => MONEDA+' '+v.toLocaleString('es-PE') } }
        }
    }
});

// Gráfica métodos de pago
const pagoLabels = @json($ventasPorPago->pluck('metodoPago.nombre'));
const pagoMontos = @json($ventasPorPago->pluck('monto'));
const pagoColores = @json(collect(range(0, max($ventasPorPago->count() - 1, 0)))->map(fn($i) => $paletaGraficos[$i % count($paletaGraficos)])->values());

const chartPago = new Chart(document.getElementById('chartPago'), {
    type: 'doughnut',
    data: {
        labels: pagoLabels,
        datasets: [{
            data: pagoMontos,
            backgroundColor: pagoColores,
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 }, padding: 12, color: chartTickColor } },
            tooltip: { callbacks: { label: c => ' ' + MONEDA + ' ' + c.parsed.toLocaleString('es-PE', {minimumFractionDigits:2}) } }
        }
    }
});

document.addEventListener('themechange', function (e) {
    const dark = e.detail.theme === 'dark';
    const tick = dark ? '#9691ac' : '#9ca3af';
    const grid = dark ? 'rgba(255,255,255,.06)' : '#f3f4f6';
    chartVentasDias.options.scales.x.ticks.color = tick;
    chartVentasDias.options.scales.y.ticks.color = tick;
    chartVentasDias.options.scales.y.grid.color = grid;
    chartVentasDias.update();
    chartPago.options.plugins.legend.labels.color = tick;
    chartPago.update();
});
</script>
@endpush
