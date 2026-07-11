@extends('layouts.app')
@section('title', 'Actualizar Reparación')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.index') }}" style="color:#a855f7;">Reparaciones</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reparaciones.show', $reparacion) }}" style="color:#a855f7;">{{ $reparacion->numero_orden }}</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Actualizar Orden: {{ $reparacion->numero_orden }}</h5>
                        <p class="text-muted mb-0" style="font-size:13px;">
                            {{ $reparacion->dispositivo }} — {{ $reparacion->cliente->nombre_completo ?? '' }}
                        </p>
                    </div>
                    <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eye me-1"></i>Ver Detalle
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reparaciones.update', $reparacion) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row g-4">
                        {{-- Estado y prioridad --}}
                        <div class="col-12">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:var(--text-dark);">
                                <i class="fas fa-tasks me-2" style="color:#a855f7;"></i>Estado de la Orden
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Estado Actual <span class="text-danger">*</span></label>
                                    <select name="estado" class="form-select" required>
                                        @php $estados = ['recibido'=>'📥 Recibido','en_diagnostico'=>'🔍 En Diagnóstico','esperando_repuesto'=>'⏳ Esperando Repuesto','en_reparacion'=>'🔧 En Reparación','listo'=>'✅ Listo para Entregar','entregado'=>'📦 Entregado','no_reparable'=>'❌ No Reparable']; @endphp
                                        @foreach($estados as $val => $lbl)
                                            <option value="{{ $val }}" {{ old('estado',$reparacion->estado)==$val?'selected':'' }}>
                                                {{ $lbl }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prioridad</label>
                                    <select name="prioridad" class="form-select">
                                        <option value="baja" {{ old('prioridad',$reparacion->prioridad)=='baja'?'selected':'' }}>🟢 Baja</option>
                                        <option value="media" {{ old('prioridad',$reparacion->prioridad)=='media'?'selected':'' }}>🟡 Media</option>
                                        <option value="alta" {{ old('prioridad',$reparacion->prioridad)=='alta'?'selected':'' }}>🟠 Alta</option>
                                        <option value="urgente" {{ old('prioridad',$reparacion->prioridad)=='urgente'?'selected':'' }}>🔴 Urgente</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Técnico Asignado</label>
                                    <select name="tecnico_id" class="form-select">
                                        @foreach($tecnicos as $t)
                                            <option value="{{ $t->id }}" {{ old('tecnico_id',$reparacion->tecnico_id)==$t->id?'selected':'' }}>
                                                {{ $t->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nota de este cambio (opcional)</label>
                                    <textarea class="form-control" name="nota_cambio" rows="2"
                                              placeholder="Ej: Se reemplazó la pantalla, pendiente probar batería...">{{ old('nota_cambio') }}</textarea>
                                    <div class="form-text" style="font-size:11px;">Se guarda en el historial de esta orden, junto con el cambio de estado.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Equipo --}}
                        <div class="col-12">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:var(--text-dark);">
                                <i class="fas fa-mobile-alt me-2" style="color:#a855f7;"></i>Equipo
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Dispositivo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="dispositivo"
                                           value="{{ old('dispositivo',$reparacion->dispositivo) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Marca</label>
                                    <input type="text" class="form-control" name="marca"
                                           value="{{ old('marca',$reparacion->marca) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Modelo</label>
                                    <input type="text" class="form-control" name="modelo"
                                           value="{{ old('modelo',$reparacion->modelo) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">IMEI</label>
                                    <input type="text" class="form-control" name="imei"
                                           value="{{ old('imei',$reparacion->imei) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="color"
                                           value="{{ old('color',$reparacion->color) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Estimada</label>
                                    <input type="date" class="form-control" name="fecha_estimada"
                                           value="{{ old('fecha_estimada', optional($reparacion->fecha_estimada)->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Diagnóstico --}}
                        <div class="col-12">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:var(--text-dark);">
                                <i class="fas fa-stethoscope me-2" style="color:#a855f7;"></i>Diagnóstico Técnico
                            </h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Falla Reportada <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="falla_reportada" rows="3" required>{{ old('falla_reportada',$reparacion->falla_reportada) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Diagnóstico del Técnico</label>
                                    <textarea class="form-control" name="diagnostico" rows="4"
                                              placeholder="Describe el diagnóstico técnico del equipo...">{{ old('diagnostico',$reparacion->diagnostico) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Solución Aplicada</label>
                                    <textarea class="form-control" name="solucion" rows="4"
                                              placeholder="Describe qué se hizo para solucionar la falla...">{{ old('solucion',$reparacion->solucion) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Costos y garantía --}}
                        <div class="col-12">
                            <h6 class="fw-600 mb-3" style="font-weight:600; color:var(--text-dark);">
                                <i class="fas fa-dollar-sign me-2" style="color:#a855f7;"></i>Costos y Garantía
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Presupuesto ({{ $config->simbolo_moneda }})</label>
                                    <input type="number" class="form-control" name="presupuesto"
                                           value="{{ old('presupuesto',$reparacion->presupuesto) }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Costo Final ({{ $config->simbolo_moneda }})</label>
                                    <input type="number" class="form-control" name="costo_final"
                                           value="{{ old('costo_final',$reparacion->costo_final) }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Incluye Garantía?</label>
                                    <select name="garantia" class="form-select">
                                        <option value="0" {{ !$reparacion->garantia?'selected':'' }}>No</option>
                                        <option value="1" {{ $reparacion->garantia?'selected':'' }}>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Días de Garantía</label>
                                    <input type="number" class="form-control" name="dias_garantia"
                                           value="{{ old('dias_garantia',$reparacion->dias_garantia) }}" min="0">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notas adicionales</label>
                                    <textarea class="form-control" name="notas" rows="2">{{ old('notas',$reparacion->notas) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
