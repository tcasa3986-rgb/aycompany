# ERP-Farmacia

Sistema de Gestión Farmacéutica construido con **Laravel 11 + Blade + Tailwind CSS** y **MySQL 8**, siguiendo el plan de trabajo (`plan.md`) y el diseño base (`diseño.png`).

## Módulos implementados

| # | Módulo | Estado |
|---|---|---|
| 1 | Autenticación + roles + permisos por rol (Spatie) | ✅ |
| 2 | Inventario / Productos (CRUD, stock bajo, requiere receta) | ✅ |
| 3 | Categorías (CRUD) | ✅ |
| 4 | Proveedores (CRUD) | ✅ |
| 5 | Lotes por producto (CRUD anidado, vencimientos) | ✅ |
| 6 | Clientes / Pacientes (alergias, enfermedades, fidelidad) | ✅ |
| 7 | POS / Punto de venta (búsqueda live, carrito, IGV, descuento) | ✅ |
| 8 | Caja registradora (apertura, cierre, cuadre, ingresos/egresos) | ✅ |
| 9 | Compras + recepción de mercadería (suma stock + crea lotes) | ✅ |
| 10 | Recetas médicas (controlados, retenidas, historial por paciente) | ✅ |
| 11 | Reportes con exportación PDF (DomPDF) y Excel (Maatwebsite) | ✅ |
| 12 | Dashboard con KPIs, gráficos Chart.js y calendario | ✅ |

## Roles y permisos sembrados

| Rol | Acceso principal |
|---|---|
| Administrador | Todos los módulos |
| Farmacéutico | Inventario, lotes, clientes, recetas, POS, caja |
| Cajero | POS, caja, clientes (lectura) |
| Almacenero | Inventario, categorías, proveedores, lotes, compras |
| Contador | Reportes, exportación, cierre de caja |

## Requisitos

- PHP 8.2+ con extensiones: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `bcmath`, `fileinfo`, `gd` (para PDFs)
- Composer 2.x
- MySQL 8 (XAMPP / Laragon / MySQL nativo)
- Node.js 18+ con npm (o usar el modo CDN de Tailwind)

## Instalación

### 1. Crear la base de datos

