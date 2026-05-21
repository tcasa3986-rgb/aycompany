# Diseño del Sistema - CRM Viaje 360

## Filosofía de Diseño

El sistema CRM Viaje 360 adopta una estética **Dark Premium** con acentos en degradados de océano y cielos vibrantes, transmitiendo la esencia de la aventura, el lujo y la exploración. La interfaz debe sentirse como el panel de control de una aerolínea premium: profesional, fluida y visualmente impresionante.

---

## 1. Sistema de Colores

### Paleta Principal

| Token | Color | Hex | Uso |
|-------|-------|-----|-----|
| `--color-primary` | Azul Océano | `#0EA5E9` | Botones primarios, enlaces, highlights |
| `--color-primary-dark` | Azul Profundo | `#0369A1` | Hover estados |
| `--color-secondary` | Violeta Viaje | `#8B5CF6` | Acentos, badges, tags |
| `--color-accent` | Turquesa | `#06B6D4` | Íconos activos, sparklines |
| `--color-success` | Verde Esmeralda | `#10B981` | Ventas cerradas, éxito |
| `--color-warning` | Ámbar Dorado | `#F59E0B` | VIP, advertencias |
| `--color-danger` | Coral Rojo | `#EF4444` | Alertas, cancelaciones |
| `--color-info` | Azul Info | `#3B82F6` | Información general |

### Paleta de Fondos (Dark Mode)

| Token | Hex | Descripción |
|-------|-----|-------------|
| `--bg-base` | `#0B0F1A` | Fondo principal del sistema |
| `--bg-surface` | `#111827` | Sidebar, top bar |
| `--bg-card` | `#1F2937` | Tarjetas y paneles |
| `--bg-card-hover` | `#273548` | Estado hover de tarjetas |
| `--bg-input` | `#1E293B` | Campos de formulario |
| `--border` | `#374151` | Bordes sutiles |

### Texto

| Token | Hex | Uso |
|-------|-----|-----|
| `--text-primary` | `#F9FAFB` | Títulos y texto principal |
| `--text-secondary` | `#9CA3AF` | Subtítulos, labels |
| `--text-muted` | `#6B7280` | Marcadores de posición |

---

## 2. Tipografía

```css
/* Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700&display=swap');

--font-heading : 'Outfit', sans-serif;   /* Títulos del sistema */
--font-body    : 'Inter', sans-serif;    /* Texto general */

/* Escala tipográfica */
--text-xs  : 0.75rem;   /* 12px – labels, badges */
--text-sm  : 0.875rem;  /* 14px – texto secundario */
--text-base: 1rem;      /* 16px – texto normal */
--text-lg  : 1.125rem;  /* 18px – subtítulos sección */
--text-xl  : 1.25rem;   /* 20px – títulos de tarjeta */
--text-2xl : 1.5rem;    /* 24px – KPI numbers */
--text-3xl : 1.875rem;  /* 30px – títulos de página */
--text-4xl : 2.25rem;   /* 36px – hero stats */
```

---

## 3. Layout General

```
┌─────────────────────────────────────────────────────────────┐
│  TOPBAR  [Logo] [Búsqueda]          [Notifs] [Perfil]       │
├──────────┬──────────────────────────────────────────────────┤
│          │                                                   │
│ SIDEBAR  │           ÁREA DE CONTENIDO PRINCIPAL           │
│  260px   │                 (fluid)                          │
│          │                                                   │
│ [Nav]    │  ┌─────────┐ ┌─────────┐ ┌─────────┐           │
│ [Módulos]│  │  KPI 1  │ │  KPI 2  │ │  KPI 3  │           │
│          │  └─────────┘ └─────────┘ └─────────┘           │
│          │                                                   │
│          │  ┌───────────────────┐ ┌──────────────────────┐ │
│          │  │   Gráfico Principal│ │  Pipeline / Actividad│ │
│          │  └───────────────────┘ └──────────────────────┘ │
└──────────┴──────────────────────────────────────────────────┘
```

