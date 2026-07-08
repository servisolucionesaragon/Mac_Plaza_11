@extends('layouts.app')
@section('title', 'Editar Cliente')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}" style="color:#a855f7;">Clientes</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Editar Cliente</h5>
                <p class="text-muted mb-4" style="font-size:13px;">{{ $cliente->nombre_completo }}</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label">Tipo de Cliente</label>
                        <div class="d-flex gap-3">
                            <label style="padding:10px 20px; border:1.5px solid {{ old('tipo',$cliente->tipo)=='particular'?'#a855f7':'#e5e7eb' }}; border-radius:10px; cursor:pointer; flex:1;">
                                <input type="radio" name="tipo" value="particular" style="accent-color:#a855f7;"
                                       {{ old('tipo',$cliente->tipo)=='particular'?'checked':'' }}>
                                <span style="font-size:13.5px; margin-left:6px;"><i class="fas fa-user me-1 text-muted"></i> Particular</span>
                            </label>
                            <label style="padding:10px 20px; border:1.5px solid {{ old('tipo',$cliente->tipo)=='empresa'?'#a855f7':'#e5e7eb' }}; border-radius:10px; cursor:pointer; flex:1;">
                                <input type="radio" name="tipo" value="empresa" style="accent-color:#a855f7;"
                                       {{ old('tipo',$cliente->tipo)=='empresa'?'checked':'' }}>
                                <span style="font-size:13.5px; margin-left:6px;"><i class="fas fa-building me-1 text-muted"></i> Empresa</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   name="nombre" value="{{ old('nombre', $cliente->nombre) }}">
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                   name="apellido" value="{{ old('apellido', $cliente->apellido) }}">
                            @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email', $cliente->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Celular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="telefono"
                                   value="{{ old('telefono', $cliente->telefono) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Celular 2</label>
                            <input type="text" class="form-control" name="celular"
                                   value="{{ old('celular', $cliente->celular) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tipo de Documento</label>
                            <select class="form-select @error('tipo_documento') is-invalid @enderror" name="tipo_documento">
                                <option value="">Seleccionar...</option>
                                @foreach(['Tarjeta de identidad','Cédula de ciudadanía','Tarjeta de extranjería','Cédula de extranjería','NIT','Pasaporte'] as $tipoDoc)
                                    <option value="{{ $tipoDoc }}" {{ old('tipo_documento', $cliente->tipo_documento) == $tipoDoc ? 'selected' : '' }}>
                                        {{ $tipoDoc }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_documento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Número de Documento</label>
                            <input type="text" class="form-control @error('dni') is-invalid @enderror"
                                   name="dni" value="{{ old('dni', $cliente->dni) }}">
                            @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento', optional($cliente->fecha_nacimiento)->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="departamento" id="departamento">
                                @foreach(array_keys(config('colombia')) as $depto)
                                    <option value="{{ $depto }}" {{ old('departamento', $cliente->departamento ?? 'Antioquia') == $depto ? 'selected' : '' }}>
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
                                   value="{{ old('direccion', $cliente->direccion) }}">
                        </div>
                    </div>

                    <div id="datos-empresa" style="display:{{ old('tipo',$cliente->tipo)=='empresa'?'block':'none' }};">
                        <hr>
                        <h6 class="fw-600 mb-3" style="font-weight:600;">Datos de Empresa</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Razón Social</label>
                                <input type="text" class="form-control" name="empresa"
                                       value="{{ old('empresa', $cliente->empresa) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">RUT/NIT</label>
                                <input type="text" class="form-control" name="ruc"
                                       value="{{ old('ruc', $cliente->ruc) }}">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" rows="3">{{ old('notas', $cliente->notas) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select name="activo" class="form-select">
                                <option value="1" {{ $cliente->activo?'selected':'' }}>Activo</option>
                                <option value="0" {{ !$cliente->activo?'selected':'' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Actualizar Cliente
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
document.querySelectorAll('input[name="tipo"]').forEach(r =>
    r.addEventListener('change', function() {
        document.getElementById('datos-empresa').style.display = this.value === 'empresa' ? 'block' : 'none';
    })
);

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

poblarCiudades(deptoSelect.value, {{ Illuminate\Support\Js::from(old('ciudad', $cliente->ciudad)) }});
</script>
@endpush
