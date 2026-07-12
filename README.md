# CRM Gestión Tienda de Celulares

CRM/POS a medida para una tienda de venta y reparación de celulares: inventario,
punto de venta, clientes, órdenes de servicio técnico, reportes y configuración,
con control de acceso por roles.

- **Producción (demo/portal):** https://app.ssaragon.com/celulares
- **Producción (cliente Mac Plaza 11):** https://app.macplaza11.com
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
| **Clientes** | Registro con DNI/RUC, tipo particular/empresa, historial de compras y reparaciones, búsqueda y filtros. Alerta/filtro/badge de **cumpleaños del mes**. Check **"Cliente Distribuidor"**: aplica automáticamente en Ventas un % de descuento configurable (ver Configuración) sobre el total de sus compras, badge visible en el listado. Botones de WhatsApp en la ficha del cliente: contacto directo, cobro de cartera pendiente (mensaje distinto si el crédito ya está en mora) y felicitación de cumpleaños con oferta de descuento. Indicativo de país fijo `+57` (Colombia). |
| **Inventario (Productos)** | Stock en tiempo real, alertas de stock mínimo, specs técnicas (IMEI/serial, RAM, almacenamiento), condición (nuevo/reacondicionado/usado), márgenes automáticos, exportar el inventario completo a Excel. |
| **Ventas (POS)** | Búsqueda de productos en tiempo real, impuesto configurable, descuentos, métodos de pago editables, numeración automática (`VTA-000001`), filtro por tipo de venta (Contado/Crédito), recibo con toggle Hoja Carta / Tirilla térmica 80mm (con logo en la cabecera de la tirilla) y botón para enviarlo por WhatsApp mediante un enlace público firmado. Buscador de cliente por nombre/documento en tiempo real; si el cliente es **Distribuidor**, un banner avisa y su % de descuento configurado se suma automáticamente al descuento manual sobre el subtotal. **Ventas a crédito:** saldo pendiente, fecha de vencimiento, abono inicial opcional al crear, y registro de abonos parciales después (cada uno con su propio recibo hoja/tirilla, también enviable por WhatsApp) — la venta queda en estado "Pendiente" hasta saldar el 100% del crédito, momento en el que pasa a "Completada" automáticamente. **Edición y cancelación** (solo rol Administrador): editar cliente, productos/cantidades, método de pago, tipo de venta y notas de una venta ya registrada (revierte y reaplica el stock correctamente, recalcula el saldo pendiente si ya tiene abonos); cancelar restaura el stock. |
| **Control de Caja** | Apertura diaria (fondo inicial en efectivo + notas) y cierre (conteo por cada medio de pago activo, comparado contra lo "esperado" — calculado en vivo a partir de ventas de contado + abonos + ingresos − gastos del día — con diferencia resaltada). Solo puede haber una caja abierta a la vez; historial de cajas pasadas con su desglose, filtrable por rango de fechas; el detalle de cada caja también lista los gastos e ingresos individuales del día. **Registrar ventas, abonos, gastos e ingresos requiere una caja abierta ese día** (bloqueado tanto en el guardado como en el acceso al formulario, con aviso visible en Ventas/Gastos/Ingresos cuando no hay caja abierta). Al cerrar, genera un **Reporte de Cierre** (totales de ventas/descuentos/abonos/ingresos/gastos, desglose por método de pago, y el total que debe haber en caja) con toggle Hoja Carta/Tirilla térmica (logo incluido) para imprimir, y descarga en PDF real (`barryvdh/laravel-dompdf`). |
| **Gastos** | Salidas de dinero de caja durante la operación (descripción, monto, medio de pago, notas), con filtro por fecha/método y total del período. Editar/eliminar un gasto ya registrado está restringido al rol Administrador. |
| **Ingresos** | Entradas de dinero a caja que no son ventas (ej. préstamos, aportes de capital) — mismo diseño que Gastos (descripción, monto, medio de pago), sumando en vez de restar en el cálculo de caja. Editar/eliminar restringido a Administrador. |
| **Reparaciones** | Órdenes de servicio con 7 estados (recibido → diagnóstico → esperando repuesto → reparación → listo → entregado), prioridad (baja/media/alta/urgente), asignación de técnico, garantía (con fecha de vencimiento calculada), historial de cambios de estado. Buscador de cliente por nombre/documento en tiempo real al crear una orden (igual que en Ventas). Recibo con toggle Hoja Carta / Tirilla térmica 80mm, enviable por WhatsApp mediante enlace público firmado. Botón de WhatsApp adicional cuando el estado pasa a "Listo para entrega", avisando al cliente. |
| **Catálogos** | Gestión (crear/editar/activar-desactivar/eliminar) de categorías, marcas, condición, almacenamiento, RAM y métodos de pago — antes eran listas fijas hardcodeadas, ahora son catálogos dinámicos y protegidos contra borrado si tienen productos/ventas asociadas. |
| **Reportes** | Filtro por fechas, ventas por día/método de pago, top 10 productos/clientes, reparaciones por estado, estadísticas del sistema (usuarios/clientes/productos/ventas/reparaciones), **Cartera por Cobrar** (ventas a crédito con saldo pendiente, con días de atraso si vencieron, sin depender del filtro de fechas), y **Abonos de Crédito Cobrados** en el período (dinero efectivamente recibido, aunque la venta siga "Pendiente"). |
| **Mi Perfil** | Cualquier usuario autenticado (sin importar rol) puede editar su nombre, correo y teléfono, y cambiar su contraseña (requiere confirmar la contraseña actual). Accesible desde el dropdown de usuario en la esquina superior derecha. |
| **Usuarios** | Módulo independiente (antes vivía dentro de Configuración): gestión de usuarios (crear/editar/activar-desactivar/eliminar, con protección para no auto-eliminarse ni auto-desactivarse) y **Permisos de Roles** — matriz de qué módulos ve Vendedor/Técnico (Administrador siempre tiene acceso completo). |
| **Configuración** | Organizada en **5 pestañas**: **Empresa** (nombre, NIT, teléfono, dirección, ciudad/departamento, correo, web, zona horaria), **Logo**, **Colores** (colores de la plataforma, del menú, de botones, de los 3 gráficos de Dashboard/Reportes, y 3 colores propios de la **pantalla de login** — fondo de la página, tarjeta de módulos, texto de los módulos, independientes de los colores de marca del panel principal), **Moneda & Impuestos** (moneda, símbolo, % de impuesto) y **Parámetro de Cliente** (% de descuento automático para clientes Distribuidores, por defecto 20%). Un solo formulario guarda todo junto sin importar la pestaña activa. |
| **Backup & Restauración** | Exportar/importar la BD completa en SQL, restauración con backup automático previo, 3 niveles de reset. |

## Roles y permisos

| Módulo | Administrador | Vendedor | Técnico |
|---|:---:|:---:|:---:|
| Dashboard | ✅ | ✅ | Solo consulta |
| Clientes | ✅ | ✅ | — |
| Inventario | ✅ | ✅ | Solo consulta |
| Ventas (POS) | ✅ | ✅ | — |
| Control de Caja | ✅ | ✅ | — |
| Gastos | ✅ | ✅ | — |
| Ingresos | ✅ | ✅ | — |
| Reparaciones | ✅ | Solo consulta | ✅ |
| Reportes | ✅ | ✅ | — |
| Usuarios | ✅ | — | — |
| Configuración | ✅ | — | — |
| Backup | ✅ | — | — |

**Nota:** Administrador siempre tiene acceso completo a los 12 módulos (chequeo
`esAdmin()` en `User::puedeAcceder()`, previo a cualquier fila de `permisos_rol`).
Para Vendedor y Técnico, **toda la matriz — incluyendo Usuarios, Configuración y
Backup — es configurable en vivo** desde el propio módulo Usuarios → Permisos de
Roles, sin tocar código: la tabla de arriba refleja la configuración actual, pero
puede cambiar en cualquier momento desde la UI. El sidebar ("Sistema" → Usuarios/
Configuración/Backup) usa el mismo `puedeAcceder()` por ítem que el resto del menú,
ya no hay un gate especial `esAdmin()` para esa sección.