---

## 4. Componentes del Sistema

### 4.1 Sidebar de Navegación

- **Fondo:** `#111827` con borde derecho `1px solid #1F2937`
- **Logo:** Gradient horizontal `#0EA5E9 → #8B5CF6` con ícono de avión/globo terráqueo
- **Items de menú:** Hover con background `rgba(14,165,233,0.1)` y borde izquierdo `3px solid #0EA5E9`
- **Ícono activo:** Color `#0EA5E9` con glow sutil `drop-shadow(0 0 6px #0EA5E9)`
- **Submenús:** Colapsables con animación slide-down (300ms ease)

**Módulos del Sidebar:**
```
🏠  Dashboard
👥  Clientes
   └ Lista de Clientes
   └ Nuevo Cliente
   └ Segmentación
🗺️  Destinos & Paquetes
   └ Destinos
   └ Paquetes Turísticos
📊  Pipeline de Ventas
   └ Kanban de Oportunidades
   └ Lista de Oportunidades
📋  Reservas
   └ Todas las Reservas
   └ Nueva Reserva
💳  Pagos & Facturación
   └ Pagos
   └ Facturas
✈️  Proveedores
📧  Marketing
   └ Campañas
   └ Segmentos
✅  Tareas
📈  Reportes
⚙️  Configuración
```

### 4.2 Topbar

- **Altura:** 70px, fijo en la parte superior (`position: sticky; top: 0`)
- **Fondo:** `rgba(11,15,26,0.85)` con `backdrop-filter: blur(12px)`
- **Borde inferior:** `1px solid rgba(55,65,81,0.5)`
- **Búsqueda global:** Campo expandible con ícono de lupa, busca en clientes, reservas, paquetes
- **Campana de notificaciones:** Badge rojo con cantidad, dropdown animado
- **Avatar de usuario:** Círculo con gradiente, muestra nombre y rol al hover

### 4.3 Tarjetas KPI del Dashboard

```
┌────────────────────────────────────────┐
│  ✈  Reservas del Mes           +12.5% ↑│
│                                         │
│         247                            │
│                                         │
│  ──────────────────  mini sparkline    │
└────────────────────────────────────────┘
```

- **Fondo:** `#1F2937` con borde `1px solid #374151`
- **Borde superior colorido:** `4px solid var(--color-primary)`
- **Número principal:** `font-size: 2.25rem; font-weight: 800; color: #F9FAFB`
- **Badge de cambio:** Verde `#10B981` si positivo, rojo `#EF4444` si negativo
- **Mini sparkline:** Línea animada con área degradada (Chart.js)
- **Hover:** `transform: translateY(-3px); box-shadow: 0 12px 40px rgba(14,165,233,0.15)`

**4 KPIs principales:**
1. **Reservas del Mes** — ícono avión — color azul `#0EA5E9`
2. **Ingresos Totales** — ícono dólar — color verde `#10B981`
3. **Nuevos Clientes** — ícono usuarios — color violeta `#8B5CF6`
4. **Tasa de Conversión** — ícono gráfico — color ámbar `#F59E0B`

### 4.4 Pipeline Kanban

Columnas de arrastrar y soltar (drag & drop con `@dnd-kit/core`):

```
┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐
│ Nuevo Lead │ │ Cotizado   │ │ Negociac.  │ │ Ganado ✓  │
│ (gris)     │ │ (azul)     │ │ (ámbar)    │ │ (verde)    │
├────────────┤ ├────────────┤ ├────────────┤ ├────────────┤
│ ┌────────┐ │ │ ┌────────┐ │ │ ┌────────┐ │ │ ┌────────┐ │
│ │ Card   │ │ │ │ Card   │ │ │ │ Card   │ │ │ │ Card   │ │
│ │ Cliente│ │ │ │        │ │ │ │        │ │ │ │        │ │
│ │ $1,200 │ │ │ │ $3,500 │ │ │ │ $2,800 │ │ │ │ $5,100 │ │
│ └────────┘ │ │ └────────┘ │ │ └────────┘ │ │ └────────┘ │
└────────────┘ └────────────┘ └────────────┘ └────────────┘
```

