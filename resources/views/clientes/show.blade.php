@extends('layouts.app')
@section('title', 'Perfil Cliente')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}" style="color:#a855f7;">Clientes</a></li>
    <li class="breadcrumb-item active">{{ $cliente->nombre_completo }}</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- Perfil --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body p-4 text-center">
                <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,#a855f7,#ec4899);
                            display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; color:#fff; font-weight:700;">
                    {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $cliente->nombre_completo }}</h5>
                <span style="background:#ede9fe; color:#7c3aed; border-radius:20px; padding:4px 14px; font-size:12px; font-weight:500;">
                    {{ ucfirst($cliente->tipo) }}
                </span>
                @if(!$cliente->activo)
                    <span class="badge bg-danger ms-1" style="border-radius:20px;">Inactivo</span>
                @endif

                <hr class="my-3">

                <div class="text-start" style="font-size:13.5px;">
                    @if($cliente->email)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-envelope" style="color:#a855f7; width:16px;"></i>
                        <span>{{ $cliente->email }}</span>
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-phone" style="color:#a855f7; width:16px;"></i>
                        <span>{{ $cliente->telefono }}</span>
                    </div>
                    @if($cliente->celular)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-mobile-alt" style="color:#a855f7; width:16px;"></i>
                        <span>{{ $cliente->celular }}</span>
                    </div>
                    @endif
                    @if($cliente->numeroWhatsapp())
                    <a href="{{ $cliente->whatsappUrl('Hola ' . $cliente->nombre . ', te saludamos de *' . ($config->nombre_tienda ?? 'la tienda') . '*.') }}"
                       target="_blank" rel="noopener"
                       class="btn btn-sm w-100 mt-1" style="background:#25D366; color:#fff; border-radius:8px;">
                        <i class="fab fa-whatsapp me-2"></i>Contactar por WhatsApp
                    </a>
                    @endif
                    @if($cliente->dni)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-id-card" style="color:#a855f7; width:16px;"></i>
                        <span>{{ $cliente->tipo_documento ?? 'Documento' }}: {{ $cliente->dni }}</span>
                    </div>
                    @endif
                    @if($cliente->direccion)
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="fas fa-map-marker-alt" style="color:#a855f7; width:16px; margin-top:2px;"></i>
                        <span>{{ $cliente->direccion }}{{ $cliente->ciudad ? ', '.$cliente->ciudad : '' }}{{ $cliente->departamento ? ' - '.$cliente->departamento : '' }}</span>
                    </div>
                    @endif
                    @if($cliente->fecha_nacimiento)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-birthday-cake" style="color:#a855f7; width:16px;"></i>
                        <span>{{ $cliente->fecha_nacimiento->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($cliente->empresa)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-building" style="color:#a855f7; width:16px;"></i>
                        <span>{{ $cliente->empresa }} @if($cliente->ruc)(RUT/NIT: {{ $cliente->ruc }})@endif</span>
                    </div>
                    @endif
                </div>

                @if($cliente->notas)
                <div class="mt-3 p-3 rounded-3 text-start" style="background:#f8f5ff; font-size:13px; color:#6b7280;">
                    <i class="fas fa-sticky-note text-muted me-1"></i>{{ $cliente->notas }}
                </div>
                @endif

                <div class="mt-4 d-grid gap-2">
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar Cliente
                    </a>
                    <a href="{{ route('ventas.create') }}?cliente={{ $cliente->id }}" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Nueva Venta
                    </a>
                    <a href="{{ route('reparaciones.create') }}?cliente={{ $cliente->id }}" class="btn btn-outline-secondary">
                        <i class="fas fa-tools me-2"></i>Nueva Reparación
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Estadísticas</h6>
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div style="background:#ede9fe; border-radius:12px; padding:16px;">
                            <div style="font-size:22px; font-weight:700; color:#7c3aed;">
                                {{ $cliente->ventas->count() }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">Compras</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#d1fae5; border-radius:12px; padding:16px;">
                            <div style="font-size:22px; font-weight:700; color:#059669;">
                                {{ $config->simbolo_moneda }} {{ number_format($cliente->totalCompras(), 0) }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">Total gastado</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#fef3c7; border-radius:12px; padding:16px;">
                            <div style="font-size:22px; font-weight:700; color:#d97706;">
                                {{ $cliente->reparaciones->count() }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">Reparaciones</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#f0f9ff; border-radius:12px; padding:16px;">
                            <div style="font-size:14px; font-weight:700; color:#0369a1;">
                                {{ $cliente->created_at->format('M Y') }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">Desde</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial --}}
    <div class="col-lg-8">
        {{-- Ventas --}}
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Historial de Compras</h6>
                @if($cliente->ventas->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>N° Venta</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cliente->ventas->sortByDesc('fecha_venta') as $v)
                            <tr>
                                <td style="color:#a855f7; font-weight:600;">{{ $v->numero_venta }}</td>
                                <td>{{ $v->fecha_venta->format('d/m/Y') }}</td>
                                <td>{{ $v->detalles->count() }} ítem(s)</td>
                                <td style="font-weight:600;">{{ $config->simbolo_moneda }} {{ number_format($v->total, 2) }}</td>
                                <td>
                                    @php $cfg=['completada'=>['#d1fae5','#065f46'],'cancelada'=>['#fee2e2','#991b1b'],'pendiente'=>['#fef3c7','#92400e']]; $c=$cfg[$v->estado]??['#f3f4f6','#374151']; @endphp
                                    <span style="background:{{ $c[0] }}; color:{{ $c[1] }}; border-radius:20px; padding:3px 8px; font-size:11px;">{{ ucfirst($v->estado) }}</span>
                                </td>
                                <td><a href="{{ route('ventas.show', $v) }}" style="color:#a855f7; font-size:12px;">Ver</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted" style="font-size:13px;">
                    <i class="fas fa-shopping-cart fa-2x mb-2 d-block opacity-40"></i>Sin compras registradas
                </div>
                @endif
            </div>
        </div>

        {{-- Reparaciones --}}
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Historial de Reparaciones</h6>
                @if($cliente->reparaciones->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>N° Orden</th>
                                <th>Dispositivo</th>
                                <th>Falla</th>
                                <th>Estado</th>
                                <th>Costo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cliente->reparaciones->sortByDesc('fecha_recepcion') as $r)
                            <tr>
                                <td style="color:#a855f7; font-weight:600;">{{ $r->numero_orden }}</td>
                                <td>{{ $r->dispositivo }}<div style="font-size:11px;color:#9ca3af;">{{ $r->marca }} {{ $r->modelo }}</div></td>
                                <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $r->falla_reportada }}</td>
                                <td>
                                    @php $label=str_replace('_',' ',ucfirst($r->estado)); @endphp
                                    <span style="background:#ede9fe; color:#7c3aed; border-radius:20px; padding:3px 8px; font-size:11px;">{{ $label }}</span>
                                </td>
                                <td style="font-weight:600;">{{ $r->costo_final > 0 ? $config->simbolo_moneda.' '.number_format($r->costo_final,2) : '—' }}</td>
                                <td><a href="{{ route('reparaciones.show', $r) }}" style="color:#a855f7; font-size:12px;">Ver</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted" style="font-size:13px;">
                    <i class="fas fa-tools fa-2x mb-2 d-block opacity-40"></i>Sin reparaciones registradas
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
