@extends('layouts.app')
@section('title', 'Reparaciones')

@section('breadcrumb')
    <li class="breadcrumb-item active">Reparaciones</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Servicio Técnico</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Órdenes de reparación y seguimiento</p>
    </div>
    <a href="{{ route('reparaciones.create') }}" class="btn btn-primary px-4">
        <i class="fas fa-plus me-2"></i>Nueva Orden
    </a>
</div>

{{-- Estadísticas rápidas --}}
<div class="row g-3 mb-4">
    @php
        $estats = [
            ['label'=>'Recibidos', 'value'=>$estadisticas['recibidos'], 'icon'=>'fa-inbox', 'color'=>'#7c3aed', 'bg'=>'#ede9fe'],
            ['label'=>'En Proceso', 'value'=>$estadisticas['en_proceso'], 'icon'=>'fa-wrench', 'color'=>'#0284c7', 'bg'=>'#e0f2fe'],
            ['label'=>'Listos', 'value'=>$estadisticas['listos'], 'icon'=>'fa-check-circle', 'color'=>'#059669', 'bg'=>'#d1fae5'],
            ['label'=>'Entregados', 'value'=>$estadisticas['entregados'], 'icon'=>'fa-box', 'color'=>'#374151', 'bg'=>'#f3f4f6'],
        ];
    @endphp
    @foreach($estats as $e)
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div style="width:44px; height:44px; border-radius:12px; background:{{ $e['bg'] }};
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas {{ $e['icon'] }}" style="color:{{ $e['color'] }}; font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px; font-weight:700; color:{{ $e['color'] }};">{{ $e['value'] }}</div>
                    <div style="font-size:12px; color:#9ca3af;">{{ $e['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" class="form-control" name="buscar"
                       placeholder="N° orden, dispositivo o cliente..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="recibido" {{ request('estado')=='recibido'?'selected':'' }}>Recibido</option>
                    <option value="en_diagnostico" {{ request('estado')=='en_diagnostico'?'selected':'' }}>En Diagnóstico</option>
                    <option value="esperando_repuesto" {{ request('estado')=='esperando_repuesto'?'selected':'' }}>Esperando Repuesto</option>
                    <option value="en_reparacion" {{ request('estado')=='en_reparacion'?'selected':'' }}>En Reparación</option>
                    <option value="listo" {{ request('estado')=='listo'?'selected':'' }}>Listo</option>
                    <option value="entregado" {{ request('estado')=='entregado'?'selected':'' }}>Entregado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="prioridad">
                    <option value="">Toda prioridad</option>
                    <option value="urgente" {{ request('prioridad')=='urgente'?'selected':'' }}>🔴 Urgente</option>
                    <option value="alta" {{ request('prioridad')=='alta'?'selected':'' }}>🟠 Alta</option>
                    <option value="media" {{ request('prioridad')=='media'?'selected':'' }}>🟡 Media</option>
                    <option value="baja" {{ request('prioridad')=='baja'?'selected':'' }}>🟢 Baja</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="{{ route('reparaciones.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">N° Orden</th>
                        <th>Dispositivo</th>
                        <th>Cliente</th>
                        <th>Técnico</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Presupuesto</th>
                        <th>Fecha</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reparaciones as $rep)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('reparaciones.show', $rep) }}" style="font-weight:600; color:#a855f7; text-decoration:none;">
                                {{ $rep->numero_orden }}
                            </a>
                        </td>
                        <td style="font-size:13px;">
                            <div style="font-weight:500;">{{ $rep->dispositivo }}</div>
                            <div style="font-size:11px; color:#9ca3af;">
                                {{ $rep->marca }} {{ $rep->modelo }}
                            </div>
                        </td>
                        <td style="font-size:13px;">{{ $rep->cliente->nombre_completo ?? '—' }}</td>
                        <td style="font-size:13px; color:#6b7280;">{{ $rep->tecnico->name ?? '—' }}</td>
                        <td>
                            @php
                                $priCol = ['urgente'=>['🔴','#fee2e2','#991b1b'],'alta'=>['🟠','#ffedd5','#9a3412'],'media'=>['🟡','#fef9c3','#713f12'],'baja'=>['🟢','#d1fae5','#065f46']];
                                $pr = $priCol[$rep->prioridad] ?? ['⚪','#f3f4f6','#374151'];
                            @endphp
                            <span style="background:{{ $pr[1] }}; color:{{ $pr[2] }}; border-radius:20px; padding:3px 9px; font-size:11px; font-weight:500;">
                                {{ $pr[0] }} {{ ucfirst($rep->prioridad) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $stColors = [
                                    'recibido'           => ['#ede9fe','#6d28d9'],
                                    'en_diagnostico'     => ['#e0f2fe','#0369a1'],
                                    'esperando_repuesto' => ['#fef9c3','#92400e'],
                                    'en_reparacion'      => ['#dbeafe','#1d4ed8'],
                                    'listo'              => ['#d1fae5','#065f46'],
                                    'entregado'          => ['#f3f4f6','#374151'],
                                    'no_reparable'       => ['#fee2e2','#991b1b'],
                                ];
                                $sc = $stColors[$rep->estado] ?? ['#f3f4f6','#374151'];
                                $stLabel = str_replace('_',' ',ucfirst($rep->estado));
                            @endphp
                            <span style="background:{{ $sc[0] }}; color:{{ $sc[1] }}; border-radius:20px; padding:4px 10px; font-size:11px; font-weight:500;">
                                {{ $stLabel }}
                            </span>
                        </td>
                        <td style="font-size:13px; font-weight:600;">
                            @if($rep->presupuesto > 0)
                                {{ $config->simbolo_moneda }} {{ number_format($rep->presupuesto, 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;">
                            <div>{{ optional($rep->fecha_recepcion)->format('d/m/Y') ?? '—' }}</div>
                            @if($rep->fecha_estimada)
                                <div style="color:#9ca3af; font-size:11px;">
                                    Est: {{ $rep->fecha_estimada->format('d/m') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                @if($rep->estado === 'listo' && $rep->cliente && $rep->cliente->numeroWhatsapp())
                                @php
                                    $mensajeListoIdx = "Hola {$rep->cliente->nombre}, te saludamos de " . ($config->nombre_tienda ?? 'la tienda')
                                        . ". Tu equipo {$rep->dispositivo}" . ($rep->modelo ? " {$rep->modelo}" : '')
                                        . " (orden {$rep->numero_orden}) ya está listo para recoger. ¡Te esperamos!";
                                @endphp
                                <a href="{{ $rep->cliente->whatsappUrl($mensajeListoIdx) }}" target="_blank" rel="noopener"
                                   class="btn btn-sm" style="background:#25D366; color:#fff; border-radius:8px; padding:5px 10px;" title="Avisar que está listo por WhatsApp">
                                    <i class="fab fa-whatsapp fa-sm"></i>
                                </a>
                                @endif
                                <a href="{{ route('reparaciones.show', $rep) }}"
                                   class="btn btn-sm" style="background:#ede9fe; color:#7c3aed; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                <a href="{{ route('reparaciones.edit', $rep) }}"
                                   class="btn btn-sm" style="background:#e0f2fe; color:#0369a1; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-edit fa-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-tools fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                            <p class="text-muted mb-2">No hay órdenes de reparación</p>
                            <a href="{{ route('reparaciones.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Nueva Orden
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reparaciones->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px;">
                Mostrando {{ $reparaciones->firstItem() }}–{{ $reparaciones->lastItem() }} de {{ $reparaciones->total() }}
            </span>
            {{ $reparaciones->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
