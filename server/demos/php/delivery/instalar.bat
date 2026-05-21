@echo off
color 0A
echo.
echo =============================================================
echo    CRM DELIVERY - INSTALADOR PARA LARAGON
echo =============================================================
echo.

:: Verificar que estamos en la carpeta correcta
if not exist "%~dp0_crm_source" (
    echo [ERROR] No se encontro la carpeta _crm_source.
    echo Asegurate de ejecutar desde C:\CRMyERP\crm-delivery\
    pause
    exit /b 1
)

set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

echo [1/7] Verificando PHP y Composer...
php -v >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP no encontrado. Usa la terminal de Laragon.
    pause
    exit /b 1
)
composer -V >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Composer no encontrado. Usa la terminal de Laragon.
    pause
    exit /b 1
)
echo [OK] PHP y Composer encontrados.
echo.

echo [2/7] Creando proyecto Laravel base...
composer create-project laravel/laravel _laravel_temp --prefer-dist --no-interaction --quiet
if errorlevel 1 (
    echo [ERROR] No se pudo crear el proyecto Laravel.
    pause
    exit /b 1
)
echo [OK] Laravel instalado.
echo.

echo [3/7] Copiando archivos base de Laravel...
xcopy /E /Y /Q "_laravel_temp\*" "." >nul
rmdir /S /Q "_laravel_temp"
echo [OK] Archivos base copiados.
echo.

echo [4/7] Aplicando archivos CRM Delivery...
xcopy /E /Y /Q "_crm_source\*" "." >nul
echo [OK] Archivos CRM aplicados.
echo.

echo [5/7] Instalando dependencias adicionales...
composer require spatie/laravel-permission --quiet
if errorlevel 1 (
    echo [ADVERTENCIA] Error instalando spatie/laravel-permission
)
composer require barryvdh/laravel-dompdf --quiet
if errorlevel 1 (
    echo [ADVERTENCIA] Error instalando dompdf
)
composer require laravel/sanctum --quiet
if errorlevel 1 (
    echo [ADVERTENCIA] Error instalando sanctum
)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --quiet --tag=sanctum-config
composer require laravel/breeze --dev --quiet
php artisan breeze:install blade --quiet --no-interaction
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --quiet
echo [OK] Dependencias instaladas.
echo.

echo [6/7] Generando APP_KEY y ejecutando migraciones...
php artisan key:generate --quiet
php artisan storage:link --quiet
php artisan migrate --seed --force
if errorlevel 1 (
    echo [ADVERTENCIA] Error en migraciones.
    echo Verifica tu conexion MySQL en el archivo .env
    echo Luego ejecuta manualmente: php artisan migrate --seed
)
echo [OK] Base de datos configurada.
echo.

echo [7/7] Limpiando cache...
php artisan config:clear --quiet
php artisan cache:clear --quiet
php artisan view:clear --quiet
echo [OK] Cache limpiado.
echo.

echo =============================================================
echo    INSTALACION COMPLETADA
echo.
echo    Para iniciar el servidor:
echo      php artisan serve
echo.
echo    Abrir en navegador:
echo      http://localhost:8000
echo.
echo    Credenciales iniciales:
echo      Admin:     admin@crm.com / password
echo      Operador:  operador@crm.com / password
echo =============================================================
echo.
pause