Control de acceso vía middleware `permiso:{modulo}` (`VerificarPermisoModulo`) + tabla
`permisos_rol`, editable desde Usuarios sin tocar código.

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
| `clientes` | Cartera de clientes. `fecha_nacimiento` alimenta la alerta de cumpleaños del mes. `es_distribuidor` activa el descuento automático en Ventas. |
| `categorias`, `marcas`, `condiciones`, `almacenamientos`, `rams`, `metodos_pago` | Catálogos dinámicos |
| `productos` | Inventario |
| `ventas` / `detalle_ventas` | Cabecera y detalle de ventas. `ventas` incluye `es_credito`, `saldo_pendiente`, `fecha_vencimiento` para el crédito. |
| `abonos` | Pagos parciales de una venta a crédito (`venta_id`, `monto`, `fecha_abono`, `metodo_pago_id`, `user_id`, `notas`). |
| `cajas` | Una fila por sesión de caja (día): `fecha`, `monto_inicial`, `estado` (abierta/cerrada), usuario y fecha de apertura/cierre. Solo una fila con `estado='abierta'` a la vez. |
| `caja_conteos` | Conteo físico por método de pago al cerrar una caja (`caja_id`, `metodo_pago_id`, `monto_contado`). El monto **esperado** no se guarda — se calcula en vivo en `CajaController::calcularEsperadoPorMetodo()`. |
| `gastos` | Salidas de dinero de caja (`fecha_gasto`, `descripcion`, `monto`, `metodo_pago_id`, `user_id`, `notas`). |
| `ingresos` | Entradas de dinero a caja no relacionadas a una venta (misma forma que `gastos`, con `fecha_ingreso`). |
| `reparaciones` / `reparacion_historial` | Órdenes de servicio técnico y su historial de estados |
| `configuracion` | Configuración del negocio (fila única tipo singleton), incluye colores de menú/botones/gráficos (`color_menu_texto`, `color_menu_activo`, `color_boton_texto`, `color_boton_fondo`, `color_grafico_1/2/3`) y `descuento_distribuidor` (% aplicado a clientes Distribuidores en Ventas, default 20) |
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
- **Dos despliegues desde el mismo repo:** además de la demo del portal
  (`/celulares`, subcarpeta), este código corre como instancia dedicada para el
  cliente real Mac Plaza 11 en `app.macplaza11.com` (dominio propio, servidor
  Nginx separado, sin subcarpeta), con su propia base de datos (`macplaza_crm`) y
  su propio `APP_KEY` — nunca reutilizar el `.env`/`APP_KEY` de una instancia en
  la otra. **Flujo de despliegue:** `/celulares` (staging) se sigue actualizando
  a mano por SCP; `/var/www/macplaza` (producción del cliente) es un **clon git
  real** — se actualiza con `git pull` una vez que el cambio ya se probó en
  staging y se hizo push a GitHub (nunca por SCP directo a producción). Si una
  migración cambia `composer.json`/`composer.lock`, correr `composer install
  --no-dev --optimize-autoloader` después del `git pull` (producción no trackea
  `vendor/`).

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
- **Eliminar un usuario con historial daba 500:** `ventas.user_id` y `abonos.user_id`
  tienen `onDelete('restrict')` — MySQL bloqueaba el `DELETE` a nivel de base de datos
  si el usuario tenía ventas/abonos registrados, y esa excepción no estaba capturada.
  `ConfiguracionController::destroyUsuario()` ahora verifica
  `ventas()`/`abonos()`/`reparaciones()` antes de borrar y muestra un mensaje sugiriendo
  desactivar la cuenta en su lugar.
