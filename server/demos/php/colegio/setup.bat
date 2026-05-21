@echo off
title CRM Colegio - Instalacion Automatica
color 1F
cls

echo.
echo  ============================================================
echo     CRM COLEGIO - Instalacion Automatica para Laragon
echo  ============================================================
echo.

:: ── Verificar PHP ──────────────────────────────────────────────
echo [1/7] Verificando PHP...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    color 4F
    echo  ERROR: PHP no encontrado. Abre este script desde la terminal de Laragon.
    echo  En Laragon: clic derecho en el icono bandeja ^> Terminal
    pause
    exit /b 1
)
php --version | findstr /i "8\." >nul
if %errorlevel% neq 0 (
    echo  ADVERTENCIA: Se recomienda PHP 8.2 o superior.
)
echo  OK - PHP encontrado.

:: ── Verificar Composer ─────────────────────────────────────────
echo.
echo [2/7] Verificando Composer...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    color 4F
    echo  ERROR: Composer no encontrado.
    echo  Descargalo en: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo  OK - Composer encontrado.

:: ── Ir al directorio del proyecto ─────────────────────────────
echo.
echo [3/7] Navegando al directorio del proyecto...
cd /d "%~dp0"
echo  Directorio: %CD%

:: ── Instalar dependencias Laravel ─────────────────────────────
echo.
echo [4/7] Instalando dependencias de Laravel (puede tardar 2-3 minutos)...
echo  Por favor espera...
composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    color 4F
    echo  ERROR: Fallo la instalacion de dependencias.
    echo  Verifica tu conexion a internet.
    pause
    exit /b 1
)
echo  OK - Dependencias instaladas.

:: ── Generar APP_KEY ────────────────────────────────────────────
echo.
echo [5/7] Generando clave de aplicacion...
php artisan key:generate --ansi
if %errorlevel% neq 0 (
    color 4F
    echo  ERROR al generar la clave.
    pause
    exit /b 1
)
echo  OK - Clave generada.

:: ── Crear base de datos ────────────────────────────────────────
echo.
echo [6/7] Creando base de datos colegio_crm en MySQL...
echo  Intentando conectar con root sin contrasena...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS colegio_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
if %errorlevel% neq 0 (
    echo  No se pudo crear la BD automaticamente.
    echo  Abre phpMyAdmin en http://localhost/phpmyadmin y crea manualmente:
    echo    Nombre: colegio_crm
    echo    Cotejamiento: utf8mb4_unicode_ci
    echo.
    echo  Luego presiona cualquier tecla para continuar...
    pause >nul
) else (
    echo  OK - Base de datos creada.
)

:: ── Migrar y Sembrar ───────────────────────────────────────────
echo.
echo [7/7] Ejecutando migraciones y datos de prueba...
php artisan migrate --force
if %errorlevel% neq 0 (
    color 4F
    echo  ERROR en las migraciones. Verifica la conexion a MySQL en .env
    pause
    exit /b 1
)
php artisan db:seed --force
echo  OK - Base de datos lista con datos de prueba.

:: ── Publicar assets de sesion ──────────────────────────────────
php artisan session:table >nul 2>&1
php artisan migrate --force >nul 2>&1

:: ── Limpiar cache ─────────────────────────────────────────────
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan view:clear >nul 2>&1

:: ── Optimizar ─────────────────────────────────────────────────
php artisan optimize >nul 2>&1

color 2F
echo.
echo  ============================================================
echo     INSTALACION COMPLETADA EXITOSAMENTE
echo  ============================================================
echo.
echo  Credenciales de acceso:
echo    URL:        http://crm-colegio.test  (Laragon)
echo                http://localhost:8000    (artisan serve)
echo    Email:      admin@colegio.edu.pe
echo    Contrasena: admin123
echo.
echo  Para iniciar el servidor de desarrollo:
echo    php artisan serve
echo.

set /p INICIAR="  Deseas iniciar el servidor ahora? (s/n): "
if /i "%INICIAR%"=="s" (
    echo.
    echo  Iniciando servidor en http://localhost:8000 ...
    echo  Presiona Ctrl+C para detenerlo.
    echo.
    php artisan serve
)

pause
