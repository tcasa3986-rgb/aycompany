@echo off
setlocal enabledelayedexpansion
color 0A
title CRM Delivery - Reparacion Definitiva

cd /d "%~dp0"

echo.
echo =============================================================
echo    CRM DELIVERY - REPARACION DEFINITIVA
echo    Carpeta: %~dp0
echo =============================================================
echo.

:: ------------ 1) PHP y Composer ------------
echo [1/12] Verificando PHP y Composer...
php -v >nul 2>&1
if errorlevel 1 (
    echo    [X] PHP no encontrado. Abre la terminal de Laragon.
    pause & exit /b 1
)
composer -V >nul 2>&1
if errorlevel 1 (
    echo    [X] Composer no encontrado. Abre la terminal de Laragon.
    pause & exit /b 1
)
echo    [OK] PHP y Composer disponibles.
echo.

:: ------------ 2) .env ------------
echo [2/12] Verificando archivo .env...
if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        echo    [OK] .env creado desde .env.example
    ) else if exist "_crm_source\.env" (
        copy "_crm_source\.env" ".env" >nul
        echo    [OK] .env restaurado desde _crm_source
    ) else (
        echo    [X] No existe .env. Crealo manualmente.
        pause & exit /b 1
    )
) else (
    echo    [OK] .env ya existe.
)
echo.

:: ------------ 3) Composer install ------------
echo [3/12] composer install (puede tardar 1-3 min)...
composer install --no-interaction --prefer-dist --quiet
if errorlevel 1 (
    echo    [!] Reintentando con --ignore-platform-reqs...
    composer install --no-interaction --prefer-dist --ignore-platform-reqs --quiet
)
echo    [OK] Dependencias instaladas.
echo.

:: ------------ 4) Sanctum / Spatie / DomPDF ------------
echo [4/12] Asegurando paquetes clave...
if not exist "vendor\laravel\sanctum" (
    echo    Instalando laravel/sanctum...
    composer require laravel/sanctum --no-interaction --quiet
)
if not exist "vendor\spatie\laravel-permission" (
    echo    Instalando spatie/laravel-permission...
    composer require spatie/laravel-permission --no-interaction --quiet
)
if not exist "vendor\barryvdh\laravel-dompdf" (
    echo    Instalando barryvdh/laravel-dompdf...
    composer require barryvdh/laravel-dompdf --no-interaction --quiet
)
echo    [OK] Paquetes presentes.
echo.

:: ------------ 5) Publicar configs ------------
echo [5/12] Publicando configuraciones...
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag=permission-config --force --quiet
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force --quiet
echo    [OK] Configs publicadas.

:: Eliminar migraciones duplicadas de permission_tables (mantener solo la nuestra 2024_01_01)
for %%F in ("database\migrations\*_create_permission_tables.php") do (
    set "fname=%%~nF"
    set "first10=!fname:~0,10!"
    if not "!first10!"=="2024_01_01" (
        del "%%F" >nul 2>&1
        echo    [-] Migracion duplicada eliminada: %%~nxF
    )
)
:: Lo mismo con personal_access_tokens
for %%F in ("database\migrations\*_create_personal_access_tokens_table.php") do (
    set "fname=%%~nF"
    set "first10=!fname:~0,10!"
    if not "!first10!"=="2024_01_01" (
        del "%%F" >nul 2>&1
        echo    [-] Migracion duplicada eliminada: %%~nxF
    )
)
echo.

:: ------------ 6) Autoload ------------
echo [6/12] composer dump-autoload...
composer dump-autoload --quiet
echo    [OK] Autoload regenerado.
echo.

:: ------------ 7) APP_KEY ------------
echo [7/12] Verificando APP_KEY...
findstr /B "APP_KEY=base64:" .env >nul
if errorlevel 1 (
    php artisan key:generate --force --quiet
    echo    [OK] APP_KEY generada.
) else (
    echo    [OK] APP_KEY ya existe.
)
echo.

:: ------------ 8) Crear BD ------------
echo [8/12] Asegurando base de datos delivery_crm...
php -r "try { $p = new PDO('mysql:host=127.0.0.1;port=3306', 'root', ''); $p->exec('CREATE DATABASE IF NOT EXISTS `delivery_crm` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'); echo 'OK'; } catch(Exception $e) { echo 'FAIL: '.$e->getMessage(); exit(1); }" 2>nul
if errorlevel 1 (
    echo    [X] No se pudo conectar a MySQL en 127.0.0.1:3306 con usuario root.
    echo    Asegurate que Laragon este corriendo MySQL.
    pause & exit /b 1
)
echo    [OK] BD lista.
echo.

:: ------------ 9) Storage link ------------
echo [9/12] storage:link...
php artisan storage:link >nul 2>&1
echo    [OK] Symlink listo.
echo.

:: ------------ 10) Migrate fresh + seed ------------
echo [10/12] Reseteando BD y ejecutando migraciones + seeders...
php artisan migrate:fresh --seed --force
if errorlevel 1 (
    echo    [X] Error en migrate:fresh --seed. Mira el detalle arriba.
    pause & exit /b 1
)
echo    [OK] Migraciones y seeders OK.
echo.

:: ------------ 11) Clear caches ------------
echo [11/12] Limpiando caches...
php artisan optimize:clear >nul 2>&1
echo    [OK] Caches limpios.
echo.

:: ------------ 12) Listo ------------
echo.
echo =============================================================
echo    SISTEMA REPARADO Y LISTO
echo =============================================================
echo.
echo  URL: http://crm-delivery.test
echo.
echo  CREDENCIALES (password = password):
echo    admin@crm.com       (Super Admin)
echo    gerente@crm.com     (Admin)
echo    operador@crm.com    (Operador)
echo    repartidor@crm.com  (Repartidor - panel movil)
echo.
echo =============================================================
pause
endlocal
