<?php
session_start();
include '../config/database.php';

// Cek session admin
if (!isset($_SESSION['user_id'])) {
  echo "<div class='alert alert-danger'>Sesi habis. Silakan login ulang.</div>";
  exit;
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
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
    font-size: 0.85rem;
    text-transform: uppercase;
    color: #333;
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

  .badge-role {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 12px;
  }

  .badge-admin {
    background-color: #198754;
    color: #fff;
  }

  .badge-startup {
    background-color: #0d6efd;
    color: #fff;
  }

  .badge-status-pending {
    background-color: #ffc107;
    color: #000;
  }

  .badge-status-verified {
    background-color: #198754;
    color: #fff;
  }
  .status-badge:hover {
  opacity: 0.8;
  transform: scale(1.05);
  transition: all 0.2s ease;
}
</style>

<div class="fade-in">
  <h3 class="mb-4 fw-bold"><i class="bi bi-people me-2"></i>Data Users</h3>

  <div class="custom-table-container shadow-sm rounded-4 overflow-hidden">
    <table class="table table-borderless custom-table mb-0">
      <thead>
        <tr>
          <th>Nama Lengkap</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Terdaftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $users->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
            <td>
              <span class="badge-role <?= $row['role'] === 'admin' ? 'badge-admin' : 'badge-startup'; ?>">
                <?= ucfirst($row['role']); ?>
              </span>
            </td>
            <td>
  <span class="badge-role badge-status-<?= $row['status'] ?> status-badge" 
        data-user-id="<?= $row['id'] ?>"
        onclick="toggleStatus(<?= $row['id'] ?>, '<?= $row['status'] ?>')"
        style="cursor: pointer;">
    <?= ucfirst($row['status']) ?>
  </span>
</td>
            <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
            <td class="action-icons">
              <button 
  class="action-btn btn-detail-user" 
  data-id="<?= $row['id']; ?>" 
  title="Lihat Detail"
>
  <i class="bi bi-eye text-primary"></i>
</button>

               <button 
    class="action-btn btn-edit-user" 
    title="Edit Pengguna"
    data-id="<?= $row['id']; ?>"
    data-nama="<?= htmlspecialchars($row['nama_lengkap']); ?>"
    data-email="<?= htmlspecialchars($row['email']); ?>"
    data-role="<?= $row['role']; ?>"
    data-status="<?= $row['status']; ?>"
  >
    <i class="bi bi-pencil-square text-warning"></i>
  </button>
              <button onclick="hapusUser(<?= $row['id']; ?>)" class="action-btn" title="Hapus Pengguna">
                <i class="bi bi-trash text-danger"></i>
              </button>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <!-- Tombol Tambah -->


  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  /**
   * Mengubah status pengguna (verified <-> pending)
   */
  function toggleStatus(userId, currentStatus) {
    const newStatus = currentStatus === 'verified' ? 'pending' : 'verified';
    const badgeElement = document.querySelector(`.status-badge[data-user-id="${userId}"]`);

    // Tampilkan loading spinner
    badgeElement.innerHTML = '<i class="bi bi-arrow-repeat"></i>';

    fetch('update_status.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `user_id=${userId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Perbarui tampilan badge
        badgeElement.className = `badge-role badge-status-${newStatus} status-badge`;
        badgeElement.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
        badgeElement.setAttribute('onclick', `toggleStatus(${userId}, '${newStatus}')`);
      } else {
        alert('Gagal memperbarui status');
        badgeElement.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      badgeElement.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
    });
  }

  /**
   * Menghapus pengguna
   */
  function hapusUser(userId) {
    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: "Data pengguna akan dihapus permanen!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('hapusUser.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire('Terhapus!', 'Data pengguna berhasil dihapus.', 'success')
              .then(() => location.reload());
          } else {
            Swal.fire('Gagal', 'Tidak dapat menghapus pengguna.', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
        });
      }
    });
  }

  /**
   * Event listener tombol edit pengguna
   */
  document.querySelectorAll('.btn-edit-user').forEach(button => {
    button.addEventListener('click', function () {
      const id = this.dataset.id;
      const nama = this.dataset.nama;
      const email = this.dataset.email;
      const role = this.dataset.role;
      const status = this.dataset.status;

      Swal.fire({
        title: 'Edit Users',
        html: `
          <input id="edit-nama" class="swal2-input" placeholder="Nama Lengkap" value="${nama}">
          <input id="edit-email" type="email" class="swal2-input" placeholder="Email" value="${email}">
          <select id="edit-role" class="swal2-input">
            <option value="admin" ${role === 'admin' ? 'selected' : ''}>Admin</option>
            <option value="startup" ${role === 'startup' ? 'selected' : ''}>Startup</option>
          </select>
          <select id="edit-status" class="swal2-input">
            <option value="verified" ${status === 'verified' ? 'selected' : ''}>Verified</option>
            <option value="pending" ${status === 'pending' ? 'selected' : ''}>Pending</option>
          </select>
        `,
        confirmButtonText: 'Simpan',
        showCancelButton: true,
        focusConfirm: false,
        preConfirm: () => {
          const nama = document.getElementById('edit-nama').value.trim();
          const email = document.getElementById('edit-email').value.trim();
          const role = document.getElementById('edit-role').value;
          const status = document.getElementById('edit-status').value;

          if (!nama || !email) {
            Swal.showValidationMessage('Nama dan Email wajib diisi');
          }

          return { id, nama, email, role, status };
        }
      }).then(result => {
        if (result.isConfirmed) {
          const data = result.value;

          fetch('updateUser.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${data.id}&nama_lengkap=${encodeURIComponent(data.nama)}&email=${encodeURIComponent(data.email)}&role=${data.role}&status=${data.status}`
          })
          .then(res => res.json())
          .then(response => {
            if (response.success) {
              Swal.fire('Tersimpan!', 'Data pengguna berhasil diperbarui.', 'success')
                .then(() => location.reload());
            } else {
              Swal.fire('Gagal', response.message || 'Gagal memperbarui.', 'error');
            }
          })
          .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
          });
        }
      });
    });
  });

  /**
   * Event listener tombol detail pengguna
   */
  document.querySelectorAll('.btn-detail-user').forEach(button => {
    button.addEventListener('click', function () {
      const userId = this.dataset.id;

      Swal.fire({
        title: 'Memuat...',
        didOpen: () => {
          Swal.showLoading();
        },
        allowOutsideClick: false
      });

      fetch(`detailUser.php?id=${userId}`)
        .then(res => res.json())
        .then(response => {
          if (response.success) {
            const user = response.user;
            Swal.fire({
              title: 'Detail Users',
              html: `
                <div style="text-align: left">
                  <p><strong>Nama:</strong> ${user.nama_lengkap}</p>
                  <p><strong>Username:</strong> ${user.username}</p>
                  <p><strong>Email:</strong> ${user.email || '-'}</p>
                  <p><strong>Role:</strong> ${user.role}</p>
                  <p><strong>Status:</strong> ${user.status}</p>
                  <p><strong>Terdaftar:</strong> ${user.created_at}</p>
                </div>
              `,
              icon: 'info'
            });
          } else {
            Swal.fire('Gagal', response.message, 'error');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('Error', 'Gagal mengambil data.', 'error');
        });
    });
  });
</script>