- Cada card muestra: avatar del cliente, nombre, destino, valor, fecha de cierre
- Borde izquierdo con el color de la etapa
- Drag con animación de lift-shadow

### 4.5 Lista de Clientes

- **Tabla con sticky header** y filas con efecto hover `rgba(14,165,233,0.05)`
- **Avatar generado:** Iniciales del cliente en un círculo con gradiente dinámico
- **Badge de categoría:** `Nuevo | Recurrente | VIP | Inactivo` con colores propios
- **Columnas:** Avatar, Nombre, Email, Teléfono, Destino Interés, Categoría, Agente, Acciones
- **Acciones:** Ver, Editar, Agregar Interacción (íconos con tooltip)
- **Buscador + Filtros:** Por categoría, fuente, agente, fecha de registro

### 4.6 Formularios

- **Campos:** Fondo `#1E293B`, borde `#374151`, foco con `box-shadow: 0 0 0 3px rgba(14,165,233,0.25)` y borde `#0EA5E9`
- **Selects:** Personalizados con icono de flecha y opciones con scroll
- **Date pickers:** Con calendario oscuro integrado
- **Botón primario:** Gradiente `linear-gradient(135deg, #0EA5E9, #8B5CF6)` con hover brightness(1.1)
- **Botón secundario:** Borde `#374151` con hover fondo `#273548`
- **Validación:** Mensajes en rojo con ícono de error, verde al pasar validación

### 4.7 Gráficos y Visualizaciones (Chart.js / Recharts)

| Gráfico | Tipo | Módulo |
|---------|------|--------|
| Ingresos por mes | Área con gradiente | Dashboard |
| Destinos más vendidos | Donut moderno | Dashboard |
| Pipeline por etapa | Barras horizontales | Ventas |
| Tasa de conversión | Gauge/Radial | Reportes |
| Clientes por fuente | Pie chart | Clientes |
| Tendencia de reservas | Línea suavizada | Reportes |

**Colores de gráficos:** `['#0EA5E9', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4']`

### 4.8 Notificaciones / Toasts

- Posición: **top-right**, apilados
- Toast de éxito: borde izquierdo verde, ícono check
- Toast de error: borde izquierdo rojo, ícono X
- Toast de info: borde izquierdo azul
- Animación: slide-in desde la derecha + fade-out después de 4s

---

## 5. Animaciones y Microinteracciones

```css
/* Transición global */
* { transition: all 0.2s ease; }

/* Entrada de tarjetas */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Pulse para badges de notificación */
@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50%       { transform: scale(1.15); }
}

/* Shimmer skeleton loading */
@keyframes shimmer {
  0%   { background-position: -1000px 0; }
  100% { background-position: 1000px 0; }
}
```

**Interacciones clave:**
- Los números KPI incrementan animadamente al cargar (Counter animation)
- Las filas de tabla aparecen con `stagger delay` (50ms por fila)
- Los gráficos se dibujan con animación de entrada (Chart.js animation)
- El sidebar colapsa suavemente a modo icono en pantallas medianas
- Los modales entran con `scale(0.95) → scale(1)` + blur de fondo

---

## 6. Mapa de Módulos y Pantallas

| Módulo | Pantallas | Funcionalidad Principal |
|--------|-----------|------------------------|
| **Dashboard** | 1 pantalla | KPIs, gráficos, actividad reciente, tareas pendientes |
| **Clientes** | Lista, Detalle, Formulario | CRUD completo, historial, interacciones, etiquetas |
| **Destinos** | Lista, Formulario | Catálogo de destinos y países |
| **Paquetes** | Lista, Detalle, Formulario | CRUD paquetes con precios e itinerario |
| **Pipeline** | Kanban, Lista | Drag&Drop por etapas, conversión |
| **Reservas** | Lista, Detalle, Formulario | Reservas con pasajeros y servicios |
| **Pagos** | Lista, Formulario | Registro de pagos, estados |
| **Facturas** | Lista, Detalle | Generación PDF, estados |
| **Proveedores** | Lista, Formulario | CRUD proveedores |
| **Marketing** | Campañas, Segmentos | Gestión de campañas |
| **Tareas** | Lista Kanban | Gestión de to-dos del agente |
| **Reportes** | 5 sub-pantallas | Ventas, Clientes, Destinos, Agentes, Financiero |
| **Configuración** | Usuarios, Roles, Sistema | Administración |

