<?php
include '../config/database.php';

$total_startup = $conn->query("SELECT COUNT(*) AS total FROM startup_profiles")->fetch_assoc()['total'];
$total_kriteria = $conn->query("SELECT COUNT(*) AS total FROM kriteria")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="fade-in">
  <!-- Header Box -->
  <div class="p-4 rounded-4 mb-4 text-white" style="background: linear-gradient(135deg,rgb(24, 85, 176),rgb(216, 201, 241)); box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
    <h3 class="mb-1 fw-bold"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</h3>
    <p class="mb-0 text-white-50">Pantau statistik dan kelola sistem pendukung keputusan MOORA</p>
  </div>

  <!-- Stat Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
          <div class="me-3 text-primary fs-2"><i class="bi bi-people-fill"></i></div>
          <div>
            <div class="text-muted">Total Start-Up</div>
            <div class="fs-4 fw-bold text-primary"><?= $total_startup; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
          <div class="me-3 text-success fs-2"><i class="bi bi-funnel-fill"></i></div>
          <div>
            <div class="text-muted">Total Kriteria</div>
            <div class="fs-4 fw-bold text-success"><?= $total_kriteria; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
          <div class="me-3 text-warning fs-2"><i class="bi bi-person-lines-fill"></i></div>
          <div>
            <div class="text-muted">Total Users</div>
            <div class="fs-4 fw-bold text-warning"><?= $total_users; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik -->
  <div class="card shadow-sm border-0 p-4 fade-in">
    <h5 class="mb-3"><i class="bi bi-bar-chart-fill me-2"></i>Statistik Visual</h5>
    <canvas id="dashboardChart" height="100"></canvas>
  </div>
</div>

<script>
  const ctx = document.getElementById('dashboardChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Start-Up', 'Kriteria', 'Users'],
      datasets: [{
        label: 'Jumlah',
        data: [<?= $total_startup; ?>, <?= $total_kriteria; ?>, <?= $total_users; ?>],
        backgroundColor: ['#0d6efd', '#198754', '#ffc107'],
        borderRadius: 10
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
</script>
