# Sistema de Inventario TI - Laravel 11

Sistema moderno de gestión de inventario de equipos TI desarrollado con Laravel 11, diseño oscuro y gradientes vibrantes.

## 🚀 Características

- **Dashboard Moderno**: KPIs y gráficos con Chart.js
- **Gestión de Equipos**: CRUD completo con filtros avanzados
- **Gestión de Empleados**: Control de personal por sucursal
- **Asignaciones**: Tracking de equipos asignados a empleados
- **Catálogos**: Sucursales, Marcas, Tipos de Equipo
- **Control de Roles**: Admin y Usuario con permisos diferenciados
- **UI Profesional**: Diseño oscuro con gradientes azul/cyan
- **Responsive**: Adaptado a móvil, tablet y desktop

## 📋 Requisitos

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM

## 🔧 Instalación

1. **Clonar el repositorio**
```bash
cd inventario_ti_laravel
```

2. **Instalar dependencias**
```bash
composer install
npm install
```

3. **Configurar el entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar la base de datos en `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_ti
DB_USERNAME=root
DB_PASSWORD=
```

5. **Ejecutar migraciones y seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Compilar assets**
```bash
npm run dev
```

7. **Iniciar servidor**
```bash
php artisan serve
```

## 👤 Usuarios de Prueba

- **Administrador**: 
  - Email: `admin@inventario.com`
  - Password: `password`
  
- **Usuario Normal**: 
  - Email: `usuario@inventario.com`
  - Password: `password`

## 📦 Módulos

### Dashboard
- Total de equipos, asignados, disponibles, en reparación
- Gráfico de distribución por estado
- Gráfico de asignaciones mensuales
- Últimas 10 asignaciones

### Equipos
- CRUD completo
- Búsqueda por código/serie
- Filtros por estado
- Dropdowns dependientes Marca -> Modelo

### Empleados
- CRUD completo
- Búsqueda por DNI/nombre
- Filtros por estado
- Vinculación con cargo y área

### Asignaciones
- Registro de entregas
- Control de devoluciones
- Actualización automática del estado del equipo

### Catálogos
- Sucursales (con contadores)
- Marcas (con contadores de modelos/equipos)
- Tipos de Equipo (con contador)

## 🎨 Stack Tecnológico

- **Backend**: Laravel 11, MySQL
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Gráficos**: Chart.js
- **Autenticación**: Laravel Breeze

## 🔒 Seguridad

- Middleware de roles (`role:Administrador`)
- Filtrado automático por sucursal para usuarios no-admin
- Validación de formularios
- CSRF protection

## 📄 Licencia

Este proyecto es privado.
