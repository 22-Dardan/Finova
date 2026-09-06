<?php
// config.php - Finova Database Connection

$host = "localhost";
$dbname = "finova";
$username = "root";
$password = "root";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set UTF-8 encoding (important for special characters)
$conn->set_charset("utf8mb4");
?>