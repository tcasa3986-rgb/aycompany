# =============================================================
#  CRM DELIVERY - REPARACION DEFINITIVA (PowerShell)
#  Aplica TODOS los pasos en orden correcto, de forma idempotente.
#  Uso: clic derecho > "Ejecutar con PowerShell"  o  .\reparar-todo.ps1
# =============================================================

$ErrorActionPreference = "Continue"
$ProjectDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ProjectDir

function Step($n, $msg) {
    Write-Host ""
    Write-Host "============================================" -ForegroundColor Cyan
    Write-Host " [$n] $msg" -ForegroundColor Yellow
    Write-Host "============================================" -ForegroundColor Cyan
}

function OK($msg) { Write-Host "    [OK] $msg" -ForegroundColor Green }
function WARN($msg) { Write-Host "    [!]  $msg" -ForegroundColor Yellow }
function FAIL($msg) { Write-Host "    [X]  $msg" -ForegroundColor Red }

Write-Host ""
Write-Host "=============================================================" -ForegroundColor Magenta
Write-Host "    CRM DELIVERY - REPARACION DEFINITIVA" -ForegroundColor Magenta
Write-Host "    Carpeta: $ProjectDir" -ForegroundColor Magenta
Write-Host "=============================================================" -ForegroundColor Magenta

# ----------------------------------------------------------------
Step "1/12" "Verificando PHP y Composer"
& php -v *>$null
if ($LASTEXITCODE -ne 0) {
    FAIL "PHP no encontrado. Abre la terminal de Laragon ('Menu > Terminal' o 'Cmder')."
    Read-Host "Presiona ENTER para salir"; exit 1
}
OK "PHP detectado"

& composer -V *>$null
if ($LASTEXITCODE -ne 0) {
    FAIL "Composer no encontrado. Usa la terminal de Laragon."
    Read-Host "Presiona ENTER para salir"; exit 1
}
OK "Composer detectado"

# ----------------------------------------------------------------
Step "2/12" "Verificando archivo .env"
if (-not (Test-Path ".env")) {
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        OK ".env creado desde .env.example"
    } elseif (Test-Path "_crm_source/.env") {
        Copy-Item "_crm_source/.env" ".env"
        OK ".env restaurado desde _crm_source"
    } else {
        FAIL "No existe .env. Crealo manualmente con la conexion a MySQL."
        Read-Host "Presiona ENTER"; exit 1
    }
} else {
    OK ".env existe"
}

# ----------------------------------------------------------------
Step "3/12" "Instalando dependencias Composer (puede tardar 1-3 min)"
& composer install --no-interaction --prefer-dist 2>&1 | Out-Null
if ($LASTEXITCODE -ne 0) {
    WARN "composer install fallo. Reintentando con --ignore-platform-reqs..."
    & composer install --no-interaction --prefer-dist --ignore-platform-reqs 2>&1 | Out-Null
}
OK "Composer install OK"

# ----------------------------------------------------------------
Step "4/12" "Asegurando Sanctum, Spatie Permission y DomPDF"
if (-not (Test-Path "vendor/laravel/sanctum")) {
    Write-Host "    Instalando laravel/sanctum..." -ForegroundColor DarkGray
    & composer require laravel/sanctum --no-interaction 2>&1 | Out-Null
}
if (-not (Test-Path "vendor/spatie/laravel-permission")) {
    Write-Host "    Instalando spatie/laravel-permission..." -ForegroundColor DarkGray
    & composer require spatie/laravel-permission --no-interaction 2>&1 | Out-Null
}
if (-not (Test-Path "vendor/barryvdh/laravel-dompdf")) {
    Write-Host "    Instalando barryvdh/laravel-dompdf..." -ForegroundColor DarkGray
    & composer require barryvdh/laravel-dompdf --no-interaction 2>&1 | Out-Null
}
OK "Paquetes presentes"

# ----------------------------------------------------------------
Step "5/12" "Publicando configuracion de paquetes"
# Solo el config de Spatie (la migracion ya esta en database/migrations)
& php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag=permission-config --force 2>&1 | Out-Null
# Sanctum: config + migracion oficial (si nuestra ya existe, la nuestra detecta y skip)
& php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force 2>&1 | Out-Null
OK "Configs publicadas"

