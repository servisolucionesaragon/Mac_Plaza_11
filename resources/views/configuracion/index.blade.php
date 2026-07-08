@extends('layouts.app')
@section('title', 'Configuración')

@section('breadcrumb')
    <li class="breadcrumb-item active">Configuración</li>
@endsection

@section('content')

<!-- ── Header ── -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e1b4b;">Configuración del Sistema</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Gestión de usuarios y parámetros generales</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
        <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
    </button>
</div>

<div class="row g-4">

    <!-- ══════════ COLUMNA IZQUIERDA: Info del sistema ══════════ -->
    <div class="col-lg-4">

        <!-- Tarjeta de Configuración General -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#a855f7,#ec4899);
                                border-radius:14px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-store" style="color:#fff;font-size:22px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Configuración General</h6>
                        <small class="text-muted">Datos del portal y parámetros del sistema</small>
                    </div>
                </div>

                @if($errors->any() && old('_form') === 'general')
                    <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('configuracion.updateGeneral') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="general">

                    {{-- Logo --}}
                    <div class="text-center mb-4">
                        <div id="logoPreviewWrap" style="width:88px;height:88px;border-radius:16px;margin:0 auto 10px;
                                    background:linear-gradient(135deg,#a855f7,#ec4899);display:flex;align-items:center;
                                    justify-content:center;overflow:hidden;">
                            @if($config->logo)
                                <img id="logoPreview" src="{{ asset('storage/' . $config->logo) }}"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i id="logoIcon" class="fas fa-mobile-alt" style="color:#fff;font-size:32px;"></i>
                                <img id="logoPreview" src="" style="display:none;width:100%;height:100%;object-fit:cover;">
                            @endif
                        </div>
                        <label for="logoInput" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                            <i class="fas fa-camera me-1"></i>Cambiar logo
                        </label>
                        <input type="file" id="logoInput" name="logo" accept="image/*" style="display:none;"
                               onchange="previewLogo(this)">
                        @error('logo')
                            <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del Portal <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_tienda" class="form-control @error('nombre_tienda') is-invalid @enderror"
                               value="{{ old('nombre_tienda', $config->nombre_tienda) }}" required>
                        @error('nombre_tienda')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">NIT</label>
                            <input type="text" name="ruc" class="form-control @error('ruc') is-invalid @enderror"
                                   value="{{ old('ruc', $config->ruc) }}" placeholder="900123456-7">
                            @error('ruc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', $config->telefono) }}" placeholder="300 123 4567">
                            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <select name="departamento" id="cfgDepartamento" class="form-select @error('departamento') is-invalid @enderror">
                                <option value="">— Seleccionar —</option>
                                @foreach(array_keys(config('colombia')) as $depto)
                                    <option value="{{ $depto }}" {{ old('departamento', $config->departamento) == $depto ? 'selected' : '' }}>
                                        {{ $depto }}
                                    </option>
                                @endforeach
                            </select>
                            @error('departamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad</label>
                            <select name="ciudad" id="cfgCiudad" class="form-select @error('ciudad') is-invalid @enderror">
                            </select>
                            @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                               value="{{ old('direccion', $config->direccion) }}" placeholder="Cra 10 # 20-30">
                        @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $config->email) }}" placeholder="contacto@negocio.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Página web</label>
                            <input type="text" name="pagina_web" class="form-control @error('pagina_web') is-invalid @enderror"
                                   value="{{ old('pagina_web', $config->pagina_web) }}" placeholder="www.negocio.com">
                            @error('pagina_web')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Zona Horaria <span class="text-danger">*</span></label>
                        <select name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                            @php
                                $zonas = [
                                    'America/Bogota'              => 'Bogotá, Colombia (UTC-5)',
                                    'America/Lima'                => 'Lima, Perú (UTC-5)',
                                    'America/Mexico_City'         => 'Ciudad de México (UTC-6)',
                                    'America/Santiago'            => 'Santiago, Chile (UTC-4/-3)',
                                    'America/Argentina/Buenos_Aires' => 'Buenos Aires, Argentina (UTC-3)',
                                ];
                            @endphp
                            @foreach($zonas as $valor => $etiqueta)
                                <option value="{{ $valor }}" {{ old('timezone', $config->timezone) == $valor ? 'selected' : '' }}>
                                    {{ $etiqueta }}
                                </option>
                            @endforeach
                        </select>
                        @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-7">
                            <label class="form-label">Moneda <span class="text-danger">*</span></label>
                            @php
                                $monedas = [
                                    'COP' => 'Peso Colombiano (COP)',
                                    'PEN' => 'Sol Peruano (PEN)',
                                    'USD' => 'Dólar Americano (USD)',
                                    'MXN' => 'Peso Mexicano (MXN)',
                                    'CLP' => 'Peso Chileno (CLP)',
                                    'ARS' => 'Peso Argentino (ARS)',
                                ];
                            @endphp
                            <select name="moneda" class="form-select @error('moneda') is-invalid @enderror" required>
                                @foreach($monedas as $valor => $etiqueta)
                                    <option value="{{ $valor }}" {{ old('moneda', $config->moneda) == $valor ? 'selected' : '' }}>
                                        {{ $etiqueta }}
                                    </option>
                                @endforeach
                            </select>
                            @error('moneda')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-5">
                            <label class="form-label">Símbolo <span class="text-danger">*</span></label>
                            <input type="text" name="simbolo_moneda" class="form-control @error('simbolo_moneda') is-invalid @enderror"
                                   value="{{ old('simbolo_moneda', $config->simbolo_moneda) }}" maxlength="10" required>
                            @error('simbolo_moneda')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">% de Impuesto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="igv" class="form-control @error('igv') is-invalid @enderror"
                                   value="{{ old('igv', $config->igv) }}" min="0" max="100" step="0.01" required>
                            <span class="input-group-text">%</span>
                        </div>
                        @error('igv')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <label class="form-label">Colores de la Plataforma</label>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px;">Primario</label>
                            <input type="color" name="color_primario" class="form-control form-control-color w-100 @error('color_primario') is-invalid @enderror"
                                   value="{{ old('color_primario', $config->color_primario) }}" title="Color primario">
                            @error('color_primario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px;">Secundario</label>
                            <input type="color" name="color_secundario" class="form-control form-control-color w-100 @error('color_secundario') is-invalid @enderror"
                                   value="{{ old('color_secundario', $config->color_secundario) }}" title="Color secundario">
                            @error('color_secundario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px;">Acento</label>
                            <input type="color" name="color_acento" class="form-control form-control-color w-100 @error('color_acento') is-invalid @enderror"
                                   value="{{ old('color_acento', $config->color_acento) }}" title="Color de acento">
                            @error('color_acento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px;">Fondo del sidebar</label>
                            <input type="color" name="color_sidebar" class="form-control form-control-color w-100 @error('color_sidebar') is-invalid @enderror"
                                   value="{{ old('color_sidebar', $config->color_sidebar) }}" title="Color de fondo del sidebar">
                            @error('color_sidebar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Guardar Configuración
                    </button>
                </form>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="card mb-4">
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

        <!-- Información del sistema -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Información del Sistema</h6>
                <div style="font-size:13px;">
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Versión</span>
                        <span class="fw-500">1.0.0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Framework</span>
                        <span class="fw-500">Laravel 10</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Base de datos</span>
                        <span class="fw-500">MySQL</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ══════════ COLUMNA DERECHA: Gestión de usuarios ══════════ -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="fw-bold mb-0">Gestión de Usuarios</h6>
                    <span style="background:#ede9fe;color:#7c3aed;border-radius:20px;padding:3px 12px;font-size:12px;">
                        {{ $usuarios->count() }} usuarios
                    </span>
                </div>

                <!-- Leyenda de roles -->
                <div class="d-flex gap-3 mb-4" style="font-size:12px;">
                    <span><span style="display:inline-block;width:10px;height:10px;background:#a855f7;border-radius:50%;margin-right:4px;"></span>Admin</span>
                    <span><span style="display:inline-block;width:10px;height:10px;background:#06b6d4;border-radius:50%;margin-right:4px;"></span>Vendedor</span>
                    <span><span style="display:inline-block;width:10px;height:10px;background:#f59e0b;border-radius:50%;margin-right:4px;"></span>Técnico</span>
                </div>

                <div class="row g-3">
                    @foreach($usuarios as $usuario)
                    @php
                        $rolColor = ['admin'=>'#a855f7','vendedor'=>'#06b6d4','tecnico'=>'#f59e0b'][$usuario->rol] ?? '#9ca3af';
                        $rolBg    = ['admin'=>'#ede9fe','vendedor'=>'#e0f2fe','tecnico'=>'#fef3c7'][$usuario->rol] ?? '#f3f4f6';
                        $rolTxt   = ['admin'=>'#7c3aed','vendedor'=>'#0369a1','tecnico'=>'#92400e'][$usuario->rol] ?? '#374151';
                        $inicial  = strtoupper(substr($usuario->name, 0, 1));
                    @endphp
                    <div class="col-12">
                        <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                             style="background:#f9fafb;border:1px solid #f3f4f6;transition:all .2s;"
                             onmouseenter="this.style.borderColor='#e9d5ff'"
                             onmouseleave="this.style.borderColor='#f3f4f6'">

                            <!-- Avatar -->
                            <div style="width:44px;height:44px;background:{{ $rolColor }};border-radius:12px;
                                        display:flex;align-items:center;justify-content:center;
                                        color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                                {{ $inicial }}
                            </div>

                            <!-- Info -->
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-600" style="font-size:14px;font-weight:600;">{{ $usuario->name }}</span>
                                    <span style="background:{{ $rolBg }};color:{{ $rolTxt }};
                                                 border-radius:20px;padding:2px 8px;font-size:11px;">
                                        {{ ucfirst($usuario->rol) }}
                                    </span>
                                    @if($usuario->id === auth()->id())
                                        <span style="background:#d1fae5;color:#065f46;border-radius:20px;padding:2px 8px;font-size:11px;">
                                            Tú
                                        </span>
                                    @endif
                                    @if(!$usuario->activo)
                                        <span style="background:#fee2e2;color:#991b1b;border-radius:20px;padding:2px 8px;font-size:11px;">
                                            Inactivo
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size:12px;color:#9ca3af;margin-top:2px;">
                                    <i class="fas fa-envelope me-1"></i>{{ $usuario->email }}
                                    @if($usuario->telefono)
                                        &nbsp;·&nbsp;<i class="fas fa-phone me-1"></i>{{ $usuario->telefono }}
                                    @endif
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <!-- Editar -->
                                <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:4px 10px;"
                                        title="Editar usuario"
                                        onclick="abrirModalEditar({{ $usuario->id }}, '{{ addslashes($usuario->name) }}', '{{ $usuario->email }}', '{{ $usuario->rol }}', '{{ $usuario->telefono }}')">
                                    <i class="fas fa-edit" style="font-size:12px;"></i>
                                </button>

                                @if($usuario->id !== auth()->id())
                                <!-- Toggle activo -->
                                <form action="{{ route('configuracion.toggleUsuario', $usuario) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $usuario->activo ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            style="border-radius:8px;padding:4px 10px;"
                                            title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }} usuario">
                                        <i class="fas fa-{{ $usuario->activo ? 'ban' : 'check' }}" style="font-size:12px;"></i>
                                    </button>
                                </form>

                                <!-- Eliminar -->
                                <form action="{{ route('configuracion.destroyUsuario', $usuario) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($usuario->name) }}? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="border-radius:8px;padding:4px 10px;"
                                            title="Eliminar usuario">
                                        <i class="fas fa-trash" style="font-size:12px;"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Permisos de Roles -->
        <div class="card mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1"><i class="fas fa-shield-alt me-2" style="color:#a855f7;"></i>Permisos de Roles</h6>
                <p class="text-muted mb-4" style="font-size:13px;">Define a qué módulos tiene acceso cada rol. El Administrador siempre tiene acceso completo.</p>

                <form action="{{ route('configuracion.updatePermisos') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="table-responsive">
                        <table class="table align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>Módulo</th>
                                    <th class="text-center">Admin</th>
                                    <th class="text-center">Vendedor</th>
                                    <th class="text-center">Técnico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    'dashboard'    => 'Dashboard',
                                    'clientes'     => 'Clientes',
                                    'productos'    => 'Inventario',
                                    'ventas'       => 'Ventas',
                                    'reparaciones' => 'Reparaciones',
                                    'reportes'     => 'Reportes',
                                ] as $modulo => $label)
                                <tr>
                                    <td style="font-size:13.5px;font-weight:500;">{{ $label }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" checked disabled title="Admin siempre tiene acceso completo">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="permisos[vendedor][{{ $modulo }}]" value="1"
                                               {{ ($permisosMatriz['vendedor.'.$modulo]->permitido ?? false) ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="permisos[tecnico][{{ $modulo }}]" value="1"
                                               {{ ($permisosMatriz['tecnico.'.$modulo]->permitido ?? false) ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Permisos
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Nuevo Usuario ══════════ -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-user-plus me-2" style="color:#a855f7;"></i>Nuevo Usuario
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('configuracion.storeUsuario') }}" method="POST">
                @csrf
                <div class="modal-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                   placeholder="Ej: María García" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                   placeholder="usuario@tienda.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="nuevaPassword" class="form-control" required minlength="8">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('nuevaPassword','eyeNueva')">
                                    <i class="fas fa-eye" id="eyeNueva" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('confirmPassword','eyeConfirm')">
                                    <i class="fas fa-eye" id="eyeConfirm" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select name="rol" class="form-select" required>
                                <option value="">Seleccionar rol...</option>
                                <option value="admin"    {{ old('rol')=='admin'?'selected':'' }}>👑 Administrador</option>
                                <option value="vendedor" {{ old('rol')=='vendedor'?'selected':'' }}>🛒 Vendedor</option>
                                <option value="tecnico"  {{ old('rol')=='tecnico'?'selected':'' }}>🔧 Técnico</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}"
                                   placeholder="+51 999 999 999">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Editar Usuario ══════════ -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-user-edit me-2" style="color:#a855f7;"></i>Editar Usuario
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarUsuario" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editNombre" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="editPassword" class="form-control" minlength="8"
                                       placeholder="Dejar vacío para no cambiar">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('editPassword','eyeEdit')">
                                    <i class="fas fa-eye" id="eyeEdit" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Repetir nueva contraseña">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select name="rol" id="editRol" class="form-select" required>
                                <option value="admin">👑 Administrador</option>
                                <option value="vendedor">🛒 Vendedor</option>
                                <option value="tecnico">🔧 Técnico</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirModalEditar(id, nombre, email, rol, telefono) {
    document.getElementById('editNombre').value   = nombre;
    document.getElementById('editEmail').value    = email;
    document.getElementById('editRol').value      = rol;
    document.getElementById('editTelefono').value = telefono || '';
    document.getElementById('formEditarUsuario').action = '/configuracion/usuarios/' + id;
    var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    modal.show();
}

function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Auto-open modal si hay errores de validación (al crear usuario)
@if($errors->any() && old('_form') !== 'general')
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalNuevoUsuario')).show();
    });
