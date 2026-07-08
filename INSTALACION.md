# Instalación del CRM — Tienda Celulares

## Requisitos

- PHP 8.1+
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js (opcional, para compilar assets)

---

## Pasos de instalación

### 1. Instalar dependencias PHP

```bash
composer install
```

### 2. Configurar el entorno

El archivo `.env` ya está creado y configurado con:
- Base de datos: `tiendacelulares_crm`
- Usuario: `root`
- Puerto: `3306`

Si tu MySQL tiene contraseña, edita el `.env`:
```
DB_PASSWORD=tu_contraseña
```

### 3. Generar la clave de aplicación

```bash
php artisan key:generate
```

### 4. Crear la base de datos en MySQL

```sql
CREATE DATABASE tiendacelulares_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Ejecutar migraciones y datos de prueba

```bash
php artisan migrate --seed
```

### 6. Crear el enlace simbólico para imágenes

```bash
php artisan storage:link
```

### 7. Iniciar el servidor de desarrollo

```bash
php artisan serve
```

Accede en: **http://localhost:8000**

---

## Credenciales de acceso (datos de prueba)

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | admin@tienda.com | password |
| Vendedor | vendedor@tienda.com | password |
| Técnico | tecnico@tienda.com | password |

---

## Módulos del sistema

| Módulo | URL | Descripción |
|--------|-----|-------------|
| Dashboard | `/dashboard` | Panel principal con KPIs y gráficas |
| Clientes | `/clientes` | Gestión de clientes |
| Inventario | `/productos` | Stock de productos y celulares |
| Ventas | `/ventas` | Registro y control de ventas |
| Reparaciones | `/reparaciones` | Servicio técnico y seguimiento |

---

## Estructura de la base de datos

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios del sistema (admin, vendedor, técnico) |
| `clientes` | Cartera de clientes |
| `categorias` | Categorías de productos |
| `marcas` | Marcas de dispositivos |
| `productos` | Inventario de productos |
| `ventas` | Cabecera de ventas |
| `detalle_ventas` | Detalle de productos por venta |
| `reparaciones` | Órdenes de servicio técnico |
| `configuracion` | Configuración de la tienda |

---

## Funcionalidades implementadas

### Clientes
- Registro completo (nombre, DNI, email, teléfono, tipo particular/empresa)
- Historial de compras y reparaciones
- Búsqueda y filtros avanzados

### Inventario
- Control de stock con alertas de stock bajo
- Gestión por categoría y marca
- Registro de IMEI, color, almacenamiento, RAM
- Condición del producto: nuevo / reacondicionado / usado

### Ventas
- Punto de venta con búsqueda de productos en tiempo real
- Cálculo automático de subtotal, IGV (18%) y total
- Métodos de pago: efectivo, tarjeta, transferencia, cuotas
- Numeración automática (VTA-000001)
- Cancelación de ventas con restauración de stock

### Reparaciones
- Gestión de órdenes de servicio técnico
- Estados: recibido → diagnóstico → esperando repuesto → reparación → listo → entregado
- Prioridades: baja / media / alta / urgente
- Asignación de técnico
- Control de garantía

### Dashboard
- Ventas del día y del mes
- Gráfica de ventas de los últimos 7 días
- Top 5 productos más vendidos
- Alertas de stock bajo
- Últimas ventas y reparaciones
