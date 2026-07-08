@extends('layouts.app')
@section('title', 'Backup & Restauración')

@section('breadcrumb')
    <li class="breadcrumb-item active">Backup & Restauración</li>
@endsection

@push('styles')
<style>
    /* ── Módulo Backup ─────────────────────────────────────── */
    .backup-hero {
        background: linear-gradient(135deg, #1a0a3e 0%, #2d1068 50%, #1a0a3e 100%);
        border-radius: 20px;
        padding: 32px 36px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
    }
    .backup-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(168,85,247,.35), transparent 70%);
        border-radius: 50%;
    }
    .backup-hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: 30%;
        width: 160px; height: 160px;
        background: radial-gradient(circle, rgba(236,72,153,.2), transparent 70%);
        border-radius: 50%;
    }
    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 40px;
        padding: 8px 16px;
        font-size: 13px;
        color: #fff;
    }
    .stat-pill .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
    }

    /* ── Acción cards ─────────────────────────────────────── */
    .action-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,.07);
        transition: transform .2s, box-shadow .2s;
        height: 100%;
    }
    .action-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.1); }

    .card-icon-circle {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    /* ── Upload drop zone ─────────────────────────────────── */
    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 14px;
        padding: 32px 24px;
        text-align: center;
        cursor: pointer;
        transition: all .3s;
        background: #fafbff;
    }
    .drop-zone:hover, .drop-zone.dragover {
        border-color: #a855f7;
        background: #fdf4ff;
    }
    .drop-zone input[type=file] { display: none; }
    .drop-zone .dz-icon { font-size: 40px; color: #d1d5db; margin-bottom: 12px; transition: color .3s; }
    .drop-zone:hover .dz-icon { color: #a855f7; }

    /* ── Backup list ──────────────────────────────────────── */
    .backup-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #f3f4f6;
        margin-bottom: 8px;
        transition: all .2s;
        background: #fff;
    }
    .backup-item:hover { border-color: #e9d5ff; background: #fdf4ff; }
    .backup-item .bi-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: #ede9fe;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        color: #7c3aed;
        font-size: 16px;
    }

    /* ── Danger Zone ──────────────────────────────────────── */
    .danger-zone {
        background: linear-gradient(135deg, #fff5f5, #fff);
        border: 2px solid #fecaca;
        border-radius: 20px;
        padding: 28px 32px;
        margin-top: 28px;
    }
    .danger-title {
        color: #dc2626;
        font-weight: 700;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }

    .reset-option {
        border: 2px solid #f3f4f6;
        border-radius: 14px;
        padding: 16px 20px;
        cursor: pointer;
        transition: all .2s;
        background: #fff;
    }
    .reset-option:hover { border-color: #fca5a5; background: #fff5f5; }
    .reset-option.selected { border-color: #ef4444; background: #fff5f5; box-shadow: 0 0 0 3px rgba(239,68,68,.1); }
    .reset-option input[type=radio] { display: none; }

    .reset-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .confirm-box {
        background: #fff;
        border: 2px solid #fecaca;
        border-radius: 12px;
        padding: 16px 20px;
    }

    .btn-danger-outline {
        border: 2px solid #ef4444;
        color: #ef4444;
        background: transparent;
        border-radius: 10px;
        padding: 10px 28px;
        font-weight: 600;
        transition: all .2s;
    }
    .btn-danger-outline:hover:not(:disabled) {
        background: #ef4444;
        color: #fff;
    }
    .btn-danger-outline:disabled { opacity: .4; cursor: not-allowed; }
</style>
@endpush

@section('content')

{{-- ── Alertas ── --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" style="border-radius:12px;border:none;background:#d1fae5;color:#065f46;">
    <i class="fas fa-check-circle fa-lg"></i>
    <div>{!! session('success') !!}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" style="border-radius:12px;border:none;">
    <i class="fas fa-exclamation-circle fa-lg"></i>
    <div>{!! session('error') !!}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════ HERO ══════════ --}}
<div class="backup-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <div style="width:52px;height:52px;background:rgba(255,255,255,.15);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;">
                    🛡️
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="color:#fff;">Backup & Restauración</h4>
                    <p style="color:rgba(255,255,255,.65);font-size:13px;margin:0;">Protege los datos de tu tienda y gestiona copias de seguridad</p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="stat-pill"><span class="dot" style="background:#10b981;"></span>{{ $stats['ventas'] }} ventas</span>
                <span class="stat-pill"><span class="dot" style="background:#06b6d4;"></span>{{ $stats['clientes'] }} clientes</span>
                <span class="stat-pill"><span class="dot" style="background:#a855f7;"></span>{{ $stats['productos'] }} productos</span>
                <span class="stat-pill"><span class="dot" style="background:#f59e0b;"></span>{{ $stats['reparaciones'] }} reparaciones</span>
            </div>
        </div>
        <div class="text-end" style="color:rgba(255,255,255,.8);">
            <div style="font-size:12px;margin-bottom:6px;">ÚLTIMO BACKUP</div>
            @if($stats['ultimo'])
                <div style="font-size:18px;font-weight:700;">{{ $stats['ultimo']->format('d/m/Y') }}</div>
                <div style="font-size:12px;opacity:.7;">{{ $stats['ultimo']->format('H:i:s') }}</div>
            @else
                <div style="font-size:15px;font-weight:600;color:#fca5a5;">Sin backups aún</div>
            @endif
            <div style="margin-top:8px;font-size:12px;">{{ $stats['backups'] }} archivos · {{ $stats['tamTotal'] > 0 ? number_format($stats['tamTotal']/1024,1).' KB' : '0 KB' }} total</div>
        </div>
    </div>
</div>

{{-- ══════════ FILA SUPERIOR: Crear + Lista + Restaurar ══════════ --}}
<div class="row g-4">

    {{-- ── Crear Backup ── --}}
    <div class="col-lg-4">
        <div class="card action-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="card-icon-circle" style="background:#ede9fe;">
                        <i class="fas fa-cloud-download-alt" style="color:#7c3aed;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Crear Backup</h6>
                        <small class="text-muted">Exportar base de datos completa</small>
                    </div>
                </div>

                <div class="rounded-3 p-3 mb-4" style="background:#f8f5ff;border:1px solid #e9d5ff;font-size:12px;color:#6b7280;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-info-circle" style="color:#a855f7;"></i>
                        <strong style="color:#374151;">¿Qué incluye?</strong>
                    </div>
                    <ul class="mb-0 ps-4" style="line-height:1.9;">
                        <li>Estructura completa de tablas</li>
                        <li>Todos los registros del sistema</li>
                        <li>Configuración y usuarios</li>
                        <li>Formato SQL compatible MySQL</li>
                    </ul>
                </div>

                <form action="{{ route('backup.crear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius:12px;font-size:14px;">
                        <i class="fas fa-save me-2"></i>Generar Backup Ahora
                    </button>
                </form>

                <div class="mt-3 text-center" style="font-size:11px;color:#9ca3af;">
                    <i class="fas fa-lock me-1"></i>El archivo se guarda en el servidor de forma segura
                </div>
            </div>
        </div>
    </div>

    {{-- ── Historial de Backups ── --}}
    <div class="col-lg-4">
        <div class="card action-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="card-icon-circle" style="background:#e0f2fe;">
                            <i class="fas fa-history" style="color:#0369a1;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Historial</h6>
                            <small class="text-muted">{{ $stats['backups'] }} archivos guardados</small>
                        </div>
                    </div>
                </div>

                <div style="max-height:300px;overflow-y:auto;padding-right:4px;">
                    @forelse($backups as $bk)
                    <div class="backup-item">
                        <div class="bi-icon">
                            <i class="fas fa-file-code"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0;">
                            <div style="font-size:12px;font-weight:600;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $bk['nombre'] }}">
                                {{ Str::limit($bk['nombre'], 28) }}
                            </div>
                            <div style="font-size:11px;color:#9ca3af;">
                                {{ $bk['fecha']->format('d/m/Y H:i') }} ·
                                @php
                                    $b = $bk['tamanio'];
                                    echo $b >= 1048576 ? round($b/1048576,1).' MB' : round($b/1024,1).' KB';
                                @endphp
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('backup.descargar', $bk['nombre']) }}"
                               class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:4px 8px;" title="Descargar">
                                <i class="fas fa-download" style="font-size:11px;"></i>
                            </a>
                            <form action="{{ route('backup.eliminar', $bk['nombre']) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este backup?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 8px;" title="Eliminar">
                                    <i class="fas fa-trash" style="font-size:11px;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color:#9ca3af;">
                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-40"></i>
                        <div style="font-size:13px;">No hay backups guardados</div>
                        <div style="font-size:12px;">Crea tu primer backup ahora</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ── Restaurar ── --}}
    <div class="col-lg-4">
        <div class="card action-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="card-icon-circle" style="background:#dcfce7;">
                        <i class="fas fa-upload" style="color:#059669;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Restaurar</h6>
                        <small class="text-muted">Importar desde archivo .sql</small>
                    </div>
                </div>

                <div class="alert d-flex gap-2 mb-3" style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:10px 14px;font-size:12px;color:#92400e;">
                    <i class="fas fa-exclamation-triangle mt-1 flex-shrink-0" style="color:#d97706;"></i>
                    <div>Antes de restaurar se crea un <strong>backup automático</strong> de los datos actuales.</div>
                </div>

                <form action="{{ route('backup.restaurar') }}" method="POST" enctype="multipart/form-data" id="formRestore">
                    @csrf

                    <div class="drop-zone mb-3" id="dropZone" onclick="document.getElementById('archivoSql').click()">
                        <input type="file" name="archivo_sql" id="archivoSql" accept=".sql,.txt">
                        <div class="dz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div id="dzText" style="font-size:13px;font-weight:600;color:#374151;">
                            Arrastra tu archivo .sql aquí
                        </div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">o haz clic para seleccionar · Máx. 100 MB</div>
                    </div>

                    @error('archivo_sql')
                        <div class="text-danger" style="font-size:12px;margin-top:-8px;margin-bottom:10px;">{{ $message }}</div>
                    @enderror

                    <button type="button" id="btnRestaurar"
                            class="btn w-100 py-2 disabled"
                            style="background:#059669;color:#fff;border-radius:12px;font-size:14px;border:none;opacity:.5;"
                            onclick="confirmarRestore()">
                        <i class="fas fa-sync-alt me-2"></i>Restaurar Base de Datos
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════ DANGER ZONE ══════════ --}}
<div class="danger-zone">
    <div class="danger-title">
        <div style="width:38px;height:38px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-exclamation-triangle" style="color:#dc2626;font-size:16px;"></i>
        </div>
        Zona de Riesgo — Resetear Sistema
    </div>
    <p class="text-muted mb-4" style="font-size:13px;margin-left:48px;">
        Estas acciones eliminan datos de forma permanente. Se crea un backup automático antes de ejecutar cualquier reset.
    </p>

    <form action="{{ route('backup.resetear') }}" method="POST" id="formReset">
        @csrf

        {{-- Opciones de reset --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="reset-option d-block" id="opt_ventas" onclick="selectReset('ventas', this)">
                    <input type="radio" name="tipo_reset" value="ventas">
                    <div class="d-flex align-items-start gap-3">
                        <div style="width:40px;height:40px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">
                            🧹
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:13.5px;color:#374151;margin-bottom:4px;">
                                Reset Ventas
                                <span class="reset-badge ms-1" style="background:#fef3c7;color:#92400e;">Moderado</span>
                            </div>
                            <div style="font-size:12px;color:#6b7280;line-height:1.5;">
                                Elimina ventas y reparaciones. Conserva clientes, productos y usuarios.
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            <div class="col-md-4">
                <label class="reset-option d-block" id="opt_datos" onclick="selectReset('datos', this)">
                    <input type="radio" name="tipo_reset" value="datos">
                    <div class="d-flex align-items-start gap-3">
                        <div style="width:40px;height:40px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">
                            🗂️
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:13.5px;color:#374151;margin-bottom:4px;">
                                Reset Datos
                                <span class="reset-badge ms-1" style="background:#fee2e2;color:#991b1b;">Alto</span>
                            </div>
                            <div style="font-size:12px;color:#6b7280;line-height:1.5;">
                                Elimina ventas, reparaciones, clientes y productos. Conserva usuarios.
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            <div class="col-md-4">
                <label class="reset-option d-block" id="opt_total" onclick="selectReset('total', this)">
                    <input type="radio" name="tipo_reset" value="total">
                    <div class="d-flex align-items-start gap-3">
                        <div style="width:40px;height:40px;background:#fecaca;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">
                            🏭
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:13.5px;color:#374151;margin-bottom:4px;">
                                Reset Total
                                <span class="reset-badge ms-1" style="background:#fecaca;color:#7f1d1d;">Crítico</span>
                            </div>
                            <div style="font-size:12px;color:#6b7280;line-height:1.5;">
                                Borra todo el sistema. Solo conserva el usuario administrador.
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        @error('tipo_reset')
            <div class="text-danger mb-3" style="font-size:12px;">{{ $message }}</div>
        @enderror

        {{-- Confirmación --}}
        <div class="confirm-box" id="confirmBox" style="display:none;">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <label style="font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                        <i class="fas fa-keyboard me-1" style="color:#ef4444;"></i>
                        Escribe <strong style="color:#ef4444;">RESETEAR</strong> para confirmar
                    </label>
                    <input type="text" name="confirmacion" id="inputConfirm" class="form-control"
                           placeholder="Escribe RESETEAR aquí..."
                           style="border-color:#fecaca;border-radius:10px;"
                           oninput="validarConfirmacion(this.value)">
                    @error('confirmacion')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" id="btnReset" class="btn-danger-outline w-100" disabled>
                        <i class="fas fa-fire me-2"></i>Ejecutar Reset
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- ══════════ MODAL: Confirmar Restore ══════════ --}}
<div class="modal fade" id="modalConfirmRestore" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-body p-5 text-center">
                <div style="width:64px;height:64px;background:#fff3cd;border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">
                    ⚠️
                </div>
                <h5 class="fw-bold mb-2">¿Restaurar la base de datos?</h5>
                <p class="text-muted mb-4" style="font-size:13px;">
                    Esta acción <strong>reemplazará todos los datos actuales</strong> con el contenido del archivo.<br>
                    Se creará un <strong>backup automático</strong> antes de proceder.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning px-4 fw-600" style="border-radius:10px;"
                            onclick="document.getElementById('formRestore').submit()">
                        <i class="fas fa-sync-alt me-2"></i>Sí, restaurar ahora
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Drop zone ──────────────────────────────────────────── */
const dropZone   = document.getElementById('dropZone');
const inputFile  = document.getElementById('archivoSql');
const dzText     = document.getElementById('dzText');
const btnRestore = document.getElementById('btnRestaurar');

