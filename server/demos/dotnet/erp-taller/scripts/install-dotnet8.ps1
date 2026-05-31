# =====================================================================
# Instalador asistido de .NET 8 SDK + Runtime ASP.NET Core 8
# Ejecuta este script con permisos de administrador (clic derecho > Ejecutar con PowerShell).
# Probará primero winget; si no está disponible, descargará el instalador oficial.
# =====================================================================

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host " Instalador de .NET 8 SDK para AutoTaller ERP " -ForegroundColor Cyan
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

function Test-Command($cmd) {
    return [bool](Get-Command $cmd -ErrorAction SilentlyContinue)
}

# 1) Intentar con winget (recomendado, viene con Windows 10/11)
if (Test-Command "winget") {
    Write-Host "[1/2] winget detectado. Instalando Microsoft.DotNet.SDK.8 ..." -ForegroundColor Yellow
    try {
        winget install --id Microsoft.DotNet.SDK.8 -e --accept-source-agreements --accept-package-agreements
        Write-Host ""
        Write-Host "[2/2] Instalando ASP.NET Core Runtime 8 (necesario para Blazor) ..." -ForegroundColor Yellow
        winget install --id Microsoft.DotNet.AspNetCore.8 -e --accept-source-agreements --accept-package-agreements
    }
    catch {
        Write-Host "winget falló. Probando descarga directa..." -ForegroundColor Red
    }
}
else {
    Write-Host "winget no disponible. Descargando instalador oficial..." -ForegroundColor Yellow

    $url = "https://aka.ms/dotnet/8.0/dotnet-sdk-win-x64.exe"
    $out = "$env:TEMP\dotnet-sdk-8-installer.exe"

    Write-Host "Descargando $url ..." -ForegroundColor Gray
    Invoke-WebRequest -Uri $url -OutFile $out -UseBasicParsing

    Write-Host "Ejecutando instalador (sigue las indicaciones de la ventana) ..." -ForegroundColor Yellow
    Start-Process -FilePath $out -Wait
}

Write-Host ""
Write-Host "Verificando instalación..." -ForegroundColor Cyan
$env:Path = [System.Environment]::GetEnvironmentVariable("Path", "Machine") + ";" +
            [System.Environment]::GetEnvironmentVariable("Path", "User")

dotnet --list-sdks
dotnet --list-runtimes

Write-Host ""
Write-Host "Listo. Cierra esta ventana y abre una nueva PowerShell para que el PATH se refresque." -ForegroundColor Green
Write-Host "Luego ejecuta:" -ForegroundColor Green
Write-Host "  cd C:\CRMyERP\erp-taller-automotriz" -ForegroundColor White
Write-Host "  dotnet restore" -ForegroundColor White
Write-Host ""
Read-Host "Presiona Enter para cerrar"
