<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Reporte de Cierre — {{ $caja->fecha->format('d/m/Y') }}</title>
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1e1b4b; margin: 0; padding: 20px; }
    h1 { font-size: 18px; margin: 0 0 2px; }
    .subtitulo { font-size: 11px; color: #6b7280; margin-bottom: 14px; }
    table { width: 100%; border-collapse: collapse; }
    .encabezado-tabla td { vertical-align: top; padding-bottom: 14px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: bold; }
    .badge-abierta { background: #d1fae5; color: #065f46; }
    .badge-cerrada { background: #e5e7eb; color: #374151; }

    .resumen-tabla td { padding: 8px; }
    .resumen-caja { background: #f9fafb; border-radius: 4px; padding: 8px 10px; }
    .resumen-label { font-size: 9px; color: #9ca3af; text-transform: uppercase; }
    .resumen-value { font-size: 14px; font-weight: bold; }

    .total-caja { background: #a855f7; color: #fff; padding: 12px; text-align: center; margin: 14px 0; }
    .total-caja .label { font-size: 13px; font-weight: bold; }
    .total-caja .value { font-size: 20px; font-weight: bold; }

    .seccion-titulo { font-size: 10px; text-transform: uppercase; color: #9ca3af; margin: 16px 0 6px; }
    .detalle-tabla { font-size: 11px; }
    .detalle-tabla th { text-align: left; border-bottom: 2px solid #e9d5ff; padding: 5px 4px; font-size: 9.5px; text-transform: uppercase; color: #6b7280; }
    .detalle-tabla td { border-bottom: 1px solid #f3f4f6; padding: 5px 4px; }
    .text-right { text-align: right; }
    .text-rojo { color: #dc2626; }
    .text-verde { color: #16a34a; }
    .notas { background: #f9fafb; padding: 8px 10px; font-size: 10.5px; color: #6b7280; margin-top: 14px; }
</style>
</head>
<body>

<table class="encabezado-tabla">
    <tr>
        <td style="width:70%;">
            <h1>{{ $config->nombre_tienda ?? 'CRM Celulares' }}</h1>
            <div class="subtitulo">Reporte de Cierre de Caja</div>
        </td>
        <td style="width:30%;text-align:right;">
            <div style="font-size:16px;font-weight:bold;color:#a855f7;">{{ $caja->fecha->format('d/m/Y') }}</div>
            <span class="badge {{ $caja->estaAbierta() ? 'badge-abierta' : 'badge-cerrada' }}">
                {{ $caja->estaAbierta() ? 'Abierta' : 'Cerrada' }}
            </span>
        </td>
    </tr>
</table>

<div style="font-size:10.5px;color:#6b7280;margin-bottom:10px;">
    Abierta por {{ $caja->usuarioApertura->name ?? '—' }} ({{ $caja->fecha_apertura->format('d/m/Y H:i') }})
    @if($caja->fecha_cierre)
        &nbsp;·&nbsp; Cerrada por {{ $caja->usuarioCierre->name ?? '—' }} ({{ $caja->fecha_cierre->format('d/m/Y H:i') }})
    @endif
</div>

<table class="resumen-tabla">
    <tr>
        <td style="width:33%;"><div class="resumen-caja"><div class="resumen-label">Fondo Inicial</div><div class="resumen-value">{{ $config->simbolo_moneda }} {{ number_format($caja->monto_inicial, 2) }}</div></div></td>
        <td style="width:33%;"><div class="resumen-caja"><div class="resumen-label">Ventas de Contado ({{ $cantidadVentasContado }})</div><div class="resumen-value">{{ $config->simbolo_moneda }} {{ number_format($totalVentasContado, 2) }}</div></div></td>
        <td style="width:33%;"><div class="resumen-caja"><div class="resumen-label">Descuentos Aplicados</div><div class="resumen-value text-rojo">- {{ $config->simbolo_moneda }} {{ number_format($totalDescuentos, 2) }}</div></div></td>
    </tr>
    <tr>
        <td><div class="resumen-caja"><div class="resumen-label">Abonos de Credito Cobrados</div><div class="resumen-value">{{ $config->simbolo_moneda }} {{ number_format($totalAbonos, 2) }}</div></div></td>
        <td><div class="resumen-caja"><div class="resumen-label">Ingresos</div><div class="resumen-value text-verde">+ {{ $config->simbolo_moneda }} {{ number_format($totalIngresos, 2) }}</div></div></td>
        <td><div class="resumen-caja"><div class="resumen-label">Gastos</div><div class="resumen-value text-rojo">- {{ $config->simbolo_moneda }} {{ number_format($totalGastos, 2) }}</div></div></td>
    </tr>
</table>

<div class="total-caja">
    <span class="label">TOTAL QUE DEBE HABER EN CAJA&nbsp;&nbsp;</span>
    <span class="value">{{ $config->simbolo_moneda }} {{ number_format($totalEsperado, 2) }}</span>
</div>

<div class="seccion-titulo">Desglose por Metodo de Pago</div>
<table class="detalle-tabla">
    <thead>
        <tr>
            <th>Metodo</th>
            <th class="text-right">Esperado</th>
            @if(!$caja->estaAbierta())
                <th class="text-right">Contado</th>
                <th class="text-right">Diferencia</th>
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
                <td>{{ $fila['nombre'] }}</td>
                <td class="text-right">{{ $config->simbolo_moneda }} {{ number_format($fila['esperado'], 2) }}</td>
                @if(!$caja->estaAbierta())
                    <td class="text-right">{{ $config->simbolo_moneda }} {{ number_format($contado ?? 0, 2) }}</td>
                    <td class="text-right {{ $diferencia < 0 ? 'text-rojo' : ($diferencia > 0 ? 'text-verde' : '') }}">
                        {{ $diferencia >= 0 ? '+' : '' }}{{ number_format($diferencia ?? 0, 2) }}
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

@if($gastosDelDia->isNotEmpty())
<div class="seccion-titulo">Detalle de Gastos</div>
<table class="detalle-tabla">
    <thead>
        <tr><th>Hora</th><th>Descripcion</th><th>Metodo</th><th class="text-right">Monto</th></tr>
    </thead>
    <tbody>
        @foreach($gastosDelDia as $gasto)
        <tr>
            <td>{{ $gasto->fecha_gasto->format('H:i') }}</td>
            <td>{{ $gasto->descripcion }}</td>
            <td>{{ $gasto->metodoPago->nombre ?? '—' }}</td>
            <td class="text-right text-rojo">- {{ $config->simbolo_moneda }} {{ number_format($gasto->monto, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($ingresosDelDia->isNotEmpty())
<div class="seccion-titulo">Detalle de Ingresos</div>
<table class="detalle-tabla">
    <thead>
        <tr><th>Hora</th><th>Descripcion</th><th>Metodo</th><th class="text-right">Monto</th></tr>
    </thead>
    <tbody>
        @foreach($ingresosDelDia as $ingreso)
        <tr>
            <td>{{ $ingreso->fecha_ingreso->format('H:i') }}</td>
            <td>{{ $ingreso->descripcion }}</td>
            <td>{{ $ingreso->metodoPago->nombre ?? '—' }}</td>
            <td class="text-right text-verde">+ {{ $config->simbolo_moneda }} {{ number_format($ingreso->monto, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($caja->notas_apertura || $caja->notas_cierre)
<div class="notas">
    @if($caja->notas_apertura)<div><strong>Notas de apertura:</strong> {{ $caja->notas_apertura }}</div>@endif
    @if($caja->notas_cierre)<div><strong>Notas de cierre:</strong> {{ $caja->notas_cierre }}</div>@endif
</div>
@endif

<div style="font-size:9px;color:#9ca3af;margin-top:16px;">Generado el {{ now()->format('d/m/Y H:i') }}</div>

</body>
</html>
