@echo off
title CRM Condominio — Sistema
color 0A

echo.
echo ╔══════════════════════════════════════════════╗
echo ║       CRM CONDOMINIO - Iniciando Sistema     ║
echo ╚══════════════════════════════════════════════╝
echo.

echo [1/3] Verificando MySQL...
mysql -u root -e "USE condominio_crm;" 2>nul
if %ERRORLEVEL% NEQ 0 (
  echo [!] Creando base de datos...
  mysql -u root < database\schema.sql
  mysql -u root < database\seed.sql
  echo [OK] Base de datos creada con datos de prueba
) else (
  echo [OK] Base de datos condominio_crm encontrada
)

echo.
echo [2/3] Iniciando Backend (puerto 5000)...
start "CRM Backend" cmd /k "cd backend && npm run dev"
timeout /t 3 /nobreak >nul

echo.
echo [3/3] Iniciando Frontend (puerto 5173)...
start "CRM Frontend" cmd /k "cd frontend && npm run dev"
timeout /t 3 /nobreak >nul

echo.
echo ✅ Sistema iniciado correctamente!
echo.
echo  Backend:   http://localhost:5000
echo  Frontend:  http://localhost:5173
echo  API Docs:  http://localhost:5000/api/health
echo.
echo  Usuario:   admin@laspalmas.com
echo  Password:  Admin123!
echo.
echo Presiona cualquier tecla para abrir el sistema en el navegador...
pause >nul
start http://localhost:5173
