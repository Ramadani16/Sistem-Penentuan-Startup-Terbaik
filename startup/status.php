<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>Sesi habis. Silakan login ulang.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT sp.*, hm.nilai_akhir, hm.ranking 
                       FROM startup_profiles sp
                       LEFT JOIN hasil_moora hm ON sp.id = hm.startup_id
                       WHERE sp.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
?>

<style>
  .card-profile {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    background-color: #fff;
  }

  .profile-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #224abe 100%);
    color: white;
    padding: 2rem;
    position: relative;
  }

  .stat-card {
    border-radius: 12px;
    background: #f8f9fa;
    padding: 1.5rem;
    text-align: center;
  }

  .value-display {
    font-size: 2.3rem;
    font-weight: bold;
  }

  .detail-item {
    border-bottom: 1px solid #eee;
    padding: 0.75rem 0;
  }

  .detail-item:last-child {
    border-bottom: none;
  }

  canvas {
    margin-top: 1rem;
  }
</style>

<div class="card card-profile mb-4">
  <div class="profile-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h2 class="fw-bold mb-1">Status Evaluasi Start-Up</h2>
        <p class="mb-0"><?= date('d F Y') ?></p>
      </div>
    </div>
  </div>

  <div class="card-body p-4">
    <?php if ($row && isset($row['nilai_akhir'])): ?>
      <div class="row mb-4">
        <div class="col-lg-6 mb-3">
          <h3 class="fw-semibold"><?= htmlspecialchars($row['nama_startup']) ?></h3>
          <p class="text-muted"><?= htmlspecialchars($row['bidang_usaha']) ?></p>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="stat-card">
            <div class="text-muted mb-2">Nilai Akhir</div>
            <div class="value-display text-success" id="nilaiAkhir"><?= number_format($row['nilai_akhir'], 4) ?></div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          <div class="stat-card">
            <div class="text-muted mb-2">Peringkat</div>
            <div class="value-display text-warning">#<?= $row['ranking'] ?></div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="fw-bold"><i class="bi bi-info-circle-fill text-primary me-2"></i>Detail Start-Up</h5>
              <div class="detail-item">
                <small class="text-muted">Deskripsi</small>
                <p class="mb-0"><?= htmlspecialchars($row['deskripsi']) ?></p>
              </div>
              <div class="detail-item">
                <small class="text-muted">Tahun Berdiri</small>
                <p class="mb-0"><?= htmlspecialchars($row['tahun_berdiri']) ?></p>
              </div>
              <div class="detail-item">
                <small class="text-muted">Lokasi</small>
                <p class="mb-0"><?= htmlspecialchars($row['lokasi']) ?></p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="fw-bold"><i class="bi bi-graph-up-arrow text-success me-2"></i>Analisis Hasil</h5>
              <div class="alert alert-<?= $row['ranking'] <= 3 ? 'success' : 'warning' ?>">
                <i class="bi bi-<?= $row['ranking'] <= 3 ? 'trophy' : 'lightbulb' ?>-fill me-2"></i>
                <?php if($row['ranking'] == 1): ?>
                  Selamat! Start-Up Anda meraih peringkat pertama!
                <?php elseif($row['ranking'] <= 3): ?>
                  Start-Up Anda termasuk dalam top 3! Pertahankan!
                <?php else: ?>
                  Start-Up Anda berada di peringkat <?= $row['ranking'] ?>. Masih bisa ditingkatkan!
                <?php endif; ?>
              </div>
              <canvas id="performanceChart" height="200"></canvas>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Belum ada hasil evaluasi untuk start-up Anda.
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const nilaiElement = document.getElementById('nilaiAkhir');
  if (nilaiElement) {
    const target = parseFloat(nilaiElement.innerText);
    let current = 0;
    const duration = 1500;
    const increment = target / (duration / 16);

    const animate = () => {
      current += increment;
      if (current >= target) {
        nilaiElement.innerText = target.toFixed(4);
      } else {
        nilaiElement.innerText = current.toFixed(4);
        requestAnimationFrame(animate);
      }
    };
    animate();
  }

  <?php if ($row && isset($row['nilai_akhir'])): ?>
  const ctx = document.getElementById('performanceChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Kesesuaian Kriteria', 'Potensi Pengembangan', 'Kinerja Tim'],
      datasets: [{
        data: [60, 25, 15],
        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
        hoverBorderColor: "rgba(234, 236, 244, 1)",
      }]
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        },
        tooltip: {
          backgroundColor: "rgb(255,255,255)",
          bodyColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          padding: 15
        }
      },
      cutout: '70%'
    }
  });
  <?php endif; ?>
</script>
