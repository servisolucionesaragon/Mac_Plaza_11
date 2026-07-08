@extends('layouts.app')
@section('title', 'Nuevo Producto')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" style="color:#a855f7;">Inventario</a></li>
    <li class="breadcrumb-item active">Nuevo Producto</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Registrar Nuevo Producto</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Completa los datos del producto</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        {{-- Columna izquierda --}}
                        <div class="col-lg-8">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:#1e1b4b;">Información General</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Código SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                           name="codigo" value="{{ old('codigo') }}" placeholder="SAM-A54-128">
                                    @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                           name="nombre" value="{{ old('nombre') }}" placeholder="Samsung Galaxy A54 128GB">
                                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                                        <option value="">— Seleccionar —</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->id }}" {{ old('categoria_id')==$cat->id?'selected':'' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Marca <span class="text-danger">*</span></label>
                                    <select name="marca_id" class="form-select @error('marca_id') is-invalid @enderror" required>
                                        <option value="">— Seleccionar —</option>
                                        @foreach($marcas as $m)
                                            <option value="{{ $m->id }}" {{ old('marca_id')==$m->id?'selected':'' }}>
                                                {{ $m->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('marca_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Modelo</label>
                                    <input type="text" class="form-control" name="modelo"
                                           value="{{ old('modelo') }}" placeholder="A54, iPhone 15...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="color"
                                           value="{{ old('color') }}" placeholder="Negro, Blanco...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Condición <span class="text-danger">*</span></label>
                                    <select name="condicion_id" class="form-select @error('condicion_id') is-invalid @enderror" required>
                                        @foreach($condiciones as $c)
                                            <option value="{{ $c->id }}" {{ old('condicion_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('condicion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Almacenamiento</label>
                                    <select name="almacenamiento_id" class="form-select @error('almacenamiento_id') is-invalid @enderror">
                                        <option value="">— Sin especificar —</option>
                                        @foreach($almacenamientos as $alm)
                                            <option value="{{ $alm->id }}" {{ old('almacenamiento_id')==$alm->id?'selected':'' }}>{{ $alm->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('almacenamiento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">RAM</label>
                                    <select name="ram_id" class="form-select @error('ram_id') is-invalid @enderror">
                                        <option value="">— Sin especificar —</option>
                                        @foreach($rams as $ram)
                                            <option value="{{ $ram->id }}" {{ old('ram_id')==$ram->id?'selected':'' }}>{{ $ram->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('ram_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="requiere_imei" id="requiere_imei" value="1"
                                               {{ old('requiere_imei') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requiere_imei">Requiere IMEI al venderse</label>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="requiere_serial" id="requiere_serial" value="1"
                                               {{ old('requiere_serial') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requiere_serial">Requiere Serial al venderse</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="3"
                                              placeholder="Características, detalles del producto...">{{ old('descripcion') }}</textarea>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:#1e1b4b;">Precios y Stock</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Precio de Compra ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('precio_compra') is-invalid @enderror"
                                           name="precio_compra" value="{{ old('precio_compra',0) }}"
                                           min="0" step="0.01" oninput="calcularMargen()">
                                    @error('precio_compra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio de Venta ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('precio_venta') is-invalid @enderror"
                                           name="precio_venta" value="{{ old('precio_venta',0) }}"
                                           min="0" step="0.01" oninput="calcularMargen()">
                                    @error('precio_venta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Margen de Ganancia</label>
                                    <div class="form-control d-flex align-items-center" style="background:#f9fafb;">
                                        <span id="margenValor" style="font-weight:600; color:#10b981;">0.0%</span>
                                        <span id="margenMonto" class="ms-2 text-muted" style="font-size:12px;"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stock Inicial <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                           name="stock" value="{{ old('stock',0) }}" min="0">
                                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stock Mínimo <span class="text-danger">*</span>
                                        <i class="fas fa-info-circle text-muted fa-xs" title="Alerta cuando baje de este número"></i>
                                    </label>
                                    <input type="number" class="form-control" name="stock_minimo"
                                           value="{{ old('stock_minimo',5) }}" min="0">
                                </div>
                            </div>
                        </div>

                        {{-- Columna derecha - imagen --}}
                        <div class="col-lg-4">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:#1e1b4b;">Imagen del Producto</h6>
                            <div id="dropZone" onclick="document.getElementById('imagenInput').click()"
                                 style="border:2px dashed #d1d5db; border-radius:16px; padding:32px 20px;
                                        text-align:center; cursor:pointer; background:#fafafa; transition:.2s;"
                                 ondragover="event.preventDefault(); this.style.borderColor='#a855f7';"
                                 ondragleave="this.style.borderColor='#d1d5db';"
                                 ondrop="handleDrop(event)">
                                <div id="dropContent">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color:#d1d5db;"></i>
                                    <p class="mb-1" style="font-size:13px; color:#6b7280;">Arrastra la imagen aquí</p>
                                    <p class="mb-0" style="font-size:12px; color:#9ca3af;">o haz clic para seleccionar</p>
                                    <p class="mb-0 mt-2" style="font-size:11px; color:#d1d5db;">JPG, PNG, WebP · Máx 2MB</p>
                                </div>
                                <img id="previewImg" src="" style="display:none; width:100%; border-radius:10px; max-height:200px; object-fit:cover;">
                            </div>
                            <input type="file" id="imagenInput" name="imagen" accept="image/*"
                                   style="display:none;" onchange="previewImage(this)">

                            @error('imagen')
                                <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror

                            {{-- Resumen precio --}}
                            <div class="mt-4 p-3 rounded-3" style="background:#f8f5ff;">
                                <h6 style="font-size:13px; font-weight:600; margin-bottom:12px;">Resumen de Precio</h6>
                                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                                    <span class="text-muted">Precio compra</span>
                                    <span id="resCompra">{{ $config->simbolo_moneda }} 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                                    <span class="text-muted">Precio venta</span>
                                    <span id="resVenta" style="font-weight:600;">{{ $config->simbolo_moneda }} 0.00</span>
                                </div>
                                <hr style="margin:8px 0;">
                                <div class="d-flex justify-content-between" style="font-size:13px;">
                                    <span class="text-muted">Ganancia unitaria</span>
                                    <span id="resGanancia" style="color:#10b981; font-weight:600;">{{ $config->simbolo_moneda }} 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Guardar Producto
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
const MONEDA = "{{ $config->simbolo_moneda }}";
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('previewImg').style.display = 'block';
            document.getElementById('dropContent').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function handleDrop(e) {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('imagenInput').files = dt.files;
        previewImage(document.getElementById('imagenInput'));
    }
}

function calcularMargen() {
    const compra = parseFloat(document.querySelector('[name=precio_compra]').value) || 0;
    const venta  = parseFloat(document.querySelector('[name=precio_venta]').value) || 0;
    const margen = compra > 0 ? ((venta - compra) / compra * 100) : 0;
    const ganancia = venta - compra;

    document.getElementById('margenValor').textContent = margen.toFixed(1) + '%';
    document.getElementById('margenValor').style.color = margen >= 0 ? '#10b981' : '#dc2626';
    document.getElementById('margenMonto').textContent = '(' + MONEDA + ' ' + ganancia.toFixed(2) + ')';
    document.getElementById('resCompra').textContent  = MONEDA + ' ' + compra.toFixed(2);
    document.getElementById('resVenta').textContent   = MONEDA + ' ' + venta.toFixed(2);
    document.getElementById('resGanancia').textContent = MONEDA + ' ' + ganancia.toFixed(2);
    document.getElementById('resGanancia').style.color = ganancia >= 0 ? '#10b981' : '#dc2626';
}
</script>
@endpush
