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
- [Notas de despliegue](#notas-de-despliegue)
- [Problemas conocidos y decisiones de diseño](#problemas-conocidos-y-decisiones-de-diseño)

---

## Módulos

| Módulo | Descripción |
|---|---|
| **Dashboard** | KPIs en tiempo real (ventas del día/mes, clientes nuevos, stock bajo, reparaciones pendientes), gráfico de ventas de los últimos 7 días (Chart.js), top productos vendidos. |
| **Clientes** | Registro con DNI/RUC, tipo particular/empresa, historial de compras y reparaciones, búsqueda y filtros. |
| **Inventario (Productos)** | Stock en tiempo real, alertas de stock mínimo, specs técnicas (IMEI/serial, RAM, almacenamiento), condición (nuevo/reacondicionado/usado), márgenes automáticos. |
| **Ventas (POS)** | Búsqueda de productos en tiempo real, impuesto configurable, descuentos, métodos de pago editables, numeración automática (`VTA-000001`), cancelación con restauración de stock, recibo para hoja y tirilla. |
| **Reparaciones** | Órdenes de servicio con 7 estados (recibido → diagnóstico → esperando repuesto → reparación → listo → entregado), prioridad (baja/media/alta/urgente), asignación de técnico, garantía (con fecha de vencimiento calculada), historial de cambios de estado. Recibo en una sola hoja: datos completos de la tienda (NIT, teléfono, dirección, correo/web), datos del equipo, y diagnóstico separado en 3 secciones (falla reportada por el cliente / diagnóstico técnico / solución aplicada). Se abre en la misma pantalla (no en pestaña nueva) desde un único botón "Recibo". |
| **Catálogos** | Gestión (crear/editar/activar-desactivar/eliminar) de categorías, marcas, condición, almacenamiento, RAM y métodos de pago — antes eran listas fijas hardcodeadas, ahora son catálogos dinámicos y protegidos contra borrado si tienen productos/ventas asociadas. |
| **Reportes** | Filtro por fechas, ventas por día/método de pago, top 10 productos/clientes, reparaciones por estado. |
| **Mi Perfil** | Cualquier usuario autenticado (sin importar rol) puede editar su nombre, correo y teléfono, y cambiar su contraseña (requiere confirmar la contraseña actual). Accesible desde el dropdown de usuario en la esquina superior derecha. |
| **Configuración** | Datos del negocio (nombre, NIT, teléfono, dirección, ciudad/departamento, correo, web), logo, zona horaria, moneda, % de impuesto configurable, gestión de usuarios y permisos por rol. |
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
| `clientes` | Cartera de clientes |
| `categorias`, `marcas`, `condiciones`, `almacenamientos`, `rams`, `metodos_pago` | Catálogos dinámicos |
| `productos` | Inventario |
| `ventas` / `detalle_ventas` | Cabecera y detalle de ventas |
| `reparaciones` / `reparacion_historial` | Órdenes de servicio técnico y su historial de estados |
| `configuracion` | Configuración del negocio (fila única tipo singleton) |
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
- **Nombres en español y pluralización automática:** ver nota en
  [Estructura del proyecto](#estructura-del-proyecto) — mismo cuidado aplica a
  cualquier tabla/ruta/recurso nuevo con nombre en español.
