<?php
require_once '../config/db.php';

header('Content-Type: application/json');

/*
========================================
MONTHLY EARNINGS (REAL DATA)
========================================
*/

$result = $conn->query("
    SELECT 
        DATE_FORMAT(f.date, '%Y-%m') as month,
        SUM(f.amount) as total
    FROM finances f
    GROUP BY DATE_FORMAT(f.date, '%Y-%m')
    ORDER BY month ASC
");

$months = [
    '01' => 'Janar',
    '02' => 'Shkurt',
    '03' => 'Mars',
    '04' => 'Prill',
    '05' => 'Maj',
    '06' => 'Qershor',
    '07' => 'Korrik',
    '08' => 'Gusht',
    '09' => 'Shtator',
    '10' => 'Tetor',
    '11' => 'Nëntor',
    '12' => 'Dhjetor'
];

$data = [];

while ($row = $result->fetch_assoc()) {

    $parts = explode('-', $row['month']);
    $year = $parts[0];
    $monthNum = $parts[1];

    $label = $months[$monthNum] . ' ' . $year;

    $data[] = [
        "year" => $label,
        "total" => (float)$row['total']
    ];
}

echo json_encode($data);