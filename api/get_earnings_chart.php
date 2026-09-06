<?php
require_once '../config/db.php';

$result = $conn->query("
    SELECT 
        YEAR(date) as year,
        SUM(amount) as total
    FROM finances
    GROUP BY YEAR(date)
    ORDER BY year ASC
");

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "year" => $row['year'],
        "total" => (float)$row['total']
    ];
}

echo json_encode($data);
?>