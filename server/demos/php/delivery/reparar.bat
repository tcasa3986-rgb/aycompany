@echo off
color 0A
echo.
echo =============================================================
echo    CRM DELIVERY - REPARACION Y ARRANQUE
echo =============================================================
echo.

cd /d "%~dp0"

echo [1/5] Limpiando cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo [OK] Cache limpiado.
echo.

echo [2/5] Verificando APP_KEY...
php artisan key:generate --show
echo [OK] Verificado.
echo.

echo [3/5] Ejecutando migraciones...
php artisan migrate --force
if errorlevel 1 (
    echo [ADVERTENCIA] Algunas migraciones fallaron. Puede que ya existan las tablas.
) else (
    echo [OK] Migraciones completadas.
)
echo.

echo [4/5] Ejecutando seeders...
php artisan db:seed --force
if errorlevel 1 (
    echo [ADVERTENCIA] Error en seeders. Revisa la conexion MySQL.
) else (
    echo [OK] Datos iniciales cargados.
)
echo.

echo [5/5] Creando enlace de storage...
php artisan storage:link
echo.

echo =============================================================
echo    REPARACION COMPLETADA
echo.
echo    Credenciales de acceso:
echo      Admin:      admin@crm.com    / password
echo      Gerente:    gerente@crm.com  / password
echo      Operador:   operador@crm.com / password
echo      Repartidor: repartidor@crm.com / password
echo.
echo    Para iniciar el servidor ejecuta en OTRA terminal:
echo      php artisan serve
echo.
echo    Luego abre: http://localhost:8000
echo =============================================================
echo.
pause
