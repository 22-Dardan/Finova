<?php
include '../config/db.php';

$years = [];
$companies = [];

/* years */
$result = $conn->query("
    SELECT DISTINCT YEAR(date) AS year
    FROM finances
    ORDER BY year DESC
");

while ($row = $result->fetch_assoc()) {
    $years[] = $row['year'];
}

/* companies grouped by name */
$result = $conn->query("
    SELECT DISTINCT company_name
    FROM jobs
    ORDER BY company_name ASC
");

while ($row = $result->fetch_assoc()) {
    $companies[] = $row['company_name'];
}

echo json_encode([
    "years" => $years,
    "companies" => $companies
]);