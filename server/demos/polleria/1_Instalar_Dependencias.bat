@echo off
color 0B
echo =================================================================
echo        INSTALADOR DE DEPENDENCIAS - SISTEMA POLLERIA
echo =================================================================
echo.
echo Este proceso necesita conexion a internet. Puede tardar unos minutos.
echo.
echo [1/2] Instalando dependencias del Backend (Servidor Node.js)...
cd server
call npm install
echo.
echo [2/2] Instalando dependencias del Frontend (Cliente React.js)...
cd ../client
call npm install
cd ..
echo.
echo =================================================================
echo   [EXITO] TODAS LAS DEPENDENCIAS FUERON INSTALADAS
echo   Puedes proceder con el siguiente paso: Configurar Base de Datos.
echo =================================================================
pause
