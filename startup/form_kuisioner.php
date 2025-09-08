<?php
session_start();
include '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    die("<div class='alert alert-danger'>Sesi habis. Silakan login ulang.</div>");
}

// Ambil data startup user yang login
$user_id = $_SESSION['user_id'];
$startup = $conn->query("SELECT id, nama_startup FROM startup_profiles WHERE user_id = $user_id")->fetch_assoc();

if (!$startup) {
    die("<div class='alert alert-warning'>Anda belum mengisi profil startup.</div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Isi Kuisioner Startup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Form Kuisioner Startup</h2>

  <form action="process_kuisioner.php" method="POST" class="mt-4">
    <input type="hidden" name="startup_id" value="<?= $startup['id'] ?>">

    <div class="mb-3">
      <label class="form-label">Inovasi Produk (1-10)</label>
      <input type="number" name="inovasi_produk" class="form-control" min="1" max="10" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Potensi Pasar (1-10)</label>
      <input type="number" name="potensi_pasar" class="form-control" min="1" max="10" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Sustainability (1-10)</label>
      <input type="number" name="sustainability" class="form-control" min="1" max="10" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Reputasi Tim (1-10)</label>
      <input type="number" name="reputasi_tim" class="form-control" min="1" max="10" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Tingkat Risiko</label>
      <select name="tingkat_risiko" class="form-select" required>
        <option value="">-- Pilih --</option>
        <option value="Rendah">Rendah</option>
        <option value="Sedang">Sedang</option>
        <option value="Tinggi">Tinggi</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Valuasi Perusahaan (Rp)</label>
      <input type="number" name="valuasi_perusahaan" class="form-control" min="0" required>
    </div>

    <button type="submit" class="btn btn-primary" onclick="this.disabled=true;this.form.submit();">Kirim Kuisioner</button>
  </form>
</div>
</body>

</html>
