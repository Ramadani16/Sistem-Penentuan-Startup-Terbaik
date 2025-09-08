<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>Sesi berakhir. Silakan login ulang.</div>";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID tidak valid.</div>";
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM startup_profiles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Data tidak ditemukan.</div>";
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// Cek submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $nama_startup = $_POST['nama_startup'];
    $deskripsi = $_POST['deskripsi'];
    $bidang_usaha = $_POST['bidang_usaha'];
    $tahun_berdiri = $_POST['tahun_berdiri'];
    $lokasi = $_POST['lokasi'];

    $update = $conn->prepare("UPDATE startup_profiles SET nama_startup = ?, deskripsi = ?, bidang_usaha = ?, tahun_berdiri = ?, lokasi = ? WHERE id = ?");
    $update->bind_param("sssssi", $nama_startup, $deskripsi, $bidang_usaha, $tahun_berdiri, $lokasi, $id);

    if ($update->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal: ' . $conn->error
        ]);
    }
    exit;
}

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<div class="container my-4 px-4">
  <h3 class="mb-4 fw-semibold">Edit Profil Start-Up</h3>

  <form method="post" id="form-edit-startup">

    <div class="form-floating mb-3">
      <input type="text" name="nama_startup" class="form-control" id="nama_startup" placeholder="Nama Start-Up" required value="<?= htmlspecialchars($data['nama_startup']) ?>">
      <label for="nama_startup">Nama Start-Up</label>
    </div>

    <div class="form-floating mb-3">
      <textarea name="deskripsi" class="form-control" id="deskripsi" placeholder="Deskripsi" style="height: 120px" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
      <label for="deskripsi">Deskripsi</label>
    </div>

    <div class="form-floating mb-3">
      <input type="text" name="bidang_usaha" class="form-control" id="bidang_usaha" placeholder="Bidang Usaha" required value="<?= htmlspecialchars($data['bidang_usaha']) ?>">
      <label for="bidang_usaha">Bidang Usaha</label>
    </div>

    <div class="form-floating mb-3">
      <input type="number" name="tahun_berdiri" class="form-control" id="tahun_berdiri" placeholder="Tahun Berdiri" required value="<?= htmlspecialchars($data['tahun_berdiri']) ?>">
      <label for="tahun_berdiri">Tahun Berdiri</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" name="lokasi" class="form-control" id="lokasi" placeholder="Lokasi" required value="<?= htmlspecialchars($data['lokasi']) ?>">
      <label for="lokasi">Lokasi</label>
    </div>

    <button type="submit" class="btn btn-primary px-4 py-2">
    <i class="bi bi-save me-2"></i> Simpan
    </button>
  </form>
</div>


<style>
  body {
    background-color: #f8f9fa;
  }
  .form-control, .form-floating > label {
    font-size: 0.95rem;
  }
</style>

<script>
  $('#form-edit-startup').submit(function (e) {
    e.preventDefault();
    $.post('edit_startup.php?id=<?= $id ?>', $(this).serialize(), function (response) {
      if (response.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: response.message,
          showConfirmButton: false,
          timer: 1500
        }).then(() => {
          loadPage('data_startup'); // Ganti sesuai halaman tujuanmu
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: response.message
        });
      }
    }, 'json');
  });
</script>


