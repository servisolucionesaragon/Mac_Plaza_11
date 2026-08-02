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
                                    <th style="width:280px;">Monto Contado</th>
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
                                        @if($metodoEfectivoId && $fila['metodo_pago_id'] == $metodoEfectivoId && ($billetes->count() || $monedas->count()))
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" class="form-control conteo-input flex-grow-1"
                                                       id="inputEfectivoContado"
                                                       name="conteos[{{ $fila['metodo_pago_id'] }}]"
                                                       value="{{ old('conteos.'.$fila['metodo_pago_id'], 0) }}"
                                                       min="0" step="0.01" required readonly tabindex="-1"
                                                       style="background:#f3f4f6;cursor:not-allowed;pointer-events:none;font-size:16px;font-weight:700;min-width:130px;"
                                                       oninput="calcularTotales()">
                                                <button type="button" class="btn btn-primary text-nowrap"
                                                        onclick="abrirModalEfectivo()" title="Contar billetes y monedas">
                                                    <i class="fas fa-coins"></i> Contar
                                                </button>
                                            </div>
                                        @else
                                            <input type="number" class="form-control form-control-sm conteo-input"
                                                   name="conteos[{{ $fila['metodo_pago_id'] }}]"
                                                   value="{{ old('conteos.'.$fila['metodo_pago_id'], $fila['esperado']) }}"
                                                   min="0" step="0.01" required
                                                   oninput="calcularTotales()">
                                        @endif
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

@if($metodoEfectivoId && ($billetes->count() || $monedas->count()))
<!-- ══════════ MODAL: Contar Efectivo ══════════ -->
<div class="modal fade" id="modalEfectivo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-coins me-2" style="color:#a855f7;"></i>Contar Efectivo por Denominación
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                @foreach(['Billetes' => $billetes, 'Monedas' => $monedas] as $titulo => $items)
                    @if($items->count())
                    <h6 class="fw-bold mb-2" style="font-size:13px;">{{ $titulo }}</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:76px;"></th>
                                    <th>Valor</th>
                                    <th style="width:110px;">Cantidad</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $d)
                                <tr>
                                    <td>
                                        @if($d->imagen)
                                            <img src="{{ asset('storage/' . $d->imagen) }}" style="width:60px;height:60px;object-fit:contain;border-radius:6px;background:#f9fafb;">
                                        @else
                                            <div style="width:60px;height:60px;border-radius:6px;background:#f9fafb;"></div>
                                        @endif
                                    </td>
                                    <td style="font-size:13.5px;font-weight:500;">{{ $config->simbolo_moneda }} {{ number_format($d->valor, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm denominacion-input"
                                               form="formCierre"
                                               name="denominaciones[{{ $d->id }}]"
                                               data-valor="{{ $d->valor }}"
                                               value="{{ old('denominaciones.'.$d->id, 0) }}"
                                               min="0" step="1"
                                               oninput="calcularTotalEfectivo()">
                                    </td>
                                    <td class="text-end subtotal-denominacion" style="font-size:13px;">
                                        {{ $config->simbolo_moneda }} 0
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                <div style="font-size:14px;font-weight:700;">
                    Total Efectivo: {{ $config->simbolo_moneda }} <span id="totalModalEfectivo">0</span>
                </div>
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" onclick="calcularTotales()">
                    <i class="fas fa-check me-2"></i>Aplicar
                </button>
            </div>
        </div>
    </div>
</div>
@endif
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

function abrirModalEfectivo() {
    const modalEl = document.getElementById('modalEfectivo');
    if (!modalEl) return;
    new bootstrap.Modal(modalEl).show();
}

function formatoMiles(n) {
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function calcularTotalEfectivo() {
    const inputsDenominacion = document.querySelectorAll('.denominacion-input');
    if (inputsDenominacion.length === 0) return;

    let total = 0;

    inputsDenominacion.forEach(function (input) {
        const valor = parseFloat(input.dataset.valor) || 0;
        const cantidad = parseInt(input.value, 10) || 0;
        const subtotal = valor * cantidad;
        total += subtotal;

        const subtotalCell = input.closest('tr').querySelector('.subtotal-denominacion');
        if (subtotalCell) {
            subtotalCell.textContent = MONEDA + ' ' + formatoMiles(subtotal);
        }
    });

    const totalModalEl = document.getElementById('totalModalEfectivo');
    if (totalModalEl) totalModalEl.textContent = formatoMiles(total);

    const inputEfectivo = document.getElementById('inputEfectivoContado');
    if (inputEfectivo) {
        inputEfectivo.value = total.toFixed(2);
    }

    calcularTotales();
}

calcularTotales();
calcularTotalEfectivo();
</script>
@endpush
