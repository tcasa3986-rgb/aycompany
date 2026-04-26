param([string]$ProyectoDir = $PSScriptRoot)
$ProyectoDir = $ProyectoDir.TrimEnd('\')
$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

function OK($t)   { Write-Host "  [OK] $t" -ForegroundColor Green }
function INFO($t) { Write-Host "  [..] $t" -ForegroundColor Yellow }
function ERR($t)  { Write-Host "  [!!] $t" -ForegroundColor Red }
function RefrescarPath { $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User") }

function BuscarMySQL {
    $regPath = "HKLM:\SOFTWARE\MySQL AB"
    if (Test-Path $regPath) {
        $v = Get-ChildItem $regPath | Select-Object -First 1
        if ($v) { $loc = (Get-ItemProperty $v.PSPath -ErrorAction SilentlyContinue).Location; if ($loc) { return Join-Path $loc "bin" } }
    }
    foreach ($r in @("C:\Program Files\MySQL\MySQL Server 8.4\bin","C:\Program Files\MySQL\MySQL Server 8.0\bin")) {
        if (Test-Path "$r\mysql.exe") { return $r }
    }
    return $null
}

Clear-Host
Write-Host ""
Write-Host "  *** MI PLATAFORMA — INSTALADOR ***" -ForegroundColor Cyan
Write-Host ""

# Node.js
RefrescarPath
if (-not (Get-Command node -ErrorAction SilentlyContinue)) {
    INFO "Instalando Node.js..."
    winget install OpenJS.NodeJS.LTS --accept-source-agreements --accept-package-agreements --silent
    RefrescarPath
}
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force -ErrorAction SilentlyContinue | Out-Null
OK "Node.js $(node --version)"

# MySQL
$mysqlBin = BuscarMySQL
if (-not $mysqlBin) {
    INFO "Instalando MySQL..."
    winget install Oracle.MySQL --accept-source-agreements --accept-package-agreements --silent
    Start-Sleep -Seconds 3
    $mysqlBin = BuscarMySQL
}

$svc = Get-Service -Name "MySQL*" -ErrorAction SilentlyContinue | Select-Object -First 1
if (-not ($svc -and $svc.Status -eq "Running")) {
    $dataDir = Join-Path (Split-Path $mysqlBin -Parent) "data"
    if (-not (Test-Path (Join-Path $dataDir "ibdata1"))) {
        INFO "Inicializando MySQL..."; & "$mysqlBin\mysqld.exe" --initialize-insecure 2>$null; Start-Sleep -Seconds 5
    }
    if (-not $svc) { & "$mysqlBin\mysqld.exe" --install MySQL84 2>$null; Start-Sleep -Seconds 2 }
    Start-Service -Name "MySQL84" -ErrorAction SilentlyContinue; Start-Sleep -Seconds 4
}
OK "MySQL corriendo"

# Base de datos
$dbExiste = & "$mysqlBin\mysql.exe" -u root --connect-timeout=5 -e "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='mi_plataforma_db';" 2>$null
if ($dbExiste -notmatch "mi_plataforma_db") {
    INFO "Creando base de datos..."
    & "$mysqlBin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS mi_plataforma_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
}
OK "Base de datos lista"

# Dependencias
INFO "Instalando dependencias del servidor..."
Push-Location "$ProyectoDir\server"; npm install --silent 2>$null; Pop-Location
INFO "Instalando dependencias del cliente..."
Push-Location "$ProyectoDir\client"; npm install --silent 2>$null; Pop-Location
OK "Dependencias instaladas"

# Acceso directo en escritorio
$escritorio = [System.Environment]::GetFolderPath("Desktop")
$shortcut   = Join-Path $escritorio "Mi Plataforma.lnk"
$wsh = New-Object -ComObject WScript.Shell
$lnk = $wsh.CreateShortcut($shortcut)
$lnk.TargetPath       = "$ProyectoDir\INICIAR_PLATAFORMA.bat"
$lnk.WorkingDirectory = $ProyectoDir
$lnk.Description      = "Iniciar Mi Plataforma"
$lnk.Save()
OK "Acceso directo creado en el escritorio"

# Arrancar
INFO "Iniciando servidor (puerto 5001)..."
Start-Process cmd -ArgumentList "/k title PLATAFORMA-Backend:5001 && cd /d `"$ProyectoDir\server`" && npm run dev"
Start-Sleep -Seconds 7

INFO "Iniciando cliente (puerto 4000)..."
Start-Process cmd -ArgumentList "/k title PLATAFORMA-Frontend:4000 && cd /d `"$ProyectoDir\client`" && npm run dev"
Start-Sleep -Seconds 12

Start-Process "http://localhost:4000"

Write-Host ""
Write-Host "  ============================================" -ForegroundColor Green
Write-Host "  PLATAFORMA INICIADA" -ForegroundColor Green
Write-Host "  ============================================" -ForegroundColor Green
Write-Host ""
Write-Host "  URL:         http://localhost:4000" -ForegroundColor Cyan
Write-Host "  Usuario:     admin@tuplataforma.com" -ForegroundColor White
Write-Host "  Contraseña:  admin123" -ForegroundColor White
Write-Host ""
Write-Host "  CAMBIA el email y contraseña en server\.env" -ForegroundColor Yellow
Write-Host ""
