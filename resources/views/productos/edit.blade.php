@extends('layouts.app')
@section('title', 'Editar Producto')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" style="color:#a855f7;">Inventario</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Editar Producto</h5>
                <p class="text-muted mb-4" style="font-size:13px;">{{ $producto->nombre }}</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Código SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                           name="codigo" value="{{ old('codigo', $producto->codigo) }}">
                                    @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                           name="nombre" value="{{ old('nombre', $producto->nombre) }}">
                                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select name="categoria_id" class="form-select" required>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->id }}" {{ old('categoria_id',$producto->categoria_id)==$cat->id?'selected':'' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Marca <span class="text-danger">*</span></label>
                                    <select name="marca_id" class="form-select" required>
                                        @foreach($marcas as $m)
                                            <option value="{{ $m->id }}" {{ old('marca_id',$producto->marca_id)==$m->id?'selected':'' }}>
                                                {{ $m->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Modelo</label>
                                    <input type="text" class="form-control" name="modelo"
                                           value="{{ old('modelo', $producto->modelo) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="color"
                                           value="{{ old('color', $producto->color) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Condición</label>
                                    <select name="condicion_id" class="form-select">
                                        @foreach($condiciones as $c)
                                            <option value="{{ $c->id }}" {{ old('condicion_id',$producto->condicion_id)==$c->id?'selected':'' }}>{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Almacenamiento</label>
                                    <select name="almacenamiento_id" class="form-select">
                                        <option value="">— Sin especificar —</option>
                                        @foreach($almacenamientos as $alm)
                                            <option value="{{ $alm->id }}" {{ old('almacenamiento_id',$producto->almacenamiento_id)==$alm->id?'selected':'' }}>{{ $alm->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">RAM</label>
                                    <select name="ram_id" class="form-select">
                                        <option value="">— Sin especificar —</option>
                                        @foreach($rams as $ram)
                                            <option value="{{ $ram->id }}" {{ old('ram_id',$producto->ram_id)==$ram->id?'selected':'' }}>{{ $ram->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="requiere_imei" id="requiere_imei" value="1"
                                               {{ old('requiere_imei', $producto->requiere_imei) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requiere_imei">Requiere IMEI al venderse</label>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="requiere_serial" id="requiere_serial" value="1"
                                               {{ old('requiere_serial', $producto->requiere_serial) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requiere_serial">Requiere Serial al venderse</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio Compra ({{ $config->simbolo_moneda }})</label>
                                    <input type="number" class="form-control" name="precio_compra"
                                           value="{{ old('precio_compra', $producto->precio_compra) }}"
                                           min="0" step="0.01" oninput="calcularMargen()">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio Venta ({{ $config->simbolo_moneda }})</label>
                                    <input type="number" class="form-control" name="precio_venta"
                                           value="{{ old('precio_venta', $producto->precio_venta) }}"
                                           min="0" step="0.01" oninput="calcularMargen()">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Margen</label>
                                    <div class="form-control" style="background:var(--input-bg);">
                                        <span id="margenValor" style="font-weight:600; color:#10b981;">
                                            {{ number_format($producto->margen, 1) }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stock Actual</label>
                                    <input type="number" class="form-control" name="stock"
                                           value="{{ old('stock', $producto->stock) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stock Mínimo</label>
                                    <input type="number" class="form-control" name="stock_minimo"
                                           value="{{ old('stock_minimo', $producto->stock_minimo) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select name="activo" class="form-select">
                                        <option value="1" {{ $producto->activo?'selected':'' }}>Activo</option>
                                        <option value="0" {{ !$producto->activo?'selected':'' }}>Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label">Imagen del Producto</label>
                            @if($producto->imagen)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/'.$producto->imagen) }}" id="previewImg"
                                         style="width:100%; border-radius:12px; max-height:220px; object-fit:cover;">
                                    <p class="text-muted mt-1" style="font-size:12px;">Imagen actual</p>
                                </div>
                            @else
                                <img id="previewImg" src="" style="display:none; width:100%; border-radius:12px; max-height:220px; object-fit:cover; margin-bottom:8px;">
                            @endif

                            <div onclick="document.getElementById('imagenInput').click()"
                                 style="border:2px dashed #d1d5db; border-radius:12px; padding:20px;
                                        text-align:center; cursor:pointer; background:#fafafa;">
                                <i class="fas fa-camera text-muted mb-2 d-block"></i>
                                <span style="font-size:12px; color:var(--text-muted);">
                                    {{ $producto->imagen ? 'Cambiar imagen' : 'Subir imagen' }}
                                </span>
                            </div>
                            <input type="file" id="imagenInput" name="imagen" accept="image/*"
                                   style="display:none;" onchange="previewImage(this)">
                        </div>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Actualizar Producto
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
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('previewImg');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function calcularMargen() {
    const compra = parseFloat(document.querySelector('[name=precio_compra]').value) || 0;
    const venta  = parseFloat(document.querySelector('[name=precio_venta]').value) || 0;
    const margen = compra > 0 ? ((venta - compra) / compra * 100) : 0;
    document.getElementById('margenValor').textContent = margen.toFixed(1) + '%';
    document.getElementById('margenValor').style.color = margen >= 0 ? '#10b981' : '#dc2626';
}
</script>
@endpush
