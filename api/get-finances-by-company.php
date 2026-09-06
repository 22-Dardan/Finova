<?php


header('Content-Type: application/json');

include '../config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT 
    finances.id,
    finances.amount,
    finances.date,
    jobs.position,
    jobs.location,
    jobs.company_logo
FROM finances
JOIN jobs ON jobs.id = finances.job_id
WHERE jobs.id = $id
ORDER BY finances.date DESC
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);