@echo off
net session >nul 2>&1
if %errorlevel% neq 0 (
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)
set "PROJDIR=%~dp0"
if "%PROJDIR:~-1%"=="\" set "PROJDIR=%PROJDIR:~0,-1%"
powershell -ExecutionPolicy Bypass -File "%PROJDIR%\instalar_plataforma.ps1" -ProyectoDir "%PROJDIR%"
pause
