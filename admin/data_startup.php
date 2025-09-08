<?php
session_start();
include '../config/database.php';
$startup = $conn->query("SELECT sp.*, u.nama_lengkap FROM startup_profiles sp JOIN users u ON sp.user_id = u.id");



?>

<style>
  .custom-table-container {
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  }

  .custom-table {
    width: 100%;
    border-collapse: collapse;
  }

  .custom-table thead {
    background-color: #f8f9fa;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .custom-table th,
  .custom-table td {
    padding: 16px 20px;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
  }

  .custom-table thead th {
    border-bottom: 2px solid #dee2e6;
    font-size: 0.9rem;
    text-transform: uppercase;
    color: rgb(16, 17, 18);
  }

  .custom-table tbody tr:hover {
    background-color: #f1f3f5;
    transition: background-color 0.3s ease;
  }

  .custom-table tbody tr:last-child td {
    border-bottom: none;
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

  .action-btn {
    border: none;
    background: none;
    padding: 0 6px;
    color: #495057;
    font-size: 1.1rem;
    transition: color 0.2s ease;
  }

  .action-btn:hover {
    color: #0d6efd;
  }

  .action-icons {
    white-space: nowrap;
  }

</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="fade-in">
  <h3 class="mb-4 fw-bold"><i class="bi bi-layers me-2"></i>Data Start-Up</h3>

  <div class="custom-table-container shadow-sm rounded-4 overflow-hidden">
    <table class="table table-borderless custom-table mb-0">
      <thead>
        <tr>
          <th>Nama Pengguna</th>
          <th>Nama Start-Up</th>
          <th>Deskripsi</th>
          <th>Bidang Usaha</th>
          <th>Tahun</th>
          <th>Lokasi</th>
          <th>Aksi</th>
        </tr>
      </thead>
     <tbody>
  <?php while ($row = $startup->fetch_assoc()) { ?>
    <tr>
      <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
      <td><?php echo htmlspecialchars($row['nama_startup']); ?></td>
      <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
      <td><?php echo htmlspecialchars($row['bidang_usaha']); ?></td>
      <td><?php echo htmlspecialchars($row['tahun_berdiri']); ?></td>
      <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
      <td class="action-icons d-flex gap-1">
        <!-- Tombol Lihat -->
        <button type="button" onclick="showStartupDetail(<?php echo $row['id']; ?>)" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
          <i class="bi bi-eye"></i>
        </button>

        <!-- Form Edit -->
        <button class="btn btn-sm btn-outline-warning btn-edit-startup" 
                data-id="<?= $row['id']; ?>" 
                title="Edit Data">
          <i class="bi bi-pencil-square"></i>
        </button>


           <form method="POST" action="delete_startup.php" class="d-inline">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button class="action-btn btn-delete" data-id="<?= $row['id'] ?>" title="Hapus Data">
                  <i class="bi bi-trash text-danger"></i>
              </button>

          </form>
      </td>
    </tr>
  <?php } ?>
</tbody>

    </table>
  </div>
</div>

<script>
$(document).on('click', '.btn-edit-startup', function () {
  const id = $(this).data('id');
  $('#content-area').load('edit_startup.php?id=' + id);
});
</script>

<script>
function showStartupDetail(id) {
  // Tampilkan loading terlebih dahulu
  Swal.fire({
    title: 'Memuat data...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Ambil data via AJAX
fetch('detail.php?id=' + id)
    .then(response => response.json())
    .then(data => {
      Swal.fire({
        title: `<div class="text-primary fw-bold">${data.nama_startup}</div>`,
        html: `
          <div class="text-start">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <div class="detail-item">
                  <i class="bi bi-briefcase me-2"></i>
                  <span><strong>Bidang:</strong> ${data.bidang_usaha}</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="detail-item">
                  <i class="bi bi-geo-alt me-2"></i>
                  <span><strong>Lokasi:</strong> ${data.lokasi}</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="detail-item">
                  <i class="bi bi-calendar me-2"></i>
                  <span><strong>Tahun Berdiri:</strong> ${data.tahun_berdiri}</span>
                </div>
              </div>
            </div>
            
            <div class="description-box mt-3">
              <h6 class="text-secondary"><i class="bi bi-card-text me-2"></i> Deskripsi:</h6>
              <p class="mt-2">${data.deskripsi}</p>
            </div>
          </div>
        `,
        width: '700px',
        padding: '20px',
        background: '#ffffff',
        showConfirmButton: false,
        showCloseButton: true,
        closeButtonHtml: '<i class="bi bi-x-lg"></i>',
        customClass: {
          popup: 'rounded-3 shadow',
          closeButton: 'btn-close-custom'
        }
      });
    })
    .catch(error => {
      Swal.fire({
        icon: 'error',
        title: 'Gagal memuat data',
        text: 'Terjadi kesalahan saat mengambil data detail startup'
      });
    });
}
</script>





<style>
/* Tambahkan style untuk tombol hapus */
.action-btn .bi-trash {
  transition: transform 0.2s;
}
.action-btn:hover .bi-trash {
  transform: scale(1.1);
}
</style>
<!-- Update tombol edit di tabel -->

<style>
  /* Animasi dasar */
.animated-popup {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
/* Detail Item Style */
.detail-item {
  padding: 10px 15px;
  border-radius: 8px;
  background-color: #f8f9fa;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
}

/* Description Box */
.description-box {
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 8px;
  border-left: 3px solid #0d6efd;
}

/* Custom Close Button */
.btn-close-custom {
  font-size: 1.25rem;
  color: #6c757d;
  transition: all 0.2s;
}
.btn-close-custom:hover {
  color: #dc3545;
}

/* Text Primary Color */
.text-primary {
  color: #0d6efd;
}
</style>