---

## 7. Responsive Design

| Breakpoint | Comportamiento |
|------------|----------------|
| `< 768px`  | Sidebar oculto (drawer), layout de una columna, KPIs en 2x2 |
| `768px – 1024px` | Sidebar colapsado a solo íconos (64px), grid 2 columnas |
| `> 1024px` | Sidebar completo (260px), grid máximo |

---

## 8. Stack Frontend

| Tecnología | Versión | Uso |
|------------|---------|-----|
| React | 18.x | Framework principal |
| Vite | 5.x | Build tool |
| React Router | 6.x | Enrutamiento SPA |
| Axios | 1.x | HTTP client |
| Chart.js + react-chartjs-2 | 4.x | Visualizaciones |
| @dnd-kit/core | 6.x | Drag & Drop Kanban |
| React Hook Form | 7.x | Manejo de formularios |
| Zustand | 4.x | Estado global ligero |
| date-fns | 3.x | Manipulación de fechas |
| lucide-react | Latest | Íconos SVG |

---

## 9. Stack Backend (Node.js + Express)

| Tecnología | Versión | Uso |
|------------|---------|-----|
| Node.js | 20 LTS | Runtime |
| Express | 4.x | Framework API REST |
| mysql2 | 3.x | Driver MySQL |
| Sequelize | 6.x | ORM |
| jsonwebtoken | 9.x | Autenticación JWT |
| bcryptjs | 2.x | Hash de contraseñas |
| multer | 1.x | Subida de archivos |
| nodemailer | 6.x | Envío de emails |
| pdfkit | 0.x | Generación de PDFs |
| helmet | 7.x | Seguridad HTTP |
| cors | 2.x | CORS |
| dotenv | 16.x | Variables de entorno |

---

## 10. Estructura de Carpetas del Proyecto

```
crm-viaje360/
├── backend/
│   ├── src/
│   │   ├── config/           # DB config, env
│   │   ├── controllers/      # Lógica de endpoints
│   │   ├── models/           # Modelos Sequelize
│   │   ├── routes/           # Rutas Express
│   │   ├── middlewares/      # Auth, validación
│   │   ├── services/         # Lógica de negocio
│   │   └── utils/            # PDF, email, helpers
│   ├── .env
│   └── package.json
│
└── frontend/
    ├── src/
    │   ├── assets/           # Logos, imágenes
    │   ├── components/       # Componentes reutilizables
    │   │   ├── ui/           # Button, Input, Modal, Badge...
    │   │   ├── charts/       # Componentes de gráficos
    │   │   ├── layout/       # Sidebar, Topbar, Layout
    │   │   └── kanban/       # Pipeline Kanban
    │   ├── pages/            # Una carpeta por módulo
    │   │   ├── Dashboard/
    │   │   ├── Clientes/
    │   │   ├── Paquetes/
    │   │   ├── Pipeline/
    │   │   ├── Reservas/
    │   │   ├── Pagos/
    │   │   ├── Marketing/
    │   │   ├── Tareas/
    │   │   ├── Reportes/
    │   │   └── Configuracion/
    │   ├── store/            # Zustand stores
    │   ├── hooks/            # Custom React hooks
    │   ├── services/         # API calls (axios)
    │   ├── utils/            # Formatters, helpers
    │   ├── styles/           # index.css, variables.css
    │   ├── App.jsx
    │   └── main.jsx
    └── package.json
```
