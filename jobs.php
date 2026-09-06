
<?php include 'config/db.php'; ?>

<link rel="stylesheet" href="assets/css/jobs.css?v=3">

<div class="main-container">

    <div class="jobs-header">
        <h1>Punët e Regjistruara</h1>
    </div>

<?php

$result = $conn->query("
    SELECT 
        j.id,
        j.company_name,
        j.company_logo,
        j.location,
        j.status,
        j.start_date,
        f.amount
    FROM jobs j
    LEFT JOIN finances f ON f.job_id = j.id
    ORDER BY j.company_name, j.start_date ASC
");

/*
|--------------------------------------
| GROUP BY COMPANY
|--------------------------------------
*/
$companies = [];

while ($row = $result->fetch_assoc()) {

    $name = $row['company_name'];
    $amount = (float)($row['amount'] ?? 0);

    if (!isset($companies[$name])) {

        $companies[$name] = [
            'company_name' => $name,
            'company_logo' => $row['company_logo'],
            'location'     => $row['location'],

            'total_salary' => 0,
            'count'        => 0,
            'max_salary'   => 0,

            // IMPORTANT: keep latest status
            'status'       => $row['status'],
            'last_date'    => strtotime($row['start_date'])
        ];
    }

    // SALARY TOTAL
    $companies[$name]['total_salary'] += $amount;

    if ($amount > 0) {
        $companies[$name]['count']++;
    }

    if ($amount > $companies[$name]['max_salary']) {
        $companies[$name]['max_salary'] = $amount;
    }

    /*
    |--------------------------------------
    | KEEP NEWEST STATUS (IMPORTANT FIX)
    |--------------------------------------
    */
    $currentDate = strtotime($row['start_date']);

    if ($currentDate > $companies[$name]['last_date']) {
        $companies[$name]['status'] = $row['status'];
        $companies[$name]['last_date'] = $currentDate;
    }
}

?>

<div class="jobs-grid">

<?php foreach ($companies as $company):

    $avg = $company['count'] > 0
        ? $company['total_salary'] / $company['count']
        : 0;

    $logo = !empty($company['company_logo'])
        ? 'uploads/logos/' . basename($company['company_logo'])
        : 'assets/img/default-company.png';

    // STATUS FIXED
    $isActive = $company['status'] === 'active';
?>

    <div class="job-card">

        <div class="job-card-top">

            <div class="job-logo">
                <img src="<?= htmlspecialchars($logo) ?>">
            </div>

            <div class="job-main-info">
                <h3><?= htmlspecialchars($company['company_name']) ?></h3>
                <span class="location-badge">
                    <?= htmlspecialchars($company['location']) ?>
                </span>
            </div>

            <span class="job-badge <?= $isActive ? 'active-badge' : 'finished-badge' ?>">
                <?= $isActive ? 'Aktive' : 'Përfunduar' ?>
            </span>

        </div>

        <div class="job-stats">

            <div class="job-stat">
                <span>Totali i Pagave</span>
                <strong>€<?= number_format($company['total_salary'], 0) ?></strong>
            </div>

            <div class="job-stat">
                <span>Mesatarja</span>
                <strong>€<?= number_format($avg, 0) ?></strong>
            </div>

            <div class="job-stat">
                <span>Paga më e madhe</span>
                <strong>€<?= number_format($company['max_salary'], 0) ?></strong>
            </div>

        </div>

        </div>



<?php endforeach; ?>

</div>

</div>
<?php include 'includes/footer.php'; ?>