```sql
CREATE DATABASE farmacia_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Si tu `root` tiene contraseña, edita `.env` y completa `DB_PASSWORD=`.

### 2. Instalar dependencias PHP

```bash
composer install
```

Esto instala Laravel, Spatie Permission, **DomPDF** (PDFs) y **Maatwebsite Excel**.

### 3. Generar APP_KEY

```bash
php artisan key:generate
```

### 4. Migrar y sembrar datos

```bash
php artisan migrate --seed
```

### 5. Compilar assets

**Con Node:**
```bash
npm install
npm run dev      # desarrollo (watch)
npm run build    # producción
```

**Sin Node (CDN Tailwind):** en `resources/views/layouts/app.blade.php` y `resources/views/auth/login.blade.php` reemplaza la línea `@vite([...])` por:

```html
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: {
  farmacia: {50:'#ecfdf6',100:'#d1fae9',200:'#a7f0d4',300:'#6fe0bb',400:'#3fc99e',500:'#22b388',600:'#199172',700:'#16735c',800:'#155b4b',900:'#114a3e'},
  topbar:'#2a8f88', sidebar:'#46b8a4'
} } } }
</script>
```

### 6. Levantar el servidor

```bash
php artisan serve
```

Abre <http://localhost:8000>.

## Usuarios demo

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | admin@farmacia.test | password |
| Cajero | cajero@farmacia.test | password |
| Farmacéutico | farmaceutico@farmacia.test | password |

## Flujo de uso típico

1. Ingresa como **admin** → te lleva al Dashboard.
2. Como cajero, ve a **Caja → Abrir caja** (necesario para poder cobrar).
3. Ve al **POS**, busca productos, agrega al carrito y cobra. La venta queda ligada a la caja y descuenta stock.
4. Como almacenero, registra una **Compra** y luego presiona "Recibir mercadería" → suma stock y crea lote con vencimiento.
5. Como farmacéutico, registra una **Receta** y consulta historial.
6. Al cerrar el día, ve a **Reportes** y descarga PDF/Excel de ventas, top productos, stock crítico o por vencer.

## Estructura del proyecto

```
erp-farmacia/
├── app/
│   ├── Exports/ReporteExport.php           ← exportación Excel reusable
│   ├── Http/Controllers/                   ← 13 controllers
│   │   ├── Auth/LoginController.php
│   │   ├── CajaController.php              (apertura, cierre, movimientos)
│   │   ├── CategoriaController.php
│   │   ├── ClienteController.php
│   │   ├── CompraController.php            (orden + recepción + anular)
│   │   ├── DashboardController.php
│   │   ├── LoteController.php              (lotes por producto)
│   │   ├── PosController.php               (POS + ventas + caja + fidelidad)
│   │   ├── ProductoController.php
│   │   ├── ProveedorController.php
│   │   ├── RecetaController.php
│   │   ├── ReporteController.php           (4 reportes + PDF + Excel)
│   │   └── VentaController.php
│   └── Models/                             ← 14 modelos Eloquent
├── database/
│   ├── migrations/                         ← 14 migraciones
│   └── seeders/                            ← 7 seeders (incluye permisos)
├── resources/views/
│   ├── auth/login.blade.php
│   ├── cajas/                              (index, show)
│   ├── categorias/, clientes/, compras/, lotes/, pos/, productos/,
│   │   proveedores/, recetas/, ventas/    ← CRUDs / pantallas
│   ├── dashboard/index.blade.php           (KPIs + Chart.js + calendario)
│   ├── reportes/
│   │   ├── index, ventas, top, stock, vencer  (HTML)
│   │   ├── _filtros.blade.php                 (partial reusable)
│   │   └── pdf/ventas, top, stock, vencer     (plantillas DomPDF)
│   ├── components/icon.blade.php
│   └── layouts/app + partials/sidebar + topbar
├── routes/web.php                          (todas las rutas con middleware permission)
├── composer.json                           (Laravel 11, Spatie, DomPDF, Excel)
├── package.json, vite.config.js, tailwind.config.js
└── .env / .env.example
```

## Pendientes del plan original (no implementados)

Estos módulos del `plan.md` quedan disponibles para futuras iteraciones:

- Multi-Sucursal (módulo 10)
- Configuración del sistema (parámetros, impresora térmica, alertas email)
- Facturación electrónica (Greenter SUNAT)
- Devoluciones y anulaciones de venta con motivo
- Crédito a clientes / cuentas por cobrar y por pagar
- Códigos de barras / QR por producto
- Auditoría de acciones (logs por usuario)
- Autenticación 2FA opcional
- Backup automático de BD
- Notificaciones email automáticas (stock bajo, vencimientos)
- Alerta de interacciones medicamentosas
- Clasificación ATC para medicamentos
- Programa de fidelidad: el campo `puntos_fidelidad` ya se acumula (1 pt por cada S/ 10), falta UI de canje

## Resolución de problemas

- **`SQLSTATE[HY000] [2002]`** → MySQL no está corriendo. Inicia el servicio (XAMPP/Laragon).
- **`could not find driver`** → Falta extensión `pdo_mysql` en `php.ini`.
- **`Vite manifest not found`** → Ejecuta `npm install && npm run dev` o usa modo CDN.
- **`Class "Barryvdh\DomPDF\Facade\Pdf" not found`** → Ejecuta `composer install`.
- **`Action unauthorized`** → El usuario no tiene el permiso requerido. Inicia con `admin@farmacia.test`.
- **El POS no deja cobrar** → Necesitas abrir caja primero en el módulo Caja.
- **Pantalla 500** → Revisa `storage/logs/laravel.log` y los permisos de escritura sobre `storage/` y `bootstrap/cache/`.
