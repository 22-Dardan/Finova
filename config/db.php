<?php

$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {

    $dbparts = parse_url($databaseUrl);

    $host = $dbparts['host'];
    $user = $dbparts['user'];
    $pass = $dbparts['pass'];
    $db   = ltrim($dbparts['path'], '/');
    $port = $dbparts['port'] ?? '3306';
} else {
  
    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    $db   = 'finova';
    $port = '3306';
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
