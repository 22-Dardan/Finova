<?php include 'includes/header.php'; ?>
<?php include 'config/db.php'; ?>

<link rel="stylesheet" href="assets/css/yearly.css">

<div class="main-container">

    <div class="yearly-header">
        <h1>Raporti Vjetor</h1>
        <p>Përmbledhje e fitimeve sipas viteve.</p>
    </div>

<?php

$result = $conn->query("
   SELECT
        YEAR(f.date) AS year,
        MONTH(f.date) AS month,
        SUM(f.amount) AS total,
        GROUP_CONCAT(DISTINCT j.company_name SEPARATOR ', ') AS companies
    FROM finances f
    INNER JOIN jobs j ON j.id = f.job_id
    GROUP BY YEAR(f.date), MONTH(f.date)
    ORDER BY year DESC, month ASC
");

$years = [];

while ($row = $result->fetch_assoc()) {

    $year = $row['year'];
    $month = $row['month'];

    if (!isset($years[$year])) {
        $years[$year] = [
            'total' => 0,
            'months' => []
        ];
    }

    $years[$year]['months'][$month] = [
        'total' => $row['total'],
        'companies' => $row['companies']
    ];

    $years[$year]['total'] += $row['total'];
}


/* BEST YEAR */
$bestYear = null;
$bestYearAmount = 0;

foreach ($years as $y => $data) {
    if ($data['total'] > $bestYearAmount) {
        $bestYearAmount = $data['total'];
        $bestYear = $y;
    }
}

$months = [
    1 => 'Janar', 2 => 'Shkurt', 3 => 'Mars', 4 => 'Prill',
    5 => 'Maj', 6 => 'Qershor', 7 => 'Korrik', 8 => 'Gusht',
    9 => 'Shtator', 10 => 'Tetor', 11 => 'Nëntor', 12 => 'Dhjetor'
];

?>

<div class="years-container">

    <button class="year-arrow left" onclick="changeYear(-1)">‹</button>
    <button class="year-arrow right" onclick="changeYear(1)">›</button>

    <div class="years-slider">

        <?php foreach ($years as $year => $data): ?>

        <?php $isBestYear = ($year == $bestYear); ?>

        <div class="year-slide <?= $isBestYear ? 'best-year-slide' : '' ?>">

            <div class="year-card">

                <div class="year-header">

                    <h2>
                        <?= $year ?>

                        <?php if ($isBestYear): ?>
                            <span class="top-badge">Viti më Fitimprurës</span>
                        <?php endif; ?>
                    </h2>

                    <span class="year-total">
                        €<?= number_format($data['total'], 0) ?>
                    </span>

                </div>

                <div class="months-grid">

                    <?php
                    $yearBestMonth = 0;
                    foreach ($data['months'] as $m) {
                        if ($m['total'] > $yearBestMonth) {
                            $yearBestMonth = $m['total'];
                        }
                    }
                    ?>

                    <?php foreach ($months as $number => $monthName): ?>

                    <?php
                        $monthData = $data['months'][$number] ?? null;
                        $amount = $monthData['total'] ?? 0;
                        $hasData = $amount > 0;

                        $isBestMonth = ($amount > 0 && $amount == $yearBestMonth);
                    ?>

                    <div class="month-card <?= $isBestMonth ? 'best-month-card' : '' ?>">

                        <span><?= $monthName ?></span>

                        <strong class="<?= $isBestMonth ? 'best-month' : '' ?>">
                            €<?= number_format($amount, 0) ?>
                        </strong>

                        <?php if ($hasData): ?>
                            <div class="company-badges">
                                <?php
                                $companies = explode(',', $monthData['companies']);
                                foreach ($companies as $company):
                                ?>
                                    <span class="company-badge">
                                        <?= htmlspecialchars(trim($company)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                           <div class="company-badges">
    <span class="no-data-badge">Nuk ka të dhëna</span>
</div>
                        <?php endif; ?>

                    </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>
</div>
<?php include 'includes/footer.php'; ?>
