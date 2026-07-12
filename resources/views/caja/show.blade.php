@extends('layouts.app')
@section('title', 'Caja del ' . $caja->fecha->format('d/m/Y'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('caja.index') }}" style="color:#a855f7;">Control de Caja</a></li>
    <li class="breadcrumb-item active">{{ $caja->fecha->format('d/m/Y') }}</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Caja del {{ $caja->fecha->format('d/m/Y') }}</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            <span class="badge {{ $caja->estaAbierta() ? 'bg-success' : 'bg-secondary' }}"
                  style="border-radius:20px;font-size:11px;padding:4px 10px;">
                {{ $caja->estaAbierta() ? 'Abierta' : 'Cerrada' }}
            </span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('caja.reporte', $caja) }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-file-alt me-2"></i>Ver Reporte
        </a>
        @if($caja->estaAbierta())
            <a href="{{ route('caja.cierreForm', $caja) }}" class="btn btn-danger px-4">
                <i class="fas fa-door-closed me-2"></i>Cerrar Caja
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Apertura</h6>
                <div style="font-size:13px;">
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Fondo inicial</span>
                        <span class="fw-500">{{ $config->simbolo_moneda }} {{ number_format($caja->monto_inicial, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Abierta por</span>
                        <span class="fw-500">{{ $caja->usuarioApertura->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Fecha/hora</span>
                        <span class="fw-500">{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($caja->notas_apertura)
                        <div class="mt-2 pt-2" style="border-top:1px solid #f3f4f6;color:#6b7280;">
                            {{ $caja->notas_apertura }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(!$caja->estaAbierta())
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Cierre</h6>
                <div style="font-size:13px;">
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Cerrada por</span>
                        <span class="fw-500">{{ $caja->usuarioCierre->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Fecha/hora</span>
                        <span class="fw-500">{{ $caja->fecha_cierre->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($caja->notas_cierre)
                        <div class="mt-2 pt-2" style="border-top:1px solid #f3f4f6;color:#6b7280;">
                            {{ $caja->notas_cierre }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Desglose por Método de Pago</h6>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th class="text-end">Esperado</th>
                                @if(!$caja->estaAbierta())
                                    <th class="text-end">Contado</th>
                                    <th class="text-end">Diferencia</th>
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
                                <tr>
                                    <td style="font-size:13.5px;font-weight:500;">{{ $fila['nombre'] }}</td>
                                    <td class="text-end" style="font-size:13.5px;">{{ $config->simbolo_moneda }} {{ number_format($fila['esperado'], 2) }}</td>
                                    @if(!$caja->estaAbierta())
                                        <td class="text-end" style="font-size:13.5px;">{{ $config->simbolo_moneda }} {{ number_format($contado ?? 0, 2) }}</td>
                                        <td class="text-end" style="font-size:13.5px;font-weight:600;color:{{ $diferencia < 0 ? '#dc2626' : ($diferencia > 0 ? '#16a34a' : '#6b7280') }};">
                                            {{ $diferencia >= 0 ? '+' : '' }}{{ number_format($diferencia ?? 0, 2) }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top:2px solid #e5e7eb;">
                                <td style="font-size:14px;font-weight:700;">Total</td>
                                <td class="text-end" style="font-size:14px;font-weight:700;">{{ $config->simbolo_moneda }} {{ number_format($totalEsperado, 2) }}</td>
                                @if(!$caja->estaAbierta())
                                    <td class="text-end" style="font-size:14px;font-weight:700;">{{ $config->simbolo_moneda }} {{ number_format($totalContado, 2) }}</td>
                                    <td class="text-end" style="font-size:14px;font-weight:700;color:{{ ($totalContado - $totalEsperado) < 0 ? '#dc2626' : (($totalContado - $totalEsperado) > 0 ? '#16a34a' : '#6b7280') }};">
                                        {{ ($totalContado - $totalEsperado) >= 0 ? '+' : '' }}{{ number_format($totalContado - $totalEsperado, 2) }}
                                    </td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-arrow-circle-down me-2" style="color:#dc2626;"></i>Gastos del Día</h6>
                        @forelse($gastosDelDia as $gasto)
                            <div class="d-flex justify-content-between align-items-start py-2" style="border-bottom:1px solid #f3f4f6;font-size:13px;">
                                <div>
                                    <div style="font-weight:500;">{{ $gasto->descripcion }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">
                                        {{ $gasto->fecha_gasto->format('H:i') }} · {{ $gasto->metodoPago->nombre ?? '—' }} · {{ $gasto->usuario->name ?? '—' }}
                                    </div>
                                </div>
                                <span style="font-weight:600;color:#dc2626;">− {{ $config->simbolo_moneda }} {{ number_format($gasto->monto, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0" style="font-size:13px;">Sin gastos registrados este día.</p>
                        @endforelse
                        @if($gastosDelDia->isNotEmpty())
                            <div class="d-flex justify-content-between pt-2 mt-1" style="font-size:13px;font-weight:700;">
                                <span>Total Gastos</span>
                                <span style="color:#dc2626;">{{ $config->simbolo_moneda }} {{ number_format($gastosDelDia->sum('monto'), 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-arrow-circle-up me-2" style="color:#16a34a;"></i>Ingresos del Día</h6>
                        @forelse($ingresosDelDia as $ingreso)
                            <div class="d-flex justify-content-between align-items-start py-2" style="border-bottom:1px solid #f3f4f6;font-size:13px;">
                                <div>
                                    <div style="font-weight:500;">{{ $ingreso->descripcion }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">
                                        {{ $ingreso->fecha_ingreso->format('H:i') }} · {{ $ingreso->metodoPago->nombre ?? '—' }} · {{ $ingreso->usuario->name ?? '—' }}
                                    </div>
                                </div>
                                <span style="font-weight:600;color:#16a34a;">+ {{ $config->simbolo_moneda }} {{ number_format($ingreso->monto, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0" style="font-size:13px;">Sin ingresos registrados este día.</p>
                        @endforelse
                        @if($ingresosDelDia->isNotEmpty())
                            <div class="d-flex justify-content-between pt-2 mt-1" style="font-size:13px;font-weight:700;">
                                <span>Total Ingresos</span>
                                <span style="color:#16a34a;">{{ $config->simbolo_moneda }} {{ number_format($ingresosDelDia->sum('monto'), 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
