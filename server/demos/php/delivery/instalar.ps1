# =====================================================
#   CRM DELIVERY - INSTALADOR POWERSHELL
#   Ejecutar: .\instalar.ps1
# =====================================================

$ErrorActionPreference = "Continue"
$projectDir = $PSScriptRoot

Write-Host ""
Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host "   CRM DELIVERY - INSTALADOR" -ForegroundColor Cyan
Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host ""

# Ir a la carpeta del proyecto
Set-Location $projectDir

# Verificar _crm_source
if (-not (Test-Path "$projectDir\_crm_source")) {
    Write-Host "[ERROR] No se encontro la carpeta _crm_source" -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit 1
}

# ---- PASO 1: Verificar PHP ----
Write-Host "[1/7] Verificando PHP y Composer..." -ForegroundColor Yellow
$phpVersion = php -r "echo PHP_VERSION;" 2>$null
if (-not $phpVersion) {
    Write-Host "[ERROR] PHP no encontrado. Instala Laragon o agrega PHP al PATH." -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit 1
}
Write-Host "      PHP $phpVersion encontrado." -ForegroundColor Green

$compVersion = composer --version 2>$null | Select-Object -First 1
if (-not $compVersion) {
    Write-Host "[ERROR] Composer no encontrado." -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit 1
}
Write-Host "      $compVersion" -ForegroundColor Green
Write-Host ""

# ---- PASO 2: Crear proyecto Laravel ----
Write-Host "[2/7] Creando proyecto Laravel (puede tardar 1-2 minutos)..." -ForegroundColor Yellow
if (Test-Path "$projectDir\_laravel_temp") {
    Remove-Item "$projectDir\_laravel_temp" -Recurse -Force
}
composer create-project laravel/laravel _laravel_temp --prefer-dist --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] No se pudo crear el proyecto Laravel." -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit 1
}
Write-Host "[OK] Laravel instalado." -ForegroundColor Green
Write-Host ""

# ---- PASO 3: Copiar archivos Laravel ----
Write-Host "[3/7] Copiando archivos base de Laravel..." -ForegroundColor Yellow
$exclude = @('_crm_source', 'instalar.bat', 'instalar.ps1', 'README.md', '_laravel_temp')
Get-ChildItem "$projectDir\_laravel_temp" | ForEach-Object {
    Copy-Item $_.FullName "$projectDir\" -Recurse -Force
}
Remove-Item "$projectDir\_laravel_temp" -Recurse -Force
Write-Host "[OK] Archivos Laravel copiados." -ForegroundColor Green
Write-Host ""

# ---- PASO 4: Copiar archivos CRM ----
Write-Host "[4/7] Aplicando archivos CRM Delivery..." -ForegroundColor Yellow
Copy-Item "$projectDir\_crm_source\*" "$projectDir\" -Recurse -Force
Write-Host "[OK] Archivos CRM aplicados." -ForegroundColor Green
Write-Host ""

# ---- PASO 5: Instalar dependencias ----
Write-Host "[5/7] Instalando dependencias (spatie, dompdf, breeze)..." -ForegroundColor Yellow
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
Write-Host "[OK] Dependencias instaladas." -ForegroundColor Green
Write-Host ""

# ---- PASO 6: Configurar .env y BD ----
Write-Host "[6/7] Configurando APP_KEY y base de datos..." -ForegroundColor Yellow
php artisan key:generate
php artisan storage:link
Write-Host ""
Write-Host "IMPORTANTE: Verifica que MySQL este corriendo en Laragon" -ForegroundColor Cyan
Write-Host "            y que exista la BD: delivery_crm" -ForegroundColor Cyan
Write-Host ""
php artisan migrate --seed --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ADVERTENCIA] Error en migraciones. Verifica el archivo .env" -ForegroundColor DarkYellow
    Write-Host "              Luego ejecuta: php artisan migrate --seed" -ForegroundColor DarkYellow
} else {
    Write-Host "[OK] Base de datos configurada." -ForegroundColor Green
}
Write-Host ""

# ---- PASO 7: Limpiar cache ----
Write-Host "[7/7] Limpiando cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
Write-Host "[OK] Cache limpiado." -ForegroundColor Green
Write-Host ""

Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host "   INSTALACION COMPLETADA" -ForegroundColor Green
Write-Host ""
Write-Host "   Para iniciar: php artisan serve" -ForegroundColor White
Write-Host "   Abrir en:     http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "   Credenciales:" -ForegroundColor White
Write-Host "     Admin:    admin@crm.com / password" -ForegroundColor White
Write-Host "     Operador: operador@crm.com / password" -ForegroundColor White
Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host ""
Read-Host "Presiona Enter para terminar"
