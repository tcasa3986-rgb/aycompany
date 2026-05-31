# AutoTaller ERP — Sistema de Gestión para Talleres Automotrices

ERP web completo para talleres automotrices construido sobre **ASP.NET Core 8 + Blazor Server + Entity Framework Core + SQL Server 2022 + MudBlazor**.

---

## Arquitectura

Solución de 4 capas siguiendo Clean Architecture:

```
ERP.TallerAutomotriz.sln
└── src/
    ├── ERP.TallerAutomotriz.Domain          (entidades + enums + reglas de dominio)
    ├── ERP.TallerAutomotriz.Application     (DTOs, interfaces, servicios de aplicación)
    ├── ERP.TallerAutomotriz.Infrastructure  (EF Core, DbContext, Identity, repositorios)
    └── ERP.TallerAutomotriz.Web             (Blazor Server + MudBlazor)
```

## Módulos cubiertos por el modelo de datos

1. **Dashboard ejecutivo** — KPIs, tendencias semanal/mensual, alertas accionables.
2. **Recepción de vehículos y OT** — checkin, fotos, checklist, flujo de estados, historial.
3. **Diagnóstico, mecánica y servicios** — catálogo, paquetes, control de calidad.
4. **Inventario** — repuestos, almacenes, movimientos, kardex, lotes/series, alertas de stock.
5. **CRM** — clientes (persona/empresa), parque vehicular, historial.
6. **Agenda y citas** — calendario, conversión a OT.
7. **Proveedores y compras** — OC, recepción de mercadería, cuentas por pagar.
8. **Facturación, caja y cobranzas** — facturas/boletas/notas, pagos, caja, cuentas por cobrar.
9. **Personal y técnicos** — fichas, asistencia, comisiones.
10. **Reportes y KPIs** — base preparada en `IDashboardService`.
11. **Seguridad y configuración** — Identity con 6 roles, log de auditoría, parámetros, plantillas de notificación.

## Requisitos previos

- **.NET SDK 8.0** o superior
- **SQL Server 2022** (instancia `VITODEV\SERVERDEV` configurada en `appsettings.json`)
- **Visual Studio 2022** (17.8+) o **VS Code** con extensión C#
- Acceso a internet para restaurar paquetes NuGet (MudBlazor, EF Core, Identity)

## Configuración inicial

La cadena de conexión ya está configurada en `src/ERP.TallerAutomotriz.Web/appsettings.json`:

```json
"DefaultConnection": "Server=VITODEV\\SERVERDEV;Database=TallerAutomotrizERP;Trusted_Connection=True;TrustServerCertificate=True;MultipleActiveResultSets=true"
```

## Pasos para ejecutar (primera vez)

Abrir terminal en la raíz del proyecto y ejecutar:

```powershell
# 1. Restaurar paquetes
dotnet restore

# 2. Compilar
dotnet build

# 3. Instalar herramienta EF Core (si no está instalada)
dotnet tool install --global dotnet-ef

# 4. Crear migración inicial (genera el script con todas las tablas)
dotnet ef migrations add InitialCreate -p src/ERP.TallerAutomotriz.Infrastructure -s src/ERP.TallerAutomotriz.Web -o Persistence/Migrations

# 5. Aplicar la migración a SQL Server (crea la BD y todas las tablas)
dotnet ef database update -p src/ERP.TallerAutomotriz.Infrastructure -s src/ERP.TallerAutomotriz.Web

# 6. Ejecutar la aplicación
dotnet run --project src/ERP.TallerAutomotriz.Web
```

> **Nota:** Si la BD no existe, EF Core la crea automáticamente al ejecutar `database update`. La app ejecuta también `MigrateAsync()` y siembra datos iniciales al arrancar.

Abrir el navegador en: **https://localhost:7057** (o el puerto que indique la consola).

## Credenciales de prueba

| Rol           | Email             | Contraseña  |
|---------------|-------------------|-------------|
| Administrador | admin@taller.com  | Admin123$   |

## Esquemas SQL generados

La base de datos se organiza en esquemas por módulo:

- `seguridad` — Usuarios, Roles, Claims, Tokens (Identity)
- `crm` — Clientes, Vehículos
- `taller` — OrdenesTrabajo, Servicios, Citas, ChecklistInspeccion, ControlesCalidad, Fotos, etc.
- `inventario` — Repuestos, Almacenes, StockAlmacenes, MovimientosInventario, Categorías
- `ventas` — Facturas, Pagos, Caja, Cotizaciones
- `compras` — Proveedores, OrdenesCompra, CuentasPagar
- `personal` — Tecnicos, RegistrosAsistencia, Comisiones
- `sistema` — Empresas, Sucursales, Parametros, LogsAuditoria, PlantillasNotificacion

## Datos sembrados (Seed)

Al arrancar por primera vez, el `DbInitializer` crea:

- 1 empresa demo (AutoTaller S.A.C.)
- 1 sucursal y 1 almacén principales, 1 caja
- 6 categorías de servicio y 7 categorías de repuestos
- 5 servicios y 5 repuestos de ejemplo
- 3 técnicos con distintos niveles
- 2 proveedores
- 3 clientes con 4 vehículos asociados
- Usuario admin (admin@taller.com / Admin123$)
- Roles: Administrador, Jefe de Taller, Recepcionista, Técnico, Cajero, Contador

## Diseño visual

La paleta y el layout están inspirados en el mockup proporcionado:

- **Sidebar** azul marino oscuro con acento naranja para el módulo activo.
- **Top bar** blanca con tabs (Dashboard / Facturación / Órdenes), buscador y menú de usuario.
- **Dashboard** con tarjetas KPI gradiente, lista de OTs con barras de progreso, anillos circulares para porcentajes, mini-calendario, gráficos de tendencia.
- Tipografía **Roboto**, esquinas redondeadas, sombras suaves.

## Stack técnico

| Capa             | Tecnologías                                                              |
|------------------|--------------------------------------------------------------------------|
| Presentación     | Blazor Server (.NET 8), MudBlazor 7, Material Icons                      |
| Aplicación       | DI nativa, DTOs, interfaces de servicios                                 |
| Infraestructura  | EF Core 8 (SQL Server), ASP.NET Identity, esquemas por módulo            |
| Tiempo real      | SignalR (incluido en Blazor Server) — listo para refresh de dashboard    |
| Autenticación    | Cookies + Identity, password robusto, lockout, security stamp            |

## Próximos pasos sugeridos

1. Implementar CRUD de Clientes + Vehículos (CRM completo).
2. Wizard de Recepción → generación de OT con QR y fotos.
3. Pantalla de Órdenes de Trabajo con kanban por estado.
4. Catálogo de Repuestos con escaneo de código de barras.
5. Punto de venta / facturación con impresión PDF.
6. Reportes con QuickGrid + exportación a Excel/PDF.
7. Hangfire para notificaciones programadas (recordatorios SMS/email).
8. Integración SUNAT/SAT/SRI según país.

---

© @DateTime.Now.Year AutoTaller ERP — Construido con ASP.NET Core 8 + Blazor.
