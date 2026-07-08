@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}" style="color:#a855f7;">Clientes</a></li>
    <li class="breadcrumb-item active">Nuevo Cliente</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Registrar Nuevo Cliente</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Completa los datos del cliente</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf

                    {{-- Tipo de cliente --}}
                    <div class="mb-4">
                        <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <label class="d-flex align-items-center gap-2 cursor-pointer"
                                   style="padding:10px 20px; border:1.5px solid #e5e7eb; border-radius:10px; cursor:pointer; flex:1; transition:.2s;"
                                   onclick="this.style.borderColor='#a855f7'; document.getElementById('tipo_empresa').style.borderColor='#e5e7eb';">
                                <input type="radio" name="tipo" value="particular"
                                       {{ old('tipo','particular')=='particular'?'checked':'' }}
                                       style="accent-color:#a855f7;">
                                <span style="font-size:13.5px;">
                                    <i class="fas fa-user me-1 text-muted"></i> Particular
                                </span>
                            </label>
                            <label id="tipo_empresa" class="d-flex align-items-center gap-2"
                                   style="padding:10px 20px; border:1.5px solid #e5e7eb; border-radius:10px; cursor:pointer; flex:1; transition:.2s;"
                                   onclick="this.style.borderColor='#a855f7'; document.querySelector('[value=particular]').parentElement.style.borderColor='#e5e7eb';">
                                <input type="radio" name="tipo" value="empresa"
                                       {{ old('tipo')=='empresa'?'checked':'' }}
                                       style="accent-color:#a855f7;">
                                <span style="font-size:13.5px;">
                                    <i class="fas fa-building me-1 text-muted"></i> Empresa
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Datos personales --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   name="nombre" value="{{ old('nombre') }}" placeholder="Nombre">
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                   name="apellido" value="{{ old('apellido') }}" placeholder="Apellido">
                            @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Celular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                   name="telefono" value="{{ old('telefono') }}" placeholder="999 999 999">
                            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Celular 2</label>
                            <input type="text" class="form-control" name="celular"
                                   value="{{ old('celular') }}" placeholder="999 999 999">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tipo de Documento</label>
                            <select class="form-select @error('tipo_documento') is-invalid @enderror" name="tipo_documento">
                                <option value="">Seleccionar...</option>
                                @foreach(['Tarjeta de identidad','Cédula de ciudadanía','Tarjeta de extranjería','Cédula de extranjería','NIT','Pasaporte'] as $tipoDoc)
                                    <option value="{{ $tipoDoc }}" {{ old('tipo_documento') == $tipoDoc ? 'selected' : '' }}>
                                        {{ $tipoDoc }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_documento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Número de Documento</label>
                            <input type="text" class="form-control @error('dni') is-invalid @enderror"
                                   name="dni" value="{{ old('dni') }}" placeholder="12345678">
                            @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="departamento" id="departamento">
                                @foreach(array_keys(config('colombia')) as $depto)
                                    <option value="{{ $depto }}" {{ old('departamento', 'Antioquia') == $depto ? 'selected' : '' }}>
                                        {{ $depto }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad</label>
                            <select class="form-select" name="ciudad" id="ciudad">
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion"
                                   value="{{ old('direccion') }}" placeholder="Av. Ejemplo 123">
                        </div>
                    </div>

                    {{-- Datos empresa (condicional) --}}
                    <div id="datos-empresa" style="display:{{ old('tipo')=='empresa'?'block':'none' }};">
                        <hr>
                        <h6 class="fw-600 mb-3" style="font-weight:600;">Datos de Empresa</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Razón Social</label>
                                <input type="text" class="form-control" name="empresa"
                                       value="{{ old('empresa') }}" placeholder="Empresa SAC">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">RUT/NIT</label>
                                <input type="text" class="form-control" name="ruc"
                                       value="{{ old('ruc') }}" placeholder="20123456789">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Notas internas</label>
                        <textarea class="form-control" name="notas" rows="3"
                                  placeholder="Observaciones, preferencias del cliente...">{{ old('notas') }}</textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary px-4">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const radios = document.querySelectorAll('input[name="tipo"]');
    const datosEmpresa = document.getElementById('datos-empresa');

    radios.forEach(r => r.addEventListener('change', function() {
        datosEmpresa.style.display = this.value === 'empresa' ? 'block' : 'none';
    }));

    const colombiaData = @json(config('colombia'));
    const deptoSelect = document.getElementById('departamento');
    const ciudadSelect = document.getElementById('ciudad');

    function poblarCiudades(depto, ciudadPreseleccionada) {
        const ciudades = colombiaData[depto] || [];
        ciudadSelect.innerHTML = '';
        ciudades.forEach(function (c) {
            const opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            if (c === ciudadPreseleccionada) opt.selected = true;
            ciudadSelect.appendChild(opt);
        });
        if (ciudadPreseleccionada && !ciudades.includes(ciudadPreseleccionada)) {
            const opt = document.createElement('option');
            opt.value = ciudadPreseleccionada;
            opt.textContent = ciudadPreseleccionada + ' (valor guardado, no está en la lista)';
            opt.selected = true;
            ciudadSelect.appendChild(opt);
        }
    }

    deptoSelect.addEventListener('change', function () { poblarCiudades(deptoSelect.value, null); });

    poblarCiudades(deptoSelect.value, {{ Illuminate\Support\Js::from(old('ciudad', 'Caucasia')) }});
</script>
@endpush
