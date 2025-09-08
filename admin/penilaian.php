<?php
session_start();
require_once '../config/database.php';

// Ambil data startup yang sudah mengisi kuisioner dan sudah verified
$queryStartup = $conn->query("
    SELECT sp.id, sp.nama_startup 
    FROM startup_profiles sp
    JOIN users u ON sp.user_id = u.id
    JOIN kuisioner_startup ks ON sp.id = ks.startup_id
    WHERE u.status = 'verified'
");
$startups = $queryStartup->fetch_all(MYSQLI_ASSOC);

// Cek apakah sudah ada penilaian untuk startup tertentu
$existingPenilaian = [];
$queryExisting = $conn->query("SELECT startup_id FROM penilaian GROUP BY startup_id");
while ($row = $queryExisting->fetch_assoc()) {
    $existingPenilaian[] = $row['startup_id'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Penilaian Startup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f2f6;
        }
        .card-custom {
            background-color:rgb(93, 96, 103);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .card-custom label, .card-custom small {
            color: #dcdde1;
        }
        .form-select, .btn {
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom p-4">
                <h3 class="mb-4 text-center">Form Penilaian Startup</h3>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form action="moora_calculation.php" method="post">
                    <div class="mb-3">
                        <label for="startup_id" class="form-label">Pilih Startup</label>
                        <select class="form-select" id="startup_id" name="startup_id" required>
                            <option value="">-- Pilih Startup --</option>
                            <?php foreach ($startups as $startup): ?>
                                <?php $disabled = in_array($startup['id'], $existingPenilaian) ? 'disabled' : ''; ?>
                                <option value="<?= $startup['id'] ?>" <?= $disabled ?>>
                                    <?= $startup['nama_startup'] ?>
                                    <?= in_array($startup['id'], $existingPenilaian) ? '(Sudah Dinilai)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Hanya menampilkan startup yang sudah mengisi kuisioner dan terverifikasi</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Hitung dengan MOORA</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