- **MySQL puede backfillear mal una columna nueva en filas ya existentes:** al agregar
  columnas con `->default(...)` a la tabla singleton `configuracion` (que ya tenía datos
  reales), la fila existente quedó con valores de OTRAS columnas de color en vez del
  default declarado en la migración — un bug/limitación de "instant DDL" de MySQL 8 al
  apilar varias rondas de columnas nuevas sobre una tabla con filas preexistentes. El
  `DESCRIBE` de la tabla mostraba el default correcto, pero la fila real no lo tenía.
  Se reprodujo en la BD de staging, no en la de producción (clonada más recientemente).
  **Después de cualquier migración que agregue columnas a una tabla con datos reales,
  verificar con un `SELECT` explícito** — no asumir que el backfill del default
  funcionó solo porque la migración no lanzó error. Si sale mal, corregir con un
  `UPDATE` explícito (un `ALTER TABLE ... FORCE` solo lo arregló parcialmente).
- **Descuento de cliente Distribuidor:** se calcula sobre el subtotal antes de
  impuesto (misma base que el descuento manual de la venta) y se **suma** al
  descuento manual que el cajero ingrese — no lo reemplaza. `VentaController::
  descuentoDistribuidor()` es la única fuente de este cálculo (usada tanto en
  `store()` como en `update()`); el resultado se guarda combinado en la columna
  `ventas.descuento` ya existente, sin desglose aparte en el recibo.
- **Scrollbar temático:** el scrollbar global (`layouts/app.blade.php`) usa la
  variable `--sidebar-bg` (el mismo color de "Fondo del sidebar" en
  Configuración > Colores) en vez de un gris fijo, tanto para Chrome/Edge
  (`::-webkit-scrollbar-thumb`) como Firefox (`scrollbar-color`).
- **Control de Caja identifica "Efectivo" por nombre, no por ID:** como
  `metodos_pago` es un catálogo dinámico (gestionado en Catálogos, sin un ID
  fijo garantizado), `CajaController::calcularEsperadoPorMetodo()` suma el
  `monto_inicial` (fondo de caja) a la fila cuyo `nombre` sea literalmente
  "Efectivo" (case-insensitive). **Si se renombra ese método de pago en
  Catálogos, se pierde ese enlace** — como salvaguarda, el fondo inicial
  también forma parte del total general de "esperado" aunque no se sume a
  ninguna fila específica.
- **Solo una caja abierta a la vez:** `Caja::abiertaActual()` es el único
  gate — antes de registrar una venta (`VentaController::store()`), un
  abono (`registrarAbono()`), un gasto o un ingreso, se verifica que exista.
  Si se necesita permitir cajas por turno/usuario en el futuro, este es el
  punto central a modificar (hoy es una única caja compartida por día, a
  petición explícita del usuario).
- **Footer institucional oculto al imprimir (regla global):** el
  `<footer>` de `layouts/app.blade.php` (aviso de derechos reservados) no
  estaba cubierto por ningún `@media print` — aparecía en cualquier
  impresión. Se agregó `@media print { footer { display:none !important; } }`
  directo en el layout principal (no en cada vista de recibo/reporte por
  separado), así que corrige de una vez los recibos de Ventas/Reparaciones y
  el Reporte de Cierre de Caja.
- **PDF del Reporte de Cierre de Caja siempre en Hoja Carta:** a diferencia
  del toggle Hoja/Tirilla que sí existe para la vista en pantalla e impresión
  vía navegador (`caja/reporte.blade.php`), la descarga en PDF
  (`caja/reporte-pdf.blade.php`, vista standalone sin Bootstrap para
  compatibilidad con dompdf) usa `setPaper('letter')` fijo. Calcular el alto
  dinámico de una tirilla térmica requiere medir el DOM en el navegador
  (`getBoundingClientRect()`), y dompdf no ejecuta JavaScript — se optó por
  no replicar ese cálculo en PHP para evitar un layout de tirilla roto en el
  PDF.
