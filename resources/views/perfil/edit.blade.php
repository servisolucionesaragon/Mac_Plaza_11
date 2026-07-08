@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mi Perfil</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e1b4b;">Mi Perfil</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Edita tus datos básicos y tu contraseña</p>
    </div>
</div>

<div class="row g-4">

    <!-- ══════════ Datos básicos ══════════ -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#a855f7,#ec4899);
                                border-radius:14px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user" style="color:#fff;font-size:22px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Datos básicos</h6>
                        <small class="text-muted">Nombre, correo electrónico y teléfono</small>
                    </div>
                </div>

                @if($errors->any() && old('_form') === 'datos')
                    <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perfil.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="datos">

                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono', auth()->user()->telefono) }}" placeholder="300 123 4567">
                        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar cambios
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ══════════ Cambiar contraseña ══════════ -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#06b6d4,#0284c7);
                                border-radius:14px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-lock" style="color:#fff;font-size:22px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Cambiar contraseña</h6>
                        <small class="text-muted">Requiere confirmar tu contraseña actual</small>
                    </div>
                </div>

                @if($errors->any() && old('_form') === 'password')
                    <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perfil.updatePassword') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="password">

                    <div class="mb-3">
                        <label class="form-label">Contraseña actual <span class="text-danger">*</span></label>
                        <input type="password" name="password_actual"
                               class="form-control @error('password_actual') is-invalid @enderror" required>
                        @error('password_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted" style="font-size:12px;">Mínimo 8 caracteres.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Confirmar nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>Actualizar contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
