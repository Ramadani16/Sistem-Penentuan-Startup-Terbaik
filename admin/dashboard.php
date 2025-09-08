<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  echo "<div class='alert alert-danger'>Sesi habis. Silakan login ulang.</div>";
  
  exit;
}
include '../config/database.php';

// Ambil total dari database
$total_startup = $conn->query("SELECT COUNT(*) AS total FROM startup_profiles")->fetch_assoc()['total'];
$total_kriteria = $conn->query("SELECT COUNT(*) AS total FROM kriteria")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];

?>



<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <style>
    body {
      background-color: #f8f9fa;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .sidebar {
      height: 100vh;
      width: 240px;
      background-color: rgb(28, 30, 50);
      padding: 20px 15px;
      position: fixed;
      left: 0;
      top: 0;
      overflow-y: auto;
    }

    .sidebar a {
      color: #ddd;
      padding: 12px 20px;
      display: flex;
      align-items: center;
      text-decoration: none;
      border-radius: 8px;
      margin-bottom: 6px;
      transition: all 0.3s ease;
      font-size: 15px;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #4b6584;
      color: #fff;
      font-weight: 500;
    }

    .sidebar i {
      margin-right: 10px;
      font-size: 15px;
    }

    .sidebar .badge {
      font-size: 0.75rem;
      background-color: #198754;
      padding: 4px 8px;
      border-radius: 12px;
      margin-left: auto;
    }

    #content-area {
      margin-left: 260px;
      padding: 30px;
    }

    .fade-in {
      animation: fadeInUp 0.8s ease forwards;
      opacity: 0;
      transform: translateY(20px);
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<div class="d-flex">
  <!-- Sidebar -->
  <div class="sidebar p-3">
    <h5 class="text-white mb-4 text-center">
      <i class="bi bi-person-gear me-2"></i>Admin Panel
    </h5>

    <a href="#" data-page="home" class="active">
      <span><i class="bi bi-house-door me-2"></i>Dashboard</span>
    </a>

    <a href="#" data-page="data_startup">
      <span><i class="bi bi-lightbulb me-2"></i>Data Start-Up</span>
      <span class="badge bg-primary"><?= $total_startup; ?></span>
    </a>

    <a href="#" data-page="kriteria">
      <span><i class="bi bi-ui-checks-grid me-2"></i>Data Kriteria</span>
      <span class="badge bg-success"><?= $total_kriteria; ?></span>
    </a>

    <a href="#" data-page="kuisioner">
      <span><i class="bi bi-card-checklist me-2"></i>Kuisioner</span>
    </a>

    <a href="#" data-page="penilaian">
      <span><i class="bi bi-pencil-square me-2"></i>Kelola Penilaian</span>
    </a>

    <a href="#" data-page="hasil_moora">
      <span><i class="bi bi-diagram-3 me-2"></i>Hasil MOORA</span>
    </a>

    <a href="#" data-page="users">
      <span><i class="bi bi-people-fill me-2"></i>Users</span>
      <span class="badge bg-warning text-dark"><?= $total_users; ?></span>
    </a>

    <a href="../auth/logout.php" class="text-danger">
      <span><i class="bi bi-box-arrow-right me-2"></i>Logout</span>
    </a>
  </div>


  <!-- Main Content -->
  <div class="flex-grow-1 p-4" id="content-area">
    <!-- Konten akan dimuat di sini via AJAX -->
  </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function loadPage(pageName) {
  $('#content-area').html('<div class="text-center my-4">Memuat...</div>');
  $.get(pageName + '.php', function(data) {
    $('#content-area').html(data);
  }).fail(function() {
    $('#content-area').html('<div class="alert alert-danger">Gagal memuat halaman.</div>');
  });
}
</script>

<script>
  // Load default content
  $(document).ready(function () {
    loadPage('home');

    $('.sidebar a[data-page]').click(function (e) {
      e.preventDefault();
      let page = $(this).data('page');

      $('.sidebar a').removeClass('active');
      $(this).addClass('active');

      loadPage(page);
    });

    function loadPage(page) {
      $("#content-area").load(page + ".php");
    }
  });
</script>

<?php if (isset($_SESSION['flash_message'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <script>
    $(document).ready(function () {
        Swal.fire({
            icon: '<?= $_SESSION['flash_message']['type'] === 'success' ? 'success' : ($_SESSION['flash_message']['type'] === 'danger' ? 'error' : 'warning') ?>',
            title: '<?= $_SESSION['flash_message']['type'] === 'success' ? 'Berhasil!' : 'Oops!' ?>',
            text: '<?= $_SESSION['flash_message']['message'] ?>',
            confirmButtonText: 'Close',
            confirmButtonColor: '#198754',
            customClass: {
                popup: 'swal2-rounded',
            },
            
            timer: 7000,
            timerProgressBar: true
        });
    });
  </script>

  <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>



</body>
</html>
