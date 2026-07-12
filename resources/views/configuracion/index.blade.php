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
        <p class="text-muted mb-0" style="font-size:13px;">Parámetros generales de la plataforma</p>
    </div>
</div>

<div class="row g-4 justify-content-center">

    <div class="col-lg-8">

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

                    <ul class="nav nav-tabs mb-3" id="configGeneralTab" role="tablist" style="border-bottom:1px solid #f3f4f6;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-empresa-btn" data-bs-toggle="tab" data-bs-target="#tab-empresa" type="button" role="tab" aria-controls="tab-empresa">
                                <i class="fas fa-store me-2"></i>Empresa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-logo-btn" data-bs-toggle="tab" data-bs-target="#tab-logo" type="button" role="tab" aria-controls="tab-logo">
                                <i class="fas fa-image me-2"></i>Logo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-colores-btn" data-bs-toggle="tab" data-bs-target="#tab-colores" type="button" role="tab" aria-controls="tab-colores">
                                <i class="fas fa-palette me-2"></i>Colores
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-moneda-btn" data-bs-toggle="tab" data-bs-target="#tab-moneda" type="button" role="tab" aria-controls="tab-moneda">
                                <i class="fas fa-coins me-2"></i>Moneda &amp; Impuestos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-parametro-cliente-btn" data-bs-toggle="tab" data-bs-target="#tab-parametro-cliente" type="button" role="tab" aria-controls="tab-parametro-cliente">
                                <i class="fas fa-user-tag me-2"></i>Parámetro de Cliente
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">

                        {{-- ═══ EMPRESA ═══ --}}
                        <div class="tab-pane fade show active" id="tab-empresa" role="tabpanel">
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

                            <div class="mb-1">
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
                        </div>

                        {{-- ═══ LOGO ═══ --}}
                        <div class="tab-pane fade" id="tab-logo" role="tabpanel">
                            <div class="text-center mb-2 py-3">
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
                        </div>

                        {{-- ═══ COLORES ═══ --}}
                        <div class="tab-pane fade" id="tab-colores" role="tabpanel">
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
                                <div class="col-6">
                                    <label class="form-label" style="font-size:12px;">Texto del menú</label>
                                    <input type="color" name="color_menu_texto" class="form-control form-control-color w-100 @error('color_menu_texto') is-invalid @enderror"
                                           value="{{ old('color_menu_texto', $config->color_menu_texto) }}" title="Color del texto del menú">
                                    @error('color_menu_texto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label" style="font-size:12px;">Menú seleccionado</label>
                                    <input type="color" name="color_menu_activo" class="form-control form-control-color w-100 @error('color_menu_activo') is-invalid @enderror"
                                           value="{{ old('color_menu_activo', $config->color_menu_activo) }}" title="Color del menú seleccionado">
                                    @error('color_menu_activo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <label class="form-label">Colores de Botones</label>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label" style="font-size:12px;">Texto de botones</label>
                                    <input type="color" name="color_boton_texto" class="form-control form-control-color w-100 @error('color_boton_texto') is-invalid @enderror"
                                           value="{{ old('color_boton_texto', $config->color_boton_texto) }}" title="Color del texto de los botones">
                                    @error('color_boton_texto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label" style="font-size:12px;">Fondo de botones</label>
                                    <input type="color" name="color_boton_fondo" class="form-control form-control-color w-100 @error('color_boton_fondo') is-invalid @enderror"
                                           value="{{ old('color_boton_fondo', $config->color_boton_fondo) }}" title="Color de fondo de los botones">
                                    @error('color_boton_fondo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <label class="form-label">Colores de Gráficos (Dashboard y Reportes)</label>
                            <div class="row g-3 mb-4">
                                <div class="col-4">
                                    <label class="form-label" style="font-size:12px;">Color 1</label>
                                    <input type="color" name="color_grafico_1" class="form-control form-control-color w-100 @error('color_grafico_1') is-invalid @enderror"
                                           value="{{ old('color_grafico_1', $config->color_grafico_1) }}" title="Color 1 de gráficos">
                                    @error('color_grafico_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-4">
                                    <label class="form-label" style="font-size:12px;">Color 2</label>
                                    <input type="color" name="color_grafico_2" class="form-control form-control-color w-100 @error('color_grafico_2') is-invalid @enderror"
                                           value="{{ old('color_grafico_2', $config->color_grafico_2) }}" title="Color 2 de gráficos">
                                    @error('color_grafico_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-4">
                                    <label class="form-label" style="font-size:12px;">Color 3</label>
                                    <input type="color" name="color_grafico_3" class="form-control form-control-color w-100 @error('color_grafico_3') is-invalid @enderror"
                                           value="{{ old('color_grafico_3', $config->color_grafico_3) }}" title="Color 3 de gráficos">
                                    @error('color_grafico_3')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <label class="form-label">Colores de la Pantalla de Login</label>
                            <div class="row g-3 mb-1">
                                <div class="col-4">
                                    <label class="form-label" style="font-size:12px;">Fondo de la página</label>
                                    <input type="color" name="color_login_fondo" class="form-control form-control-color w-100 @error('color_login_fondo') is-invalid @enderror"
                                           value="{{ old('color_login_fondo', $config->color_login_fondo) }}" title="Color de fondo del login">
                                    @error('color_login_fondo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-4">
                                    <label class="form-label" style="font-size:12px;">Tarjeta de módulos</label>
                                    <input type="color" name="color_login_tarjeta" class="form-control form-control-color w-100 @error('color_login_tarjeta') is-invalid @enderror"
                                           value="{{ old('color_login_tarjeta', $config->color_login_tarjeta) }}" title="Color de la tarjeta de módulos del login">
                                    @error('color_login_tarjeta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-4">
                                    <label class="form-label" style="font-size:12px;">Texto de módulos</label>
                                    <input type="color" name="color_login_texto_modulos" class="form-control form-control-color w-100 @error('color_login_texto_modulos') is-invalid @enderror"
                                           value="{{ old('color_login_texto_modulos', $config->color_login_texto_modulos) }}" title="Color del texto de los módulos del login">
                                    @error('color_login_texto_modulos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- ═══ MONEDA & IMPUESTOS ═══ --}}
                        <div class="tab-pane fade" id="tab-moneda" role="tabpanel">
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

                            <div class="mb-1">
                                <label class="form-label">% de Impuesto <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="igv" class="form-control @error('igv') is-invalid @enderror"
                                           value="{{ old('igv', $config->igv) }}" min="0" max="100" step="0.01" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('igv')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- ═══ PARÁMETRO DE CLIENTE ═══ --}}
                        <div class="tab-pane fade" id="tab-parametro-cliente" role="tabpanel">
                            <label class="form-label">Descuento para Clientes Distribuidores</label>
                            <div class="input-group" style="max-width:220px;">
                                <input type="number" name="descuento_distribuidor" class="form-control @error('descuento_distribuidor') is-invalid @enderror"
                                       value="{{ old('descuento_distribuidor', $config->descuento_distribuidor) }}" min="0" max="100" step="0.01" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('descuento_distribuidor')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div style="font-size:12px; color:#9ca3af; margin-top:6px;">
                                Se aplica automáticamente sobre el total de la compra a todo cliente marcado como
                                "Distribuidor" en su ficha (Clientes → Nuevo/Editar Cliente). Por defecto 20%.
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        <i class="fas fa-save me-2"></i>Guardar Configuración
                    </button>
                </form>
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
</div>

@endsection

@push('scripts')
<script>
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
