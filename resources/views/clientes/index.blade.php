@extends('layouts.app')
@section('title', 'Clientes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Clientes</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Clientes</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Gestiona tu cartera de clientes</p>
    </div>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary px-4">
        <i class="fas fa-plus me-2"></i>Nuevo Cliente
    </a>
</div>

@if($cumpleanioMesCount > 0)
<a href="{{ route('clientes.index', ['cumpleanio' => 1]) }}" class="text-decoration-none">
    <div class="mb-4 p-3 rounded-3 d-flex align-items-center gap-3" style="background:#fdf2f8; border:1px solid #fbcfe8;">
        <div style="width:38px; height:38px; border-radius:50%; background:#ec4899; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-birthday-cake" style="color:#fff;"></i>
        </div>
        <div>
            <div style="font-weight:600; font-size:13.5px; color:#9d174d;">
                🎂 {{ $cumpleanioMesCount }} cliente(s) de cumpleaños este mes
            </div>
            <div style="font-size:12px; color:#be185d;">Ideal para ofrecer un descuento o promoción de fidelización — clic para verlos</div>
        </div>
    </div>
</a>
@endif

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    <input type="text" class="form-control" name="buscar"
                           placeholder="Buscar por nombre, email, DNI, teléfono..."
                           value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="tipo">
                    <option value="">Todos los tipos</option>
                    <option value="particular" {{ request('tipo')=='particular'?'selected':'' }}>Particular</option>
                    <option value="empresa" {{ request('tipo')=='empresa'?'selected':'' }}>Empresa</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="cumpleanio" value="1" id="filtroCumpleanio"
                           {{ request('cumpleanio') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label" for="filtroCumpleanio" style="font-size:12.5px;">
                        🎂 Cumpleaños este mes
                    </label>
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Tipo</th>
                        <th>Ventas</th>
                        <th>Reparaciones</th>
                        <th>Registrado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr>
                        <td class="ps-4" style="color:#9ca3af; font-size:12px;">{{ $cliente->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:38px; height:38px; border-radius:50%;
                                    background: linear-gradient(135deg,#a855f7,#ec4899);
                                    display:flex; align-items:center; justify-content:center;
                                    color:#fff; font-weight:600; font-size:14px; flex-shrink:0;">
                                    {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500; font-size:13.5px;">
                                        {{ $cliente->nombre_completo }}
                                        @if($cliente->cumpleAnioEsteMes())
                                            <span title="Cumpleaños este mes ({{ $cliente->fecha_nacimiento->format('d/m') }})">🎂</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px; color:#9ca3af;">{{ $cliente->email ?? 'Sin email' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;">
                            <div>{{ $cliente->telefono }}</div>
                            @if($cliente->dni)
                                <div style="font-size:11px; color:#9ca3af;">{{ $cliente->tipo_documento ?? 'Documento' }}: {{ $cliente->dni }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $cliente->tipo=='empresa'?'bg-info':'bg-secondary' }}"
                                  style="border-radius:20px; font-size:11px; padding:4px 10px;">
                                {{ ucfirst($cliente->tipo) }}
                            </span>
                        </td>
                        <td style="font-size:13px;">
                            <span class="badge bg-light text-dark" style="border-radius:20px; font-size:12px; padding:4px 10px;">
                                {{ $cliente->ventas_count }}
                            </span>
                        </td>
                        <td style="font-size:13px;">
                            <span class="badge bg-light text-dark" style="border-radius:20px; font-size:12px; padding:4px 10px;">
                                {{ $cliente->reparaciones_count }}
                            </span>
                        </td>
                        <td style="font-size:12px; color:#9ca3af;">
                            {{ $cliente->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                @if($cliente->cumpleAnioEsteMes() && $cliente->numeroWhatsapp())
                                @php
                                    $mensajeCumple = "¡Hola {$cliente->nombre}! Este es tu mes de cumpleaños y de parte de *"
                                        . ($config->nombre_tienda ?? 'nuestra tienda')
                                        . "* queremos celebrar contigo con un descuento especial en tu próxima compra. ¡Te esperamos!";
                                @endphp
                                <a href="{{ $cliente->whatsappUrl($mensajeCumple) }}" target="_blank" rel="noopener"
                                   class="btn btn-sm" style="background:#25D366; color:#fff; border-radius:8px; padding:5px 10px;" title="Enviar saludo de cumpleaños por WhatsApp">
                                    <i class="fab fa-whatsapp fa-sm"></i>
                                </a>
                                @endif
                                <a href="{{ route('clientes.show', $cliente) }}"
                                   class="btn btn-sm" style="background:#f3f4f6; color:#374151; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $cliente) }}"
                                   class="btn btn-sm" style="background:#ede9fe; color:#7c3aed; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-edit fa-sm"></i>
                                </a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar cliente {{ addslashes($cliente->nombre_completo) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm"
                                            style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:5px 10px;">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                            <p class="text-muted mb-2">No hay clientes registrados</p>
                            <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Agregar primer cliente
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clientes->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px;">
                Mostrando {{ $clientes->firstItem() }}–{{ $clientes->lastItem() }} de {{ $clientes->total() }} clientes
            </span>
            {{ $clientes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
