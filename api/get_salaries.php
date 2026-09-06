<?php

include '../config/db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$year = $_GET['year'] ?? '';
$company = $_GET['company'] ?? '';

$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "
SELECT
    f.id,
    f.amount,
    f.date,
    j.company_name,
    j.company_logo
FROM finances f
JOIN jobs j ON f.job_id = j.id
WHERE 1=1
";

$countSql = "
SELECT COUNT(*) as total
FROM finances f
JOIN jobs j ON f.job_id = j.id
WHERE 1=1
";

$params = [];
$types = '';

if (!empty($year)) {
    $sql .= " AND YEAR(f.date) = ?";
    $countSql .= " AND YEAR(f.date) = ?";
    $params[] = $year;
    $types .= "i";
}

if (!empty($company)) {
    $sql .= " AND j.company_name = ?";
    $countSql .= " AND j.company_name = ?";
    $params[] = $company;
    $types .= "s";
}

$sql .= "
ORDER BY f.date DESC
LIMIT ?, ?
";

$dataParams = $params;
$dataTypes = $types . "ii";

$dataParams[] = $offset;
$dataParams[] = $limit;

/* Count query */

$countStmt = $conn->prepare($countSql);

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();

$totalRecords = $countStmt
    ->get_result()
    ->fetch_assoc()['total'];

$totalPages = ceil($totalRecords / $limit);

/* Data query */

$stmt = $conn->prepare($sql);

$stmt->bind_param($dataTypes, ...$dataParams);

$stmt->execute();

$result = $stmt->get_result();

$salaries = [];

while ($row = $result->fetch_assoc()) {
    $salaries[] = $row;
}

echo json_encode([
    "salaries" => $salaries,
    "current_page" => $page,
    "total_pages" => $totalPages
]);