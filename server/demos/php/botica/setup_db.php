<?php
$host   = getenv('DB_HOST')     ?: '127.0.0.1';
$port   = getenv('DB_PORT')     ?: '3306';
$user   = getenv('DB_USER')     ?: 'root';
$pass   = getenv('DB_PASSWORD') ?: '';
$dbName = getenv('DB_DATABASE') ?: 'demo_botica';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");

    $res = $pdo->query("SHOW TABLES");
    if ($res->rowCount() === 0) {
        echo "Importando esquema...\n";
        foreach (['database/botica_db.sql', 'database/seed_data.sql'] as $file) {
            if (!file_exists(__DIR__ . '/' . $file)) continue;
            $sql = file_get_contents(__DIR__ . '/' . $file);
            $sql = preg_replace('/^CREATE\s+DATABASE\b.*?;\s*$/im', '', $sql);
            $sql = preg_replace('/^USE\s+`?\w+`?;\s*$/im', '', $sql);
            foreach (preg_split('/;\s*(?:\r?\n|$)/m', $sql) as $stmt) {
                $s = trim($stmt);
                if ($s && !preg_match('/^(--)|(\/\*)/', $s)) {
                    try { $pdo->exec($s); } catch (PDOException $e) {}
                }
            }
        }
        echo "Base de datos lista.\n";
    } else {
        echo "Base de datos ya poblada.\n";
    }
} catch (Exception $e) {
    echo "Error BD: " . $e->getMessage() . "\n";
}
