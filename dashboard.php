
<link rel="stylesheet" href="assets/css/dashboard.css?v=2">
<div class="main-container">
 <div class="dashboard-header">

    <h1>
        Përmbledhja e Panelit
    </h1>
<span id="liveStatus" class="live-badge">Live</span>
</div>


<!-- ======================
     KARTELAT
======================= -->

<div class="stats-grid">


    <div class="stat-card">
    <span class="stat-label">
        Totali i Vendeve të Punës
    </span>

    <h2 class="stat-value" id="totalJobs">
        
    </h2>
</div>



    <div class="stat-card">

        <span class="stat-label">
            Koha Totale e Punës
        </span>

        <h2 class="stat-value"  id="totalWorkTime">
            
        </h2>

    </div>



    <div class="stat-card">

        <span class="stat-label">
            Fitimet Totale
        </span>

        <h2 class="stat-value" id="totalEarnings">
            
        </h2>

    </div>




    <div class="stat-card">

        <span class="stat-label">
            Mesatarja Mujore
        </span>

        <h2 class="stat-value" id="monthlyAverage">
            
        </h2>

    </div>




    <div class="stat-card">

        <span class="stat-label">
            Paga Më e Lartë
        </span>

        <h2 class="stat-value" id="highestSalary">
            
        </h2>

    </div>




    <div class="stat-card">

        <span class="stat-label">
            Puna Aktuale
        </span>

        <h2 class="stat-value" id="currentJob">
            
        </h2>

    </div>




    <div class="stat-card">

        <span class="stat-label">
            Këtë Muaj Fitime
        </span>

        <h2 class="stat-value" id="currentMonthEarnings">
            
        </h2>

    </div>





    <div class="stat-card">

        <span class="stat-label" >
            Rritja Mujore
        </span>


        <h2 class="stat-value stat-growth" id="monthlyGrowth">

            <span>
            
            </span>

            <i class="fa-solid fa-arrow-up stat-icon"></i>

        </h2>


    </div>





    <div class="stat-card">

        <span class="stat-label">
            Fitimet Muajin e Kaluar
        </span>

        <h2 class="stat-value" id="lastMonthEarnings">
            
        </h2>

    </div>


</div>



    
    


    <!-- ======================
         BUTONAT
    ======================= -->

    <div class="action-buttons">


        <!-- REGJISTRO PUNË -->

        <button class="register-job-btn"
                 data-action="register_job"
                data-bs-toggle="modal"
                 data-bs-target="#globalModal">

            + Regjistro Punë

        </button>


        <!-- SHTO FINANCA -->

        <button class="add-finance-btn"
                 data-action="add_finance" 
                data-bs-toggle="modal"
                data-bs-target="#globalModal">

            + Shto Financa

        </button>


        <!-- EDITO PUNË -->

        <button class="edit-job-btn"
                data-action="edit_jobs"
                data-bs-toggle="modal"
                 data-bs-target="#globalModal">

            + Edito Punë

        </button>

    </div>


    <!-- ======================
         GRAFIKU
    ======================= -->
<div class="chart-container">

    <div class="chart-header">
        <h3>Rritja e Fitimeve</h3>
        <p>Progresi financiar ndër vite</p>
    </div>

    <div class="chart-toggle">
        <button onclick="loadEarningsChart('year')" class="active">Vjetor</button>
        <button onclick="loadEarningsChart('month')">Mujor</button>
    </div>

    <div class="chart-body">
        <canvas id="earningsChart"></canvas>
    </div>

</div>
</div>

<?php include 'includes/footer.php'; ?>

 <!-- ======================
         GLOBAL MODAL
    ======================= -->
<div class="modal fade" id="globalModal" tabindex="-1">

  <div class="modal-dialog">

    <div class="modal-content custom-modal">

      <div class="modal-header border-0">
        <h5 class="modal-title">Titulli</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
    


    </div>
<button id="saveBtn" class="save-btn" type="button">
        Ruaj
    </button>
  </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/charts.js"></script>