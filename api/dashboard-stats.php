<?php

require_once '../config/db.php';

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| 1. TOTAL JOBS
|--------------------------------------------------------------------------
*/
$result = $conn->query("
    SELECT COUNT(DISTINCT company_name) AS total
    FROM jobs
");

$totalJobs = (int)$result->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 2. CURRENT JOB
|--------------------------------------------------------------------------
*/
$result = $conn->query("
    SELECT company_name, position
    FROM jobs
    WHERE status = 'active'
    LIMIT 1
");

$current = $result->fetch_assoc();
$currentJob = $current
    ? $current['company_name'] . " • " . $current['position']
    : "-";

/*
|--------------------------------------------------------------------------
| 3. TOTAL EARNINGS
|--------------------------------------------------------------------------
*/
$result = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM finances");
$totalEarnings = (float)$result->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 4. HIGHEST SALARY
|--------------------------------------------------------------------------
*/
$result = $conn->query("SELECT COALESCE(MAX(amount),0) AS max FROM finances");
$highestSalary = (float)$result->fetch_assoc()['max'];



$result = $conn->query("
    SELECT MAX(DATE_FORMAT(date, '%Y-%m')) AS latest_month
    FROM finances
");

$latestMonth = $result->fetch_assoc()['latest_month'];




$result = $conn->query("
    SELECT MAX(DATE_FORMAT(date, '%Y-%m')) AS previous_month
    FROM finances
    WHERE DATE_FORMAT(date, '%Y-%m') < '$latestMonth'
");

$previousMonth = $result->fetch_assoc()['previous_month'];
/*
|--------------------------------------------------------------------------
| 5. THIS MONTH (SAFE)
|--------------------------------------------------------------------------
*/
$result = $conn->query("
    SELECT COALESCE(SUM(amount),0) AS total
    FROM finances
    WHERE DATE_FORMAT(date, '%Y-%m') = '$latestMonth'
");

$currentMonth = (float)$result->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 6. LAST MONTH (SAFE)
|--------------------------------------------------------------------------
*/
$result = $conn->query("
    SELECT COALESCE(SUM(amount),0) AS total
    FROM finances
    WHERE DATE_FORMAT(date, '%Y-%m') = '$previousMonth'
");

$lastMonth = (float)$result->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 7. MONTHLY GROWTH
|--------------------------------------------------------------------------
*/
if ($lastMonth > 0) {
    $growth = (($currentMonth - $lastMonth) / $lastMonth) * 100;
} else {
    $growth = 0;
}

/*
|--------------------------------------------------------------------------
| 8. MONTHLY AVERAGE
|--------------------------------------------------------------------------
*/
$result = $conn->query("
    SELECT AVG(month_total) AS avg_month
    FROM (
        SELECT SUM(amount) AS month_total
        FROM finances
        GROUP BY YEAR(date), MONTH(date)
    ) t
");

$monthlyAverage = (float)($result->fetch_assoc()['avg_month'] ?? 0);

/*
|--------------------------------------------------------------------------
| 9. TOTAL WORK TIME
|--------------------------------------------------------------------------
*/
$periods = [];

$result = $conn->query("
    SELECT
        start_date,
        COALESCE(end_date, CURDATE()) AS end_date
    FROM jobs
    WHERE start_date IS NOT NULL
    ORDER BY start_date
");

while ($row = $result->fetch_assoc()) {
    $periods[] = [
        'start' => strtotime($row['start_date']),
        'end'   => strtotime($row['end_date'])
    ];
}

$merged = [];

foreach ($periods as $period) {

    if (empty($merged)) {
        $merged[] = $period;
        continue;
    }

    $lastIndex = count($merged) - 1;

    if ($period['start'] <= $merged[$lastIndex]['end']) {

        $merged[$lastIndex]['end'] = max(
            $merged[$lastIndex]['end'],
            $period['end']
        );

    } else {

        $merged[] = $period;
    }
}

$totalDays = 0;

foreach ($merged as $period) {
    $totalDays += ($period['end'] - $period['start']) / 86400;
}

$totalMonths = floor($totalDays / 30.44);

$years = floor($totalMonths / 12);
$months = $totalMonths % 12;

$totalWorkTime = "{$years} Vjet {$months} Muaj";

/*
|--------------------------------------------------------------------------
| RESPONSE (NO FORMATTING HERE!)
|--------------------------------------------------------------------------
*/
echo json_encode([
    "totalJobs" => $totalJobs,
    "currentJob" => $currentJob,

    "totalEarnings" => $totalEarnings,
    "highestSalary" => $highestSalary,

    "currentMonthEarnings" => $currentMonth,
    "lastMonthEarnings" => $lastMonth,
    "monthlyGrowth" => round($growth, 1),

    "monthlyAverage" => $monthlyAverage,
    "totalWorkTime" => $totalWorkTime
]);