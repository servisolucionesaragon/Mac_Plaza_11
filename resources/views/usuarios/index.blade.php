@extends('layouts.app')
@section('title', 'Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')

<!-- ── Header ── -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e1b4b;">Usuarios</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Gestión de usuarios y permisos por rol</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
        <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
    </button>
</div>

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
                        <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $usuario->activo ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                    style="border-radius:8px;padding:4px 10px;"
                                    title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }} usuario">
                                <i class="fas fa-{{ $usuario->activo ? 'ban' : 'check' }}" style="font-size:12px;"></i>
                            </button>
                        </form>

                        <!-- Eliminar -->
                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" style="display:inline;"
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

        <form action="{{ route('usuarios.updatePermisos') }}" method="POST">
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
                            'caja'         => 'Control de Caja',
                            'gastos'       => 'Gastos',
                            'ingresos'     => 'Ingresos',
                            'reparaciones' => 'Reparaciones',
                            'reportes'     => 'Reportes',
                            'usuarios'     => 'Usuarios',
                            'configuracion' => 'Configuración',
                            'backup'       => 'Backup & Restauración',
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
            <form action="{{ route('usuarios.store') }}" method="POST">
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
const baseUsuariosUrl = '{{ url('/usuarios') }}';

function abrirModalEditar(id, nombre, email, rol, telefono) {
    document.getElementById('editNombre').value   = nombre;
    document.getElementById('editEmail').value    = email;
    document.getElementById('editRol').value      = rol;
    document.getElementById('editTelefono').value = telefono || '';
    document.getElementById('formEditarUsuario').action = baseUsuariosUrl + '/' + id;
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
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalNuevoUsuario')).show();
    });
@endif
</script>
@endpush
