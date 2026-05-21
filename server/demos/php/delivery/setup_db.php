<?php
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'railway';
$user = getenv('DB_USERNAME') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

$maxTries = 15;
for ($i = 1; $i <= $maxTries; $i++) {
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        break;
    } catch (Exception $e) {
        if ($i === $maxTries) { echo "DB no disponible: " . $e->getMessage() . "\n"; exit(1); }
        echo "Esperando DB ($i/$maxTries)...\n";
        sleep(2);
    }
}

// Check if already seeded
try {
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count > 0) {
        echo "Base de datos ya poblada ($count usuarios), omitiendo import.\n";
        exit(0);
    }
} catch (Exception $e) {
    // Table doesn't exist yet — proceed with import
}

$sqlFile = __DIR__ . '/bk_basededatos.sql';
if (!file_exists($sqlFile)) {
    echo "Sin dump SQL, usando solo migraciones.\n";
    exit(0);
}

echo "Importando dump SQL...\n";
$sql = file_get_contents($sqlFile);
$sql = preg_replace('/^CREATE DATABASE\b.*?;\s*/im', '', $sql);
$sql = preg_replace('/^USE\s+`?\w+`?;\s*/im', '', $sql);

$statements = preg_split('/;\s*(\r?\n|$)/', $sql);
$ok = 0;
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '' || str_starts_with($stmt, '--')) continue;
    try { $pdo->exec($stmt); $ok++; } catch (Exception $e) { /* skip duplicates/errors */ }
}
echo "Import completado: $ok sentencias ejecutadas.\n";
