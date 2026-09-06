<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';

$company = $_GET['company'] ?? '';

$stmt = $conn->prepare("
    SELECT
        j.company_name,
        j.company_logo,
        j.position,
        f.amount,
        f.date
    FROM finances f
    INNER JOIN jobs j ON j.id = f.job_id
    WHERE j.company_name = ?
    ORDER BY f.date DESC
");

if (!$stmt) {
    die($conn->error);
}

$stmt->bind_param("s", $company);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p>Nuk ka paga.</p>";
    exit;
}

$row = $result->fetch_assoc();

$logo = !empty($row['company_logo'])
    ? '../uploads/logos/' . basename($row['company_logo'])
    : '../assets/img/default-company.png';

?>

<div class="salary-company-header">

    <img src="<?= htmlspecialchars($logo) ?>" class="salary-company-logo">

    <div>
        <h2><?= htmlspecialchars($row['company_name']) ?></h2>
    </div>

</div>

<div class="salary-list">

<?php

$result->data_seek(0);

$months = [
    1=>'Janar',
    2=>'Shkurt',
    3=>'Mars',
    4=>'Prill',
    5=>'Maj',
    6=>'Qershor',
    7=>'Korrik',
    8=>'Gusht',
    9=>'Shtator',
    10=>'Tetor',
    11=>'Nëntor',
    12=>'Dhjetor'
];

while ($salary = $result->fetch_assoc()):

    $date = strtotime($salary['date']);

    $month = $months[(int)date('n', $date)];
    $year  = date('Y', $date);
?>

<div class="salary-item">

    <div>
        <strong><?= htmlspecialchars($salary['position']) ?></strong>
        <br>
        <span><?= $month ?> <?= $year ?></span>
    </div>

    <div>
        <strong>€<?= number_format($salary['amount'], 0) ?></strong>
    </div>

</div>

<?php endwhile; ?>

</div>