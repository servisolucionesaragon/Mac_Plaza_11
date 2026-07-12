<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CatalogoTipoController;
use App\Http\Controllers\CatalogoValorController;
use App\Http\Controllers\PerfilController;

// ── Autenticación ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',       [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Recibos públicos (link firmado, sin login — para compartir por WhatsApp) ───
Route::middleware('signed')->group(function () {
    Route::get('/r/venta/{venta}', [VentaController::class, 'reciboPublico'])->name('publico.venta.recibo');
    Route::get('/r/reparacion/{reparacion}', [ReparacionController::class, 'reciboPublico'])
        ->name('publico.reparacion.recibo');
    Route::get('/r/abono/{venta}/{abono}', [VentaController::class, 'abonoReciboPublico'])
        ->name('publico.abono.recibo');
});

// ── Rutas protegidas (requieren autenticación) ────────────────────────────────
Route::middleware(['auth', 'nocache'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permiso:dashboard');

    // Clientes
    Route::resource('clientes', ClienteController::class)->middleware('permiso:clientes');

    // Productos
    Route::get('/productos/exportar/excel', [ProductoController::class, 'exportarExcel'])
        ->name('productos.exportar')->middleware('permiso:productos');
    Route::resource('productos', ProductoController::class)->middleware('permiso:productos');

    // Catálogos (categorías, marcas, condición, almacenamiento, ram)
    Route::prefix('catalogos')->name('catalogos.')->middleware('permiso:catalogos')->group(function () {
        Route::get('/', [CatalogoController::class, 'index'])->name('index');

        // ── Tipos de catálogo dinámicos (deben ir antes de las rutas wildcard {tipo} de abajo) ──
        Route::post('/tipos', [CatalogoTipoController::class, 'store'])->name('tipos.store');
        Route::put('/tipos/{catalogoTipo}', [CatalogoTipoController::class, 'update'])->name('tipos.update');
        Route::patch('/tipos/{catalogoTipo}/toggle', [CatalogoTipoController::class, 'toggle'])->name('tipos.toggle');
        Route::delete('/tipos/{catalogoTipo}', [CatalogoTipoController::class, 'destroy'])->name('tipos.destroy');

        // ── Valores de catálogos dinámicos ──
        Route::post('/tipos/{catalogoTipo}/valores', [CatalogoValorController::class, 'store'])->name('valores.store');
        Route::put('/valores/{valor}', [CatalogoValorController::class, 'update'])->name('valores.update');
        Route::patch('/valores/{valor}/toggle', [CatalogoValorController::class, 'toggle'])->name('valores.toggle');
        Route::delete('/valores/{valor}', [CatalogoValorController::class, 'destroy'])->name('valores.destroy');

        // ── Catálogos fijos (categorías, marcas, condición, almacenamiento, ram, métodos de pago) ──
        Route::post('/{tipo}', [CatalogoController::class, 'store'])->name('store');
        Route::put('/{tipo}/{id}', [CatalogoController::class, 'update'])->name('update');
        Route::patch('/{tipo}/{id}/toggle', [CatalogoController::class, 'toggle'])->name('toggle');
        Route::delete('/{tipo}/{id}', [CatalogoController::class, 'destroy'])->name('destroy');
    });

    // Ventas
    Route::resource('ventas', VentaController::class)->except(['edit', 'update', 'destroy'])->middleware('permiso:ventas');
    Route::get('/ventas/{venta}/edit', [VentaController::class, 'edit'])->name('ventas.edit')->middleware('permiso:ventas');
    Route::put('/ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update')->middleware('permiso:ventas');
    Route::patch('/ventas/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar')->middleware('permiso:ventas');
    Route::get('/ventas/{venta}/recibo', [VentaController::class, 'recibo'])
        ->name('ventas.recibo')->middleware('permiso:ventas');
    Route::post('/ventas/{venta}/abonos', [VentaController::class, 'registrarAbono'])
        ->name('ventas.abonos.store')->middleware('permiso:ventas');
    Route::get('/ventas/{venta}/abonos/{abono}/recibo', [VentaController::class, 'reciboAbono'])
        ->name('ventas.abonos.recibo')->middleware('permiso:ventas');

    // Reparaciones
    Route::resource('reparaciones', ReparacionController::class)->except(['destroy'])
        ->parameters(['reparaciones' => 'reparacion'])
        ->middleware('permiso:reparaciones');
    Route::get('/reparaciones/{reparacion}/recibo', [ReparacionController::class, 'recibo'])
        ->name('reparaciones.recibo')->middleware('permiso:reparaciones');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index')->middleware('permiso:reportes');

    // Mi Perfil (disponible para cualquier usuario autenticado, sin permiso de módulo)
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.updatePassword');

    // Configuración
    Route::middleware('permiso:configuracion')->group(function () {
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('/configuracion/general', [ConfiguracionController::class, 'updateGeneral'])->name('configuracion.updateGeneral');
    });

    // Usuarios (gestión de usuarios + permisos por rol)
    Route::middleware('permiso:usuarios')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::put('/usuarios/permisos', [UsuarioController::class, 'updatePermisos'])->name('usuarios.updatePermisos');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    // Backup & Restauración
    Route::middleware('permiso:backup')->group(function () {
        Route::get('/backup',                       [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup/crear',                [BackupController::class, 'crear'])->name('backup.crear');
        Route::get('/backup/descargar/{nombre}',    [BackupController::class, 'descargar'])->name('backup.descargar');
        Route::delete('/backup/eliminar/{nombre}',  [BackupController::class, 'eliminar'])->name('backup.eliminar');
        Route::post('/backup/restaurar',            [BackupController::class, 'restaurar'])->name('backup.restaurar');
        Route::post('/backup/resetear',             [BackupController::class, 'resetear'])->name('backup.resetear');
    });

    // API interna para búsqueda de productos (para el formulario de ventas)
    Route::get('/api/productos/buscar', function () {
        $productos = \App\Models\Producto::with(['marca'])
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->when(request('q'), fn($q, $buscar) =>
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('codigo', 'like', "%$buscar%")
            )
            ->limit(10)
            ->get(['id', 'nombre', 'codigo', 'precio_venta', 'stock', 'marca_id']);

        return response()->json($productos);
    })->name('api.productos.buscar')->middleware('permiso:ventas');

    // API interna para datos del dashboard (AJAX)
    Route::get('/api/dashboard/ventas-semana', function () {
        // Retorna datos de ventas por día para el gráfico
        $datos = \App\Models\Venta::select(
                \Illuminate\Support\Facades\DB::raw('DATE(fecha_venta) as fecha'),
                \Illuminate\Support\Facades\DB::raw('SUM(total) as total')
            )
            ->where('estado', 'completada')
            ->where('fecha_venta', '>=', \Carbon\Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return response()->json($datos);
    })->name('api.dashboard.ventas')->middleware('permiso:dashboard');

});
