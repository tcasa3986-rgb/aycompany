@echo off
color 0E
echo =================================================================
echo        CONFIGURADOR DE BASE DE DATOS - SISTEMA POLLERIA
echo =================================================================
echo.
echo NOTA: Asegurate de tener MySQL encendido (ej. usando XAMPP o Laragon).
echo       Por defecto el sistema intentara conectarse con el usuario "root"
echo       y sin contrasena.
echo.
set user=
set pass=
set /p user=Ingrese el usuario de MySQL (Presiona ENTER para usar 'root'): 
if "%user%"=="" set user=root
set /p pass=Ingrese la contrasena de MySQL (Presiona ENTER si NO TIENE contrasena): 

if "%pass%"=="" (
    set MYSQL_CMD=mysql -u %user%
) else (
    set MYSQL_CMD=mysql -u %user% -p%pass%
)

echo.
echo Procesando... Creando base de datos 'polleria_db' e importando tablas...

echo DROP DATABASE IF EXISTS polleria_db; CREATE DATABASE polleria_db; | %MYSQL_CMD% 2>nul
%MYSQL_CMD% polleria_db < polleria_db.sql 2>nul

if %errorlevel% neq 0 (
    color 0C
    echo.
    echo [ERROR] No se pudo conectar a MySQL o no se encontro "mysql" en el sistema.
    echo Asegurate de que XAMPP/Laragon esta corriendo y que las variables de
    echo entorno (PATH) de MySQL esten configuradas si no usas un instalador por defecto.
    echo Tambien verifica si cambiaron la conexion en server/src/config/db.js
) else (
    color 0A
    echo.
    echo [EXITO] La Base de Datos 'polleria_db' fue importada correctamente.
    echo         Inicia sesion en el sistema con admin@polleria.com / admin
)
echo.
pause
