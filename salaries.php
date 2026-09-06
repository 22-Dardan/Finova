<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);



?>
<?php include 'config/db.php'; ?>


<link rel="stylesheet" href="assets/css/salaries.css">

<div class="main-container">

    <div class="salary-header">
        <h1>Pagat</h1>
        <p>Historiku i të gjitha pagave.</p>
    </div>
 

    <div class="salary-filters">

        <select id="yearFilter" class="filter-select">
            <option value="">Të gjitha vitet</option>
        </select>

        <select id="companyFilter" class="filter-select">
            <option value="">Të gjitha kompanitë</option>
        </select>

     <button class="export-btn" onclick="exportPDF()">
    Shkarko PDF
</button>
 
    </div>

    <div class="salary-table-wrapper">

        <table class="salary-table">

            <thead>
                <tr>
                    <th>Kompania</th>
                    <th>Data</th>
                    <th>Shuma</th>
                </tr>
            </thead>

            <tbody id="salaryTableBody">

            </tbody>

        </table>

    </div>

    <div class="salary-pagination">

        <button id="prevPage" class="page-btn">
            ←
        </button>

        <span id="pageInfo"></span>

        <button id="nextPage" class="page-btn">
            →
        </button>

    </div>

</div>




<?php include 'includes/footer.php'; ?>