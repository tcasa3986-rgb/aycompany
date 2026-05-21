@echo off
color 0A
echo =================================================================
echo                    INICIANDO SISTEMA POLLERIA
echo =================================================================
echo.
echo Iniciando Servidor Backend (Puerto 3001)...
start "Backend Polleria" cmd /k "cd server && npm start"

echo.
echo Iniciando Interfaz Frontend...
start "Frontend Polleria" cmd /k "cd client && npm run dev"

echo.
echo =================================================================
echo ¡El sistema esta en funcionamiento!
echo.
echo IMPORTANTE: Han aparecido 2 ventanas negras adicionales.
echo Son los motores del sistema. NO SE DEBEN CERRAR MIENTRAS SE TRABAJA.
echo Para apagar el sistema, cierra las 2 ventanas negras de Node.js.
echo.
echo Se abrira tu navegador en http://localhost:5173 
echo =================================================================
timeout /t 3
start http://localhost:5173
exit
