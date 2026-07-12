@extends('layouts.app')
@section('title', 'Cerrar Caja')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('caja.index') }}" style="color:#a855f7;">Control de Caja</a></li>
    <li class="breadcrumb-item"><a href="{{ route('caja.show', $caja) }}" style="color:#a855f7;">{{ $caja->fecha->format('d/m/Y') }}</a></li>
    <li class="breadcrumb-item active">Cerrar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Cerrar Caja del {{ $caja->fecha->format('d/m/Y') }}</h5>
                <p class="text-muted mb-4" style="font-size:13px;">
                    Cuenta el dinero físico/registrado en cada medio de pago y regístralo abajo
                </p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('caja.cerrar', $caja) }}" method="POST" id="formCierre">
                    @csrf

                    <div class="table-responsive mb-3">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th class="text-end">Esperado</th>
                                    <th style="width:180px;">Monto Contado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($esperadoPorMetodo as $fila)
                                <tr>
                                    <td style="font-size:13.5px;font-weight:500;">{{ $fila['nombre'] }}</td>
                                    <td class="text-end" style="font-size:13.5px;" id="esperado-{{ $fila['metodo_pago_id'] }}"
                                        data-esperado="{{ $fila['esperado'] }}">
                                        {{ $config->simbolo_moneda }} {{ number_format($fila['esperado'], 2) }}
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm conteo-input"
                                               name="conteos[{{ $fila['metodo_pago_id'] }}]"
                                               value="{{ old('conteos.'.$fila['metodo_pago_id'], $fila['esperado']) }}"
                                               min="0" step="0.01" required
                                               oninput="calcularTotales()">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="border-top:2px solid #e5e7eb;">
                                    <td style="font-size:14px;font-weight:700;">Total</td>
                                    <td class="text-end" style="font-size:14px;font-weight:700;">
                                        {{ $config->simbolo_moneda }} <span id="totalEsperado">{{ number_format($totalEsperado, 2) }}</span>
                                    </td>
                                    <td style="font-size:14px;font-weight:700;">
                                        {{ $config->simbolo_moneda }} <span id="totalContado">0.00</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end" style="font-size:13px;font-weight:600;">Diferencia</td>
                                    <td style="font-size:13px;font-weight:700;" id="totalDiferencia">—</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas de Cierre</label>
                        <textarea class="form-control" name="notas_cierre" rows="3"
                                  placeholder="Observaciones al cerrar la caja...">{{ old('notas_cierre') }}</textarea>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('caja.show', $caja) }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-door-closed me-2"></i>Confirmar Cierre
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

function calcularTotales() {
    let totalEsperado = 0;
    let totalContado = 0;

    document.querySelectorAll('.conteo-input').forEach(function (input) {
        const fila = input.closest('tr');
        const esperadoCell = fila.querySelector('[data-esperado]');
        const esperado = parseFloat(esperadoCell.dataset.esperado) || 0;
        const contado = parseFloat(input.value) || 0;
        totalEsperado += esperado;
        totalContado += contado;
    });

    const diferencia = totalContado - totalEsperado;

    document.getElementById('totalContado').textContent = totalContado.toFixed(2);
    const diferenciaEl = document.getElementById('totalDiferencia');
    diferenciaEl.textContent = (diferencia >= 0 ? '+' : '') + MONEDA + ' ' + diferencia.toFixed(2);
    diferenciaEl.style.color = diferencia < 0 ? '#dc2626' : (diferencia > 0 ? '#16a34a' : '#6b7280');
}

calcularTotales();
</script>
@endpush