@endif

function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('logoPreview');
            const icon = document.getElementById('logoIcon');
            img.src = e.target.result;
            img.style.display = 'block';
            if (icon) icon.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

const colombiaDataConfig = @json(config('colombia'));
const cfgDeptoSelect  = document.getElementById('cfgDepartamento');
const cfgCiudadSelect = document.getElementById('cfgCiudad');

function poblarCiudadesConfig(depto, ciudadPreseleccionada) {
    const ciudades = colombiaDataConfig[depto] || [];
    cfgCiudadSelect.innerHTML = '<option value="">— Seleccionar —</option>';
    ciudades.forEach(function (c) {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        if (c === ciudadPreseleccionada) opt.selected = true;
        cfgCiudadSelect.appendChild(opt);
    });
    if (ciudadPreseleccionada && !ciudades.includes(ciudadPreseleccionada)) {
        const opt = document.createElement('option');
        opt.value = ciudadPreseleccionada;
        opt.textContent = ciudadPreseleccionada + ' (valor guardado, no está en la lista)';
        opt.selected = true;
        cfgCiudadSelect.appendChild(opt);
    }
}

cfgDeptoSelect.addEventListener('change', function () { poblarCiudadesConfig(cfgDeptoSelect.value, null); });

poblarCiudadesConfig(cfgDeptoSelect.value, {{ Illuminate\Support\Js::from(old('ciudad', $config->ciudad)) }});
</script>
@endpush
