<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$year = $_GET['year'] ?? '';
$company = $_GET['company'] ?? '';

$titleCompany = !empty($company) ? $company : "Të gjitha kompanitë";
$titleYear = !empty($year) ? $year : "Të gjitha vitet";

$pageTitle = "Raporti i Pagave - Finova";
$pageSubtitle = "$titleCompany • $titleYear";

$where = [];

if (!empty($year)) {
    $where[] = "YEAR(f.date) = '$year'";
}

if (!empty($company)) {
    $company = $conn->real_escape_string($company);
    $where[] = "j.company_name = '$company'";
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$result = $conn->query("
    SELECT
        j.company_name,
        f.amount,
        f.date
    FROM finances f
    INNER JOIN jobs j ON j.id = f.job_id
    $whereSql
    ORDER BY f.date DESC
");

$months = [
    1 => "Janar", 2 => "Shkurt", 3 => "Mars", 4 => "Prill",
    5 => "Maj", 6 => "Qershor", 7 => "Korrik", 8 => "Gusht",
    9 => "Shtator", 10 => "Tetor", 11 => "Nëntor", 12 => "Dhjetor"
];

$rows = '';
$total = 0;
$count = 0;
$highest = 0;

while ($row = $result->fetch_assoc()) {

    $amount = (float)$row['amount'];

    $total += $amount;
    $count++;

    if ($amount > $highest) $highest = $amount;

    $month = $months[(int)date('n', strtotime($row['date']))];
    $yearText = date('Y', strtotime($row['date']));

    $rows .= "
        <tr>
            <td class='cell-company'>{$row['company_name']}</td>
            <td class='cell-date'>{$month} {$yearText}</td>
            <td class='money'>€" . number_format($amount, 2) . "</td>
        </tr>
    ";
}

$avg = $count > 0 ? $total / $count : 0;

$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
    @page {
        margin: 50px 45px;
    }
    
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 14px;
        color: #1F1C2C;
        background: #FFFFFF;
        line-height: 1.4;
    }

    /* PREMIUM MINIMALIST HEADER STYLING */
    .header {
        border-bottom: 2px solid #1F1C2C;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .title {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #1F1C2C;
    }

    .subtitle {
        margin-top: 8px;
        font-size: 13px;
        color: #615C75;
        letter-spacing: 0.5px;
    }

    /* TOP SUMMARY METRICS SUBPANEL */
    .stats-container {
        width: 100%;
        margin-bottom: 35px;
    }
    
    .stats-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .stats-table td {
        width: 25%;
        padding: 0 10px 0 0;
        vertical-align: top;
    }
    
    .stats-table td:last-child {
        padding-right: 0;
    }

    .stat-box {
        border-top: 3px solid #7C3AED; /* Accent violet baseline rule indicator */
        padding-top: 10px;
    }

    .stat-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #837E9C;
        margin-bottom: 4px;
    }

    .value {
        font-weight: 700;
        color: #1F1C2C;
        font-size: 18px;
    }
    
    .value.total-gain {
        color: #7C3AED; /* Deep rich signature violet */
    }

    /* EDITORIAL HORIZONTAL RULE SHEET TABLE */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th {
        color: #1F1C2C;
        padding: 12px 10px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid #1F1C2C;
        text-align: left;
    }

    td {
        padding: 16px 10px;
        font-size: 14px;
        border-bottom: 1px solid #EAE8F2; /* Delicate hairline separator rule */
    }

    .cell-company {
        font-weight: 600;
        color: #1F1C2C;
    }

    .cell-date {
        color: #615C75;
    }

    .money {
        font-weight: 700;
        color: #7C3AED;
        font-size: 15px;
        text-align: right;
    }
    
    th.align-right {
        text-align: right;
    }

    /* PRINT CLOSURE FOOTER */
    .footer {
        margin-top: 60px;
        border-top: 1px solid #EAE8F2;
        padding-top: 15px;
        text-align: center;
        font-size: 11px;
        color: #9D99B3;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }
</style>
</head>

<body>

<!-- 1. HEADER SECTION -->
<div class='header'>
    <table style='width: 100%; border-collapse: collapse;'>
        <tr>
            <td><div class='title'>$pageTitle</div></td>
            <td style='text-align: right; font-size: 12px; color: #615C75; letter-spacing: 0.5px;'>
                Gjeneruar më: " . date('d.m.Y - H:i') . "
            </td>
        </tr>
    </table>
    <div class='subtitle'><strong>Filtrimi:</strong> $pageSubtitle</div>
</div>

<!-- 2. EXECUTIVE METRICS BAR (TOP MOUNTED) -->
<div class='stats-container'>
    <table class='stats-table'>
        <tr>
            <td>
                <div class='stat-box'>
                    <div class='stat-label'>Totali i Pagave</div>
                    <div class='value total-gain'>€" . number_format($total,2) . "</div>
                </div>
            </td>
            <td>
                <div class='stat-box'>
                    <div class='stat-label'>Mesatarja</div>
                    <div class='value'>€" . number_format($avg,2) . "</div>
                </div>
            </td>
            <td>
                <div class='stat-box'>
                    <div class='stat-label'>Paga më e Madhe</div>
                    <div class='value'>€" . number_format($highest,2) . "</div>
                </div>
            </td>
            <td>
                <div class='stat-box'>
                    <div class='stat-label'>Numri i Pagesave</div>
                    <div class='value'>$count</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- 3. DATA LIST SPREADSHEET (OPEN LINE LAYOUT) -->
<table class='data-table'>
<thead>
    <tr>
        <th>Kompania</th>
        <th>Periudha</th>
        <th class='align-right'>Shuma e Paguar</th>
    </tr>
</thead>
<tbody>
    $rows
</tbody>
</table>

<!-- 4. SYSTEM ASSURED FOOTER -->
<div class='footer'>
    Dokument zyrtar i gjeneruar nga Sistemi Financiar Finova.
</div>

</body>
</html>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Pagat-Finova.pdf', ['Attachment' => false]);
?>
