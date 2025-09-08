<?php
include '../config/database.php';

session_start();



if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Start-Up</title>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --sidebar-bg: #1b263b;
      --sidebar-active: #415a77;
      --primary-blue: #1e3a8a;
      --white: #ffffff;
      --light-bg: #f8f9fa;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--white);
      margin: 0;
      padding: 0;
      color: #333;
    }
    
    /* Sidebar Styles */
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      background-color: var(--sidebar-bg);
      color: var(--white);
      padding: 1.5rem 0;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      transition: all 0.3s ease;
    }
    
    .sidebar-header {
      text-align: center;
      padding: 0 1.5rem 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-header h4 {
      font-weight: 600;
      margin: 0;
      color: var(--white);
    }
    
    .nav-links {
      padding: 1rem 0;
    }
    
    .nav-link {
      display: flex;
      align-items: center;
      padding: 0.75rem 1.5rem;
      color: var(--white);
      text-decoration: none;
      transition: all 0.2s;
      margin: 0.25rem 1rem;
      border-radius: 6px;
    }
    
    .nav-link i {
      margin-right: 10px;
      font-size: 1.1rem;
    }
    
    .nav-link:hover,
    .nav-link.active {
      background-color: var(--sidebar-active);
      color: var(--white);
    }
    
    /* Main Content Styles */
    .main-content {
      margin-left: 250px;
      padding: 2rem;
      min-height: 100vh;
      background-color: var(--white);
      transition: all 0.3s ease;
    }
    
    .welcome-card {
      background-color: var(--primary-blue);
      color: var(--white);
      border-radius: 12px;
      padding: 2.5rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      text-align: center;
    }
    
    .welcome-card h1 {
      font-weight: 700;
      font-size: 2.25rem;
      margin-bottom: 1rem;
    }
    
    .welcome-card p {
      font-size: 1.1rem;
      opacity: 0.9;
      max-width: 700px;
      margin: 0 auto;
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
      .sidebar {
        width: 220px;
      }
      .main-content {
        margin-left: 220px;
      }
    }
    
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }
      .main-content {
        margin-left: 0;
        padding: 1.5rem;
      }
      .welcome-card {
        padding: 1.5rem;
      }
      .welcome-card h1 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body>
  <!-- Sidebar Navigation -->
  <div class="sidebar">
    <div class="sidebar-header">
      <h4>Start-Up Panel</h4>
    </div>
    <div class="nav-links">
      <a href="#" class="nav-link active" data-page="form_profil.php">
        <i class="bi bi-person-circle"></i> Isi Profil
      </a>
      <a href="#" class="nav-link" data-page="form_kuisioner.php?id=1">
        <i class="bi bi-list-check"></i> Kuisioner
      </a>
      <a href="#" class="nav-link" data-page="status.php">
        <i class="bi bi-bar-chart-line"></i> Status Evaluasi
      </a>
      <a href="../auth/logout.php" class="nav-link">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="main-content">
    <div class="welcome-card">
      <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
      <p>Silakan pilih menu di sebelah kiri untuk mengisi data start-up Anda.</p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Fungsi untuk menandai link aktif dan load konten
    document.querySelectorAll('.nav-link[data-page]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Update active state
        document.querySelectorAll('.nav-link').forEach(item => {
          item.classList.remove('active');
        });
        this.classList.add('active');
        
        // Load content
        const page = this.getAttribute('data-page');
        fetch(page)
          .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
          })
          .then(html => {
            document.querySelector('.main-content').innerHTML = html;
          })
          .catch(error => {
            console.error('Error loading page:', error);
            document.querySelector('.main-content').innerHTML = `
              <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> Gagal memuat halaman. Silakan coba lagi.
              </div>
            `;
          });
      });
    });
  </script>

  <?php if (isset($success_message)): ?>
  <script>
    Swal.fire({
      title: 'Berhasil!',
      text: '<?= addslashes($success_message) ?>',
      icon: 'success',
      confirmButtonColor: '#1e3a8a',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>

  <?php if (isset($error_message)): ?>
  <script>
    Swal.fire({
      title: 'Gagal!',
      text: '<?= addslashes($error_message) ?>',
      icon: 'error',
      confirmButtonColor: '#dc3545',
      confirmButtonText: 'Mengerti'
    });
  </script>
  <?php endif; ?>
</body>
</html>