<?php
include '../config/db.php';

$result = $conn->query("SELECT id, company_name, position FROM jobs WHERE status='active'");

$jobs = [];

while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode($jobs);