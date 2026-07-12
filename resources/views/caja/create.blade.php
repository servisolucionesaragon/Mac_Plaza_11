@extends('layouts.app')
@section('title', 'Abrir Caja')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('caja.index') }}" style="color:#a855f7;">Control de Caja</a></li>
    <li class="breadcrumb-item active">Abrir Caja</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Abrir Caja</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Registra el fondo inicial en efectivo para comenzar el día</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                @if($ultimoCierre)
                    <div class="mb-3 p-3 rounded-3" style="background:#f8f5ff;font-size:12.5px;color:#5b21b6;">
                        <i class="fas fa-info-circle me-1"></i>Referencia: el último cierre
                        ({{ $ultimoCierre->fecha->format('d/m/Y') }}) contó
                        <strong>{{ $config->simbolo_moneda }} {{ number_format($ultimoConteoEfectivo ?? 0, 2) }}</strong> en efectivo.
                    </div>
                @endif

                <form action="{{ route('caja.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Fondo Inicial en Efectivo ({{ $config->simbolo_moneda }}) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('monto_inicial') is-invalid @enderror"
                               name="monto_inicial" value="{{ old('monto_inicial', 0) }}" min="0" step="0.01" required>
                        @error('monto_inicial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas de Apertura</label>
                        <textarea class="form-control" name="notas_apertura" rows="3"
                                  placeholder="Observaciones al abrir la caja...">{{ old('notas_apertura') }}</textarea>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('caja.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-door-open me-2"></i>Abrir Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