inputFile.addEventListener('change', () => updateDropZone(inputFile.files[0]));

dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        inputFile.files = dt.files;
        updateDropZone(file);
    }
});

function updateDropZone(file) {
    if (!file) return;
    const size = file.size >= 1048576
        ? (file.size / 1048576).toFixed(1) + ' MB'
        : (file.size / 1024).toFixed(1) + ' KB';
    dzText.innerHTML = `<span style="color:#7c3aed;">📄 ${file.name}</span><br><small style="color:#059669;font-size:11px;">${size} — listo para restaurar</small>`;
    btnRestore.style.opacity = '1';
    btnRestore.classList.remove('disabled');
    dropZone.style.borderColor = '#a855f7';
}

function confirmarRestore() {
    if (inputFile.files.length === 0) return;
    new bootstrap.Modal(document.getElementById('modalConfirmRestore')).show();
}

/* ── Reset opciones ─────────────────────────────────────── */
let resetSeleccionado = null;

function selectReset(tipo, el) {
    ['opt_ventas','opt_datos','opt_total'].forEach(id => {
        document.getElementById(id).classList.remove('selected');
    });
    el.classList.add('selected');
    el.querySelector('input[type=radio]').checked = true;
    resetSeleccionado = tipo;
    document.getElementById('confirmBox').style.display = 'block';
    document.getElementById('inputConfirm').value = '';
    document.getElementById('btnReset').disabled = true;
}

function validarConfirmacion(val) {
    document.getElementById('btnReset').disabled = val !== 'RESETEAR';
}

/* ── Form reset: prevenir doble submit ──────────────────── */
document.getElementById('formReset').addEventListener('submit', function(e) {
    const btn = document.getElementById('btnReset');
    if (btn.disabled) { e.preventDefault(); return; }
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ejecutando...';
});
</script>
@endpush