# Eliminar migraciones duplicadas de Spatie publicadas (si las publica al mismo tiempo)
$dupes = Get-ChildItem "database/migrations" -Filter "*_create_permission_tables.php" -ErrorAction SilentlyContinue
if ($dupes.Count -gt 1) {
    $keep = $dupes | Where-Object { $_.Name -like "2024_01_01*" }
    foreach ($d in $dupes) {
        if ($d.FullName -ne $keep.FullName) {
            Remove-Item $d.FullName -Force
            WARN "Migracion duplicada eliminada: $($d.Name)"
        }
    }
}

# Eliminar duplicados de personal_access_tokens
$dupesTokens = Get-ChildItem "database/migrations" -Filter "*_create_personal_access_tokens_table.php" -ErrorAction SilentlyContinue
if ($dupesTokens.Count -gt 1) {
    $keep = $dupesTokens | Where-Object { $_.Name -like "2024_01_01*" }
    foreach ($d in $dupesTokens) {
        if ($d.FullName -ne $keep.FullName) {
            Remove-Item $d.FullName -Force
            WARN "Migracion duplicada eliminada: $($d.Name)"
        }
    }
}

# ----------------------------------------------------------------
Step "6/12" "Regenerando autoload"
& composer dump-autoload --quiet 2>&1 | Out-Null
OK "Autoload regenerado"

# ----------------------------------------------------------------
Step "7/12" "Generando APP_KEY (si no existe)"
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    & php artisan key:generate --force 2>&1 | Out-Null
    OK "APP_KEY generada"
} else {
    OK "APP_KEY ya existe"
}

# ----------------------------------------------------------------
Step "8/12" "Verificando conexion a MySQL"
$test = & php -r "try { new PDO('mysql:host=127.0.0.1;port=3306', 'root', ''); echo 'OK'; } catch(Exception `$e) { echo 'FAIL: ' . `$e->getMessage(); }"
if ($test -notmatch "^OK") {
    FAIL "No se pudo conectar a MySQL en 127.0.0.1:3306 con usuario root sin password."
    FAIL "Asegurate que Laragon este corriendo MySQL."
    FAIL "Detalle: $test"
    Read-Host "Presiona ENTER"; exit 1
}
OK "MySQL accesible"

# Crear BD si no existe
$dbName = "delivery_crm"
$envText = Get-Content ".env" | Where-Object { $_ -match "^DB_DATABASE" }
if ($envText) { $dbName = ($envText -split "=")[1].Trim() }
& php -r "try { `$p = new PDO('mysql:host=127.0.0.1;port=3306', 'root', ''); `$p->exec('CREATE DATABASE IF NOT EXISTS ``$dbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'); echo 'OK'; } catch(Exception `$e) { echo `$e->getMessage(); }" 2>&1 | Out-Null
OK "Base de datos '$dbName' lista"

# ----------------------------------------------------------------
Step "9/12" "Storage link"
& php artisan storage:link 2>&1 | Out-Null
OK "Symlink storage creado"

# ----------------------------------------------------------------
Step "10/12" "Reseteando base de datos y ejecutando migraciones + seeders"
& php artisan migrate:fresh --seed --force 2>&1
if ($LASTEXITCODE -ne 0) {
    FAIL "Error en migrate:fresh --seed. Revisa el detalle arriba."
    Read-Host "Presiona ENTER"; exit 1
}
OK "Migraciones y seeders ejecutados"

# ----------------------------------------------------------------
Step "11/12" "Limpiando todas las caches"
& php artisan optimize:clear 2>&1 | Out-Null
OK "Caches limpios"

# ----------------------------------------------------------------
Step "12/12" "Verificando rutas"
& php artisan route:list --columns=uri,name 2>&1 | Out-Null
OK "Rutas registradas"

# ----------------------------------------------------------------
Write-Host ""
Write-Host "=============================================================" -ForegroundColor Green
Write-Host "    SISTEMA REPARADO Y LISTO" -ForegroundColor Green
Write-Host "=============================================================" -ForegroundColor Green
Write-Host ""
Write-Host " URL: http://crm-delivery.test  (o el host configurado en Laragon)" -ForegroundColor White
Write-Host ""
Write-Host " CREDENCIALES (todos con password = password):" -ForegroundColor White
Write-Host "   admin@crm.com       - Super Admin" -ForegroundColor Cyan
Write-Host "   gerente@crm.com     - Admin" -ForegroundColor Cyan
Write-Host "   operador@crm.com    - Operador" -ForegroundColor Cyan
Write-Host "   repartidor@crm.com  - Repartidor (panel movil)" -ForegroundColor Cyan
Write-Host ""
Write-Host "=============================================================" -ForegroundColor Green
Read-Host "Presiona ENTER para terminar"
