# 🎓 CRM Colegio — Guía de Instalación

## Requisitos
- PHP 8.2+
- Composer
- MySQL 5.7+ / MariaDB 10.4+
- Node.js 18+ (opcional, solo si usas Vite)

---

## Opción A — Instalación Completa con Laravel (Recomendada)

### 1. Crear proyecto Laravel
```bash
composer create-project laravel/laravel crm-colegio
cd crm-colegio
```

### 2. Copiar los archivos generados
Copia las carpetas del proyecto generado sobre tu instalación de Laravel:
```
app/Http/Controllers/   → reemplaza/añade controladores
app/Models/             → copia todos los modelos
database/migrations/    → copia las migraciones
database/seeders/       → copia DatabaseSeeder.php
resources/views/        → copia todas las vistas
routes/web.php          → reemplaza el archivo
```

### 3. Configurar el archivo .env
```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` con la configuración de tu base de datos:
```env
APP_NAME="CRM Colegio"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=colegio_crm
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Crear la base de datos
```sql
CREATE DATABASE colegio_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

### 6. Levantar el servidor
```bash
php artisan serve
```

Visita: **http://localhost:8000**

---

## Opción B — Importar SQL directamente

Si prefieres importar el esquema directamente en MySQL:

```bash
mysql -u root -p colegio_crm < database/colegio_crm.sql
```

---

## Credenciales de acceso

| Usuario          | Email                        | Contraseña | Rol          |
|-----------------|------------------------------|------------|--------------|
| Administrador   | admin@colegio.edu.pe         | admin123   | admin        |
| Secretaria      | secretaria@colegio.edu.pe    | admin123   | secretaria   |
| Contador        | contador@colegio.edu.pe      | admin123   | contador     |

> ⚠️ **Cambia estas contraseñas después del primer acceso.**

---

## Módulos del Sistema

| Módulo          | Ruta          | Descripción                              |
|----------------|---------------|------------------------------------------|
| Dashboard       | /dashboard    | Estadísticas, gráficas y resúmenes       |
| Alumnos         | /alumnos      | CRUD completo de estudiantes             |
| Matrículas      | /matriculas   | Registro de matrículas por año escolar   |
| Pagos           | /pagos        | Control de pagos y estados de cuenta     |
| Personal        | /personal     | Gestión de docentes y administrativos    |
| Mensajes        | /mensajes     | Sistema de mensajería interna            |

---

## Estructura del Proyecto

```
crm-colegio/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/LoginController.php
│   │       ├── DashboardController.php
│   │       ├── AlumnoController.php
│   │       ├── MatriculaController.php
│   │       ├── PagoController.php
│   │       ├── PersonalController.php
│   │       └── MensajeController.php
│   └── Models/
│       ├── User.php, Alumno.php, Grado.php
│       ├── Seccion.php, Matricula.php
│       ├── ConceptoPago.php, Pago.php
│       ├── Personal.php, Mensaje.php
├── database/
│   ├── colegio_crm.sql          ← Script SQL directo
│   ├── migrations/              ← Migraciones Laravel
│   └── seeders/DatabaseSeeder.php
├── resources/views/
│   ├── layouts/app.blade.php   ← Layout principal
│   ├── auth/login.blade.php
│   ├── dashboard/index.blade.php
│   ├── students/               ← Vistas de alumnos
│   ├── enrollments/            ← Vistas de matrículas
│   ├── payments/               ← Vistas de pagos
│   ├── staff/                  ← Vistas de personal
│   └── messages/               ← Vistas de mensajes
└── routes/web.php
```

---

## Tecnologías utilizadas

- **Backend:** PHP 8.2 + Laravel 11
- **Base de datos:** MySQL 8 / MariaDB
- **Frontend:** Blade Templates + CSS puro (sin frameworks externos)
- **Gráficas:** Chart.js 4.4
- **Íconos:** Font Awesome 6.5
- **Autenticación:** Laravel Auth (sesiones nativas)

---

## Soporte
Para reportar errores o solicitar nuevas funcionalidades, contacta al equipo de desarrollo.
