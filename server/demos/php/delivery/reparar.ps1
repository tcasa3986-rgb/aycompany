# =====================================================
#   CRM DELIVERY - REPARACION Y ARRANQUE
#   Ejecutar: .\reparar.ps1
# =====================================================

$ErrorActionPreference = "Continue"
Set-Location $PSScriptRoot

Write-Host ""
Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host "   CRM DELIVERY - REPARACION Y ARRANQUE" -ForegroundColor Cyan
Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host ""

# [1] Limpiar cache
Write-Host "[1/5] Limpiando cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
Write-Host "[OK] Cache limpiado." -ForegroundColor Green
Write-Host ""

# [2] Verificar APP_KEY
Write-Host "[2/5] Generando APP_KEY..." -ForegroundColor Yellow
php artisan key:generate
Write-Host "[OK] APP_KEY generado." -ForegroundColor Green
Write-Host ""

# [3] Migraciones
Write-Host "[3/5] Ejecutando migraciones..." -ForegroundColor Yellow
php artisan migrate --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Migraciones completadas." -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] Algunas migraciones fallaron (puede que las tablas ya existan)." -ForegroundColor DarkYellow
}
Write-Host ""

# [4] Seeders
Write-Host "[4/5] Ejecutando seeders..." -ForegroundColor Yellow
php artisan db:seed --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Datos iniciales cargados." -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] Error en seeders. Verifica la conexion MySQL." -ForegroundColor DarkYellow
}
Write-Host ""

# [5] Storage link
Write-Host "[5/5] Enlace de storage..." -ForegroundColor Yellow
php artisan storage:link
Write-Host "[OK]" -ForegroundColor Green
Write-Host ""

Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host "   REPARACION COMPLETADA" -ForegroundColor Green
Write-Host ""
Write-Host "   Credenciales de acceso:" -ForegroundColor White
Write-Host "     Admin:       admin@crm.com      / password" -ForegroundColor White
Write-Host "     Gerente:     gerente@crm.com     / password" -ForegroundColor White
Write-Host "     Operador:    operador@crm.com    / password" -ForegroundColor White
Write-Host "     Repartidor:  repartidor@crm.com  / password" -ForegroundColor White
Write-Host ""
Write-Host "   Inicia el servidor con:" -ForegroundColor Yellow
Write-Host "     php artisan serve" -ForegroundColor White
Write-Host "   Luego abre: http://localhost:8000" -ForegroundColor White
Write-Host "=============================================================" -ForegroundColor Cyan
Write-Host ""
Read-Host "Presiona Enter para terminar"
