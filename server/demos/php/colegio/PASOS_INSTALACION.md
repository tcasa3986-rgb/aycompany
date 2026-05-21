# 🚀 Guía de Instalación — CRM Colegio con Laragon

## ✅ Prerrequisitos confirmados
- [x] Laragon instalado
- [x] Composer disponible

---

## PASO 1 — Abrir la terminal de Laragon

1. Busca el ícono de **Laragon** en la barra de tareas (bandeja del sistema)
2. **Clic derecho** sobre el ícono
3. Selecciona **"Terminal"** (o "Cmder")

> La terminal de Laragon ya tiene PHP y Composer en el PATH automáticamente.

---

## PASO 2 — Ir a la carpeta del proyecto

En la terminal de Laragon, escribe:
```bash
cd C:\CRMyERP\crm-colegio
```

---

## PASO 3 — Ejecutar el script automático

Simplemente escribe:
```bash
setup.bat
```

El script hará todo automáticamente:
- ✅ Instala las dependencias de Laravel (via Composer)
- ✅ Genera la APP_KEY
- ✅ Crea la base de datos `colegio_crm`
- ✅ Ejecuta las migraciones (crea todas las tablas)
- ✅ Inserta datos de prueba (alumnos, pagos, personal)

---

## PASO 4 — Acceder al sistema

Una vez terminado el script, tienes 2 opciones:

### Opción A — Con `artisan serve` (desde terminal)
```bash
php artisan serve
```
Abre: **http://localhost:8000**

### Opción B — Con Laragon (dominio .test)
1. Mueve o copia la carpeta `crm-colegio` a `C:\laragon\www\`
2. Laragon detectará el proyecto automáticamente
3. Accede en: **http://crm-colegio.test**

---

## Credenciales de acceso

| Campo      | Valor                     |
|-----------|---------------------------|
| Email     | admin@colegio.edu.pe      |
| Contraseña | admin123                 |

---

## ❗ Solución de problemas comunes

### Error: "could not find driver" (PDO MySQL)
Laragon ya incluye las extensiones PHP necesarias. Si usas PHP del sistema, habilita en `php.ini`:
```
extension=pdo_mysql
extension=mbstring
extension=openssl
```

### Error en migraciones: "Access denied for user root"
El `.env` tiene `DB_PASSWORD=` (vacío). Si tu MySQL tiene contraseña:
1. Abre `C:\CRMyERP\crm-colegio\.env`
2. Cambia: `DB_PASSWORD=tu_contraseña`
3. Vuelve a ejecutar: `php artisan migrate --seed`

### Error: "Base de datos no existe"
Abre phpMyAdmin en http://localhost/phpmyadmin y crea:
- Nombre: `colegio_crm`
- Cotejamiento: `utf8mb4_unicode_ci`

Luego ejecuta en terminal:
```bash
php artisan migrate --seed
```

### La página muestra error 500
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize
```

---

## Comandos útiles de mantenimiento

```bash
# Limpiar toda la caché
php artisan optimize:clear

# Rehacer la base de datos desde cero
php artisan migrate:fresh --seed

# Ver el log de errores
php artisan tail  (o revisar storage/logs/laravel.log)
```
