# CRM Gestión Tienda de Celulares

CRM/POS a medida para una tienda de venta y reparación de celulares: inventario,
punto de venta, clientes, órdenes de servicio técnico, reportes y configuración,
con control de acceso por roles.

- **Producción:** https://app.ssaragon.com/celulares
- **Stack:** Laravel 10 (PHP 8.1+) · Blade · Bootstrap 5.3 · Chart.js 4.x · Font Awesome 6.4 · MySQL/MariaDB

---

## Índice

- [Módulos](#módulos)
- [Roles y permisos](#roles-y-permisos)
- [Requisitos](#requisitos)
- [Instalación local](#instalación-local)
- [Variables de entorno](#variables-de-entorno)
- [Base de datos](#base-de-datos)
- [Estructura del proyecto](#estructura-del-proyecto)
- [WhatsApp e integraciones](#whatsapp-e-integraciones)
- [Notas de despliegue](#notas-de-despliegue)
- [Problemas conocidos y decisiones de diseño](#problemas-conocidos-y-decisiones-de-diseño)

---

## Módulos

| Módulo | Descripción |
|---|---|
| **Dashboard** | KPIs en tiempo real (ventas del día/mes, clientes nuevos, stock bajo, reparaciones pendientes, cartera pendiente por cobrar), gráfico de ventas de los últimos 7 días (Chart.js), top productos vendidos. Colores de los gráficos y de las tarjetas de ranking configurables desde Configuración. Campana de alertas en el topbar: stock bajo, créditos vencidos, créditos por vencer en ≤3 días y clientes de cumpleaños en el mes. |
| **Clientes** | Registro con DNI/RUC, tipo particular/empresa, historial de compras y reparaciones, búsqueda y filtros. Alerta/filtro/badge de **cumpleaños del mes**. Botones de WhatsApp en la ficha del cliente: contacto directo, cobro de cartera pendiente (mensaje distinto si el crédito ya está en mora) y felicitación de cumpleaños con oferta de descuento. Indicativo de país fijo `+57` (Colombia). |
| **Inventario (Productos)** | Stock en tiempo real, alertas de stock mínimo, specs técnicas (IMEI/serial, RAM, almacenamiento), condición (nuevo/reacondicionado/usado), márgenes automáticos, exportar el inventario completo a Excel. |
| **Ventas (POS)** | Búsqueda de productos en tiempo real, impuesto configurable, descuentos, métodos de pago editables, numeración automática (`VTA-000001`), filtro por tipo de venta (Contado/Crédito), recibo con toggle Hoja Carta / Tirilla térmica 80mm (con logo en la cabecera de la tirilla) y botón para enviarlo por WhatsApp mediante un enlace público firmado. **Ventas a crédito:** saldo pendiente, fecha de vencimiento, abono inicial opcional al crear, y registro de abonos parciales después (cada uno con su propio recibo hoja/tirilla, también enviable por WhatsApp) — la venta queda en estado "Pendiente" hasta saldar el 100% del crédito, momento en el que pasa a "Completada" automáticamente. **Edición y cancelación** (solo rol Administrador): editar cliente, productos/cantidades, método de pago, tipo de venta y notas de una venta ya registrada (revierte y reaplica el stock correctamente, recalcula el saldo pendiente si ya tiene abonos); cancelar restaura el stock. |
| **Reparaciones** | Órdenes de servicio con 7 estados (recibido → diagnóstico → esperando repuesto → reparación → listo → entregado), prioridad (baja/media/alta/urgente), asignación de técnico, garantía (con fecha de vencimiento calculada), historial de cambios de estado. Recibo con toggle Hoja Carta / Tirilla térmica 80mm, enviable por WhatsApp mediante enlace público firmado. Botón de WhatsApp adicional cuando el estado pasa a "Listo para entrega", avisando al cliente. |
| **Catálogos** | Gestión (crear/editar/activar-desactivar/eliminar) de categorías, marcas, condición, almacenamiento, RAM y métodos de pago — antes eran listas fijas hardcodeadas, ahora son catálogos dinámicos y protegidos contra borrado si tienen productos/ventas asociadas. |
| **Reportes** | Filtro por fechas, ventas por día/método de pago, top 10 productos/clientes, reparaciones por estado, estadísticas del sistema (usuarios/clientes/productos/ventas/reparaciones), **Cartera por Cobrar** (ventas a crédito con saldo pendiente, con días de atraso si vencieron, sin depender del filtro de fechas), y **Abonos de Crédito Cobrados** en el período (dinero efectivamente recibido, aunque la venta siga "Pendiente"). |
| **Mi Perfil** | Cualquier usuario autenticado (sin importar rol) puede editar su nombre, correo y teléfono, y cambiar su contraseña (requiere confirmar la contraseña actual). Accesible desde el dropdown de usuario en la esquina superior derecha. |
| **Configuración** | Datos del negocio (nombre, NIT, teléfono, dirección, ciudad/departamento, correo, web), logo, zona horaria, moneda, % de impuesto configurable, gestión de usuarios y permisos por rol. **Colores configurables:** texto del menú, color sólido del ítem de menú seleccionado, texto/fondo de los botones, y 3 colores para los gráficos de Dashboard/Reportes. |
| **Backup & Restauración** | Exportar/importar la BD completa en SQL, restauración con backup automático previo, 3 niveles de reset. |

## Roles y permisos

| Módulo | Administrador | Vendedor | Técnico |
|---|:---:|:---:|:---:|
| Dashboard | ✅ | ✅ | Solo consulta |
| Clientes | ✅ | ✅ | — |
| Inventario | ✅ | ✅ | Solo consulta |
| Ventas (POS) | ✅ | ✅ | — |
| Reparaciones | ✅ | Solo consulta | ✅ |
| Reportes | ✅ | — | — |
| Configuración | ✅ | — | — |
| Backup | ✅ | — | — |

Control de acceso vía middleware `permiso:{modulo}` (`VerificarPermisoModulo`) + tabla
`permisos_rol`, editable desde Configuración sin tocar código.

Dentro de Ventas, **editar** y **cancelar** una venta ya registrada están restringidos
al rol Administrador incluso si Vendedor tiene permiso general sobre el módulo (chequeo
`Auth::user()->esAdmin()` tanto en la vista como dentro del controlador).

"Mi Perfil" no aparece en la tabla porque no está sujeto a permisos por módulo — lo
puede usar cualquier usuario autenticado (Administrador, Vendedor o Técnico) para
editar sus propios datos.

## Requisitos

- PHP 8.1+
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js (opcional, solo si se van a compilar assets)

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Crear la base de datos:

```sql
CREATE DATABASE tiendacelulares_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Migrar y cargar datos de prueba, enlazar storage y levantar el servidor:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Accede en **http://localhost:8000**.

### Credenciales de prueba (seed)

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | admin@tienda.com | password |
| Vendedor | vendedor@tienda.com | password |
| Técnico | tecnico@tienda.com | password |

⚠️ Son credenciales de fábrica pensadas solo para desarrollo local — **cámbialas desde
Configuración > Usuarios antes de exponer cualquier instancia a producción.**

## Variables de entorno

Ver `.env.example`. Las claves relevantes:

| Variable | Uso |
|---|---|
| `APP_URL` / `ASSET_URL` | Si se despliega en subcarpeta (ej. `/celulares`), ambas deben incluir el subpath para que los assets generen URLs absolutas correctas. |
| `DB_*` | Conexión MySQL/MariaDB. |
| `SESSION_LIFETIME` | Minutos de sesión activa. |

No commitear nunca un `.env` con credenciales reales — está excluido vía `.gitignore`.

## Base de datos

| Tabla | Descripción |
|---|---|
| `users` | Usuarios del sistema (admin, vendedor, técnico) |
| `clientes` | Cartera de clientes. `fecha_nacimiento` alimenta la alerta de cumpleaños del mes. |
| `categorias`, `marcas`, `condiciones`, `almacenamientos`, `rams`, `metodos_pago` | Catálogos dinámicos |
| `productos` | Inventario |
| `ventas` / `detalle_ventas` | Cabecera y detalle de ventas. `ventas` incluye `es_credito`, `saldo_pendiente`, `fecha_vencimiento` para el crédito. |
| `abonos` | Pagos parciales de una venta a crédito (`venta_id`, `monto`, `fecha_abono`, `metodo_pago_id`, `user_id`, `notas`). |
| `reparaciones` / `reparacion_historial` | Órdenes de servicio técnico y su historial de estados |
| `configuracion` | Configuración del negocio (fila única tipo singleton), incluye colores de menú/botones/gráficos (`color_menu_texto`, `color_menu_activo`, `color_boton_texto`, `color_boton_fondo`, `color_grafico_1/2/3`) |
| `permisos_rol` | Permisos por rol y módulo |

## Estructura del proyecto

Laravel estándar (MVC + Blade), sin frontend separado:

```
app/Http/Controllers/   Controladores por módulo
app/Models/              Modelos Eloquent
app/Http/Middleware/     VerificarPermisoModulo, PreventBackHistoryCache, etc.
config/colombia.php      Dataset de departamentos/ciudades de Colombia (formulario Clientes)
database/migrations/     Historial de migraciones
resources/views/         Vistas Blade por módulo
routes/web.php           Rutas de la aplicación
```

**Nota:** en rutas y modelos con nombres en español, Laravel/Eloquent pluraliza mal
(ej. `reparaciones` → wildcard `reparacione`, `Condicion` → tabla `condicions`). Donde
aplica, se usa `->parameters([...])` en las rutas o `protected $table` explícito en el
modelo para evitarlo — revisar ese patrón antes de agregar recursos nuevos en español.

## WhatsApp e integraciones

La app arma enlaces de WhatsApp Click-to-Chat (`https://wa.me/...`) mediante el trait
compartido `App\Traits\TieneWhatsapp` (usado por `Cliente` y `Configuracion`), que
limpia el número (fuerza indicativo `57` de Colombia) y codifica el mensaje con
`rawurlencode()`. Puntos donde se usa:

| Origen | Mensaje |
|---|---|
| Ficha de cliente | Contacto directo, sin mensaje predefinido. |
| Ficha de cliente (con saldo pendiente) | Gestión de cobro — texto distinto si el crédito está en mora (`Cliente::estaAtrasada()`). |
| Ficha de cliente (cumpleaños del mes) | Felicitación con oferta de descuento (`Cliente::cumpleAnioEsteMes()` / scope `conCumpleanioEsteMes`). Sin emojis a propósito: WhatsApp Web no los renderiza bien al abrir el enlace desde navegador (verificado con la codificación UTF-8 correcta a nivel de bytes; es una limitación de esa plataforma, no de la app). |
| Recibo de venta / abono / reparación | Botón "Enviar por WhatsApp" con el enlace **público firmado** del recibo (ver abajo). |
| Reparación en estado "Listo" | Aviso al cliente de que puede recoger el equipo. |
| Login | "¿Olvidaste tu contraseña?" abre WhatsApp al número configurado en Configuración del negocio. |

Todos los mensajes incluyen el nombre de la tienda en **negrita** (`*Nombre*`, sintaxis
de WhatsApp).

### Recibos públicos con enlace firmado

Un cliente sin cuenta en el sistema no puede abrir una ruta autenticada. Por eso los 3
recibos (venta, abono, reparación) tienen una ruta pública separada protegida por el
middleware `signed` de Laravel (`URL::signedRoute()`, sin expiración — el recibo debe
poder verse indefinidamente):

```php
Route::middleware('signed')->group(function () {
    Route::get('/r/venta/{venta}', [VentaController::class, 'reciboPublico'])->name('publico.venta.recibo');
    Route::get('/r/reparacion/{reparacion}', [ReparacionController::class, 'reciboPublico'])->name('publico.reparacion.recibo');
    Route::get('/r/abono/{venta}/{abono}', [VentaController::class, 'abonoReciboPublico'])->name('publico.abono.recibo');
});
```

Cada controlador pasa `$layout = 'layouts.publico'` (layout mínimo, sin sidebar/topbar,
que no depende de `Auth::user()`) y `$publico = true` a la misma vista Blade que usa la
versión autenticada (`@extends($layout ?? 'layouts.app')`), evitando duplicar HTML. Los
botones "Volver"/"Enviar por WhatsApp" solo se muestran cuando `!($publico ?? false)`.

## Notas de despliegue

- Desplegado en un VPS compartido junto a otras apps del portal SSA, en subcarpeta
  (`/celulares`) detrás de Nginx + PHP-FPM.
- `Storage::url()` no respeta subcarpeta + `ASSET_URL` en este tipo de despliegue —
  usar `asset('storage/' . $path)` para URLs de archivos subidos (logo, fotos de
  producto), no `Storage::url()`.
- Backups de base de datos y código de referencia (no necesariamente la última
  versión) se conservan fuera del repositorio, en el NAS interno del equipo.

## Problemas conocidos y decisiones de diseño

- **Impresión de vistas y grillas Bootstrap:** el ancho útil de una hoja tamaño carta
  al imprimir (con `@page { margin: 10mm; }`) es ~741px — **por debajo** del breakpoint
  `md` de Bootstrap (768px). Cualquier `col-md-*`/`offset-md-*` usado en una vista
  pensada para imprimirse (ej. el recibo de reparaciones) se apila en una sola columna
  al imprimir aunque en pantalla se vea en varias, alargando el documento a más de una
  hoja. En vistas de impresión, usar `col-*`/`offset-*` sin breakpoint.
- **Mensajes flash (`session('success')`/`session('error')`):** ya se renderizan una
  sola vez de forma global en `resources/views/layouts/app.blade.php` (escapados con
  `{{ }}` a propósito, porque algunos controladores meten texto escrito por el propio
  usuario — nombre de un catálogo, de un valor — directo en el mensaje sin sanitizar).
  **No agregar un bloque `@if(session('success'))...@endif` propio en una vista
  nueva** — ya salió duplicado en 4 vistas por este motivo. Tampoco meter HTML
  (`<strong>`, etc.) dentro de un mensaje flash, porque se escapa y se ve el tag
  literal.
- **Restauración de backups (`BackupController::restaurar()`):** el archivo `.sql` se
  parte en sentencias individuales por `;` porque Laravel/PDO no ejecuta múltiples
  statements en una sola llamada por defecto. El parser quita las líneas de comentario
  (`-- ...`) que preceden a cada sentencia real antes de decidir si ejecutarla —
  necesario porque el `DROP TABLE IF EXISTS` de cada tabla queda pegado (sin `;` de por
  medio) al bloque de comentario que la antecede en el backup generado por
  `generarSQL()`.
- **Ventas a crédito y el campo `estado`:** una venta a crédito con saldo pendiente se
  crea con `estado = 'pendiente'` (no `'completada'`), y solo pasa a `'completada'`
  automáticamente cuando `VentaController::registrarAbono()` deja el
  `saldo_pendiente` en 0. **Esto es intencional**, pero implica que cualquier reporte/
  KPI que filtre por `where('estado', 'completada')` (Ventas de Hoy/Mes, Ventas por
  Día/Método de Pago, Top Productos/Clientes en `ReporteController`/
  `DashboardController`) **no cuenta una venta a crédito como ingreso hasta que se
  paga por completo**. El dinero que sí se cobra por abonos parciales se refleja aparte
  en la tarjeta "Abonos de Crédito Cobrados" de Reportes (`Abono::whereBetween(...)`),
  no dentro de "Total Ventas". Tenerlo en cuenta antes de agregar un reporte nuevo que
  necesite el ingreso total real de un período.
- **`@page { size: <longitud> auto; }` no funciona en Chrome:** el toggle Hoja
  Carta/Tirilla de los recibos cambia dinámicamente la regla `@page` de un
  `<style id="printPageStyle">`. Usar `size: 80mm auto;` para que el alto se ajuste
  solo al contenido de la tirilla **no funciona** — Chrome descarta silenciosamente
  cualquier `@page size` que mezcle una longitud con la palabra clave `auto` (verificado
  inspeccionando el CSSOM: la propiedad `size` queda vacía), y el navegador cae al
  tamaño de página por defecto (Carta). El fix real es medir en runtime el alto del
  contenido de la tirilla (`getBoundingClientRect().height`, px → mm dividiendo por
  `3.7795275591`) e inyectar `size: 80mm {altoMm}mm;` con dos longitudes concretas,
  recalculado también en el evento `beforeprint`. Aplica a los 3 recibos (venta, abono,
  reparación) y es un cuidado a tener en cuenta en cualquier otra vista de impresión con
  ancho fijo/alto variable.
