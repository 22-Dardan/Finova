<?php

header('Content-Type: application/json');

include __DIR__ . '/../config/db.php';

$sql = "SELECT id, company_name, position, location, start_date,end_date,company_logo, status FROM jobs ORDER BY id DESC";
$result = $conn->query($sql);

$jobs = [];

while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode($jobs);