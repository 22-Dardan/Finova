<?php
// Railway provides these cloud environment variables automatically when online
$host = getenv('MYSQLHOST') ?: '127.0.0.1';
$port = getenv('MYSQLPORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: 'finova'; // Falls back to your local db name if offline
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';

try {
    // Standard PDO connection string mapping your live cloud variables
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
