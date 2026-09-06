<?php

include '../config/db.php';

$result = $conn->query("
    SELECT
        finances.id,
        finances.amount,
        finances.date,
        jobs.company_name,
        jobs.company_logo
    FROM finances
    LEFT JOIN jobs ON jobs.id = finances.job_id
    ORDER BY finances.date DESC
");

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);