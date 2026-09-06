<?php

header('Content-Type: application/json');

include __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM jobs WHERE id = $id";
$result = $conn->query($sql);

echo json_encode($result->fetch_assoc());