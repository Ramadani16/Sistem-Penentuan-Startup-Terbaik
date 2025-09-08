<?php
session_start();
include '../config/database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ====== HANDLE POST ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $kode = $_POST['kode'];
        $nama = $_POST['nama_kriteria'];
        $bobot = $_POST['bobot'];
        $sifat = $_POST['sifat'];

        $stmt = $conn->prepare("INSERT INTO kriteria (kode, nama_kriteria, bobot, sifat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $kode, $nama, $bobot, $sifat);
        $stmt->execute();
        echo "<div class='alert alert-success'>✅ Kriteria berhasil ditambahkan!</div>";
        exit;
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $kode = $_POST['kode'];
        $nama = $_POST['nama_kriteria'];
        $bobot = $_POST['bobot'];
        $sifat = $_POST['sifat'];

        $stmt = $conn->prepare("UPDATE kriteria SET kode=?, nama_kriteria=?, bobot=?, sifat=? WHERE id=?");
        $stmt->bind_param("ssdsi", $kode, $nama, $bobot, $sifat, $id);
        $stmt->execute();
        echo "success";
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM kriteria WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "success";
        exit;
    }
}

// ====== AMBIL DATA UNTUK TAMPILAN ======
$kriteria = $conn->query("SELECT * FROM kriteria ORDER BY id ASC");
?>


<!-- Pastikan ini ada di head atau sebelum script Anda -->
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- FORM TAMBAH -->
<div class="card mb-4 shadow-sm border-0 rounded-4">
  <div class="card-header bg-primary text-white fw-bold rounded-top-4">
    <i class="bi bi-plus-circle me-2"></i>Tambah Kriteria
  </div>
  <div class="card-body">
    <form id="form-kriteria">
      <input type="hidden" name="action" value="add">
      <div class="row g-3 align-items-center">
        <div class="col-md-3">
          <input type="text" name="kode" class="form-control" placeholder="Kode" required>
        </div>
        <div class="col-md-4">
          <input type="text" name="nama_kriteria" class="form-control" placeholder="Nama Kriteria" required>
        </div>
        <div class="col-md-2">
          <input type="number" name="bobot" class="form-control" step="0.01" placeholder="Bobot (%)" required>
        </div>
        <div class="col-md-3">
          <select name="sifat" class="form-select" required>
            <option value="">-- Sifat --</option>
            <option value="benefit">Benefit</option>
            <option value="cost">Cost</option>
          </select>
        </div>
      </div>
      <button class="btn btn-success mt-3 px-4"><i class="bi bi-save me-1"></i> Simpan</button>
    </form>
    <div id="form-result" class="mt-3"></div>
  </div>
</div>

<!-- TABEL -->
<div class="card shadow-sm border-0 rounded-4">
  <div class="card-header bg-secondary text-white fw-bold rounded-top-4">
    <i class="bi bi-list-ul me-2"></i>Daftar Kriteria
  </div>
  <div class="card-body" id="table-wrapper">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>#</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Bobot</th>
            <th>Sifat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = $kriteria->fetch_assoc()): ?>
            <tr class="text-center">
              <td><?= $no++ ?></td>
              <td class="text-uppercase"><?= htmlspecialchars($row['kode']) ?></td>
              <td><?= htmlspecialchars($row['nama_kriteria']) ?></td>
              <td><span class="badge bg-info text-dark"><?= number_format($row['bobot'], 2) ?>%</span></td>
              <td>
                <span class="badge bg-<?= $row['sifat'] === 'benefit' ? 'success' : 'danger' ?>">
                  <?= ucfirst($row['sifat']) ?>
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-warning btn-edit me-1"
                  data-id="<?= $row['id'] ?>" 
                  data-kode="<?= $row['kode'] ?>"
                  data-nama="<?= $row['nama_kriteria'] ?>"
                  data-bobot="<?= $row['bobot'] ?>"
                  data-sifat="<?= $row['sifat'] ?>">
                  <i class="bi bi-pencil-square"></i>
                </button>
                
                <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $row['id'] ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="form-edit" class="modal-content rounded-4 shadow">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Kriteria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-2">
        <div class="col-6">
          <label class="form-label">Kode</label>
          <input type="text" name="kode" id="edit-kode" class="form-control" required>
        </div>
        <div class="col-6">
          <label class="form-label">Nama</label>
          <input type="text" name="nama_kriteria" id="edit-nama" class="form-control" required>
        </div>
        <div class="col-6">
          <label class="form-label">Bobot</label>
          <input type="number" name="bobot" id="edit-bobot" step="0.01" class="form-control" required>
        </div>
        <div class="col-6">
          <label class="form-label">Sifat</label>
          <select name="sifat" id="edit-sifat" class="form-select" required>
            <option value="benefit">Benefit</option>
            <option value="cost">Cost</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- SCRIPT -->
<script>
$(document).ready(function() {
  // Submit Tambah
  $('#form-kriteria').on('submit', function(e) {
    e.preventDefault();
    $.post('kriteria.php', $(this).serialize(), function(res) {
      $('#form-result').html(res);
      setTimeout(() => $('[data-page="kriteria"]').click(), 800);
    });
  });

  // Klik Edit
  $('.btn-edit').on('click', function() {
    $('#edit-id').val($(this).data('id'));
    $('#edit-kode').val($(this).data('kode'));
    $('#edit-nama').val($(this).data('nama'));
    $('#edit-bobot').val($(this).data('bobot'));
    $('#edit-sifat').val($(this).data('sifat'));
    new bootstrap.Modal(document.getElementById('editModal')).show();
  });

  // Submit Edit
  $('#form-edit').on('submit', function(e) {
    e.preventDefault();
    $.post('kriteria.php', $(this).serialize(), function(res) {
      if (res.trim() === 'success') {
        alert("✅ Berhasil diupdate");
        $('.modal').modal('hide');
        $('[data-page="kriteria"]').click();
      } else {
        alert("❌ Gagal update");
      }
    });
  });

  // Hapus
  $('.btn-delete').on('click', function() {
    if (confirm("Yakin ingin menghapus?")) {
      $.post('kriteria.php', { action: 'delete', id: $(this).data('id') }, function(res) {
        if (res.trim() === 'success') {
          $('[data-page="kriteria"]').click();
        } else {
          alert("❌ Gagal hapus");
        }
      });
    }
  });
});
</script>