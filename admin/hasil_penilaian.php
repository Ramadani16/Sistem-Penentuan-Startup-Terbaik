<?php
session_start();
require_once '../config/database.php';

if (isset($_GET['ajax']) && $_GET['ajax'] == '1' && isset($_GET['startup_id'])) {
    $startup_id = intval($_GET['startup_id']);

    // Ambil data startup
    $queryStartup = $conn->query("SELECT * FROM startup_profiles WHERE id = $startup_id");
    $startup = $queryStartup->fetch_assoc();

    if (!$startup) {
        echo json_encode(['status' => 'error', 'message' => 'Startup tidak ditemukan']);
        exit;
    }

    // Ambil penilaian
    $queryPenilaian = $conn->query("
        SELECT p.nilai, k.nama_kriteria, k.bobot, k.sifat 
        FROM penilaian p 
        JOIN kriteria k ON p.kriteria_id = k.id 
        WHERE p.startup_id = $startup_id
    ");
    $penilaian = $queryPenilaian->fetch_all(MYSQLI_ASSOC);

    // Ambil kuisioner
    $queryKuisioner = $conn->query("SELECT * FROM kuisioner_startup WHERE startup_id = $startup_id");
    $kuisioner = $queryKuisioner->fetch_assoc();

    // Ambil hasil MOORA
    $queryHasil = $conn->query("
        SELECT * FROM hasil_moora 
        WHERE startup_id = $startup_id 
        ORDER BY tanggal_proses DESC 
        LIMIT 1
    ");
    $hasil = $queryHasil->fetch_assoc();

    echo json_encode([
        'status' => 'ok',
        'startup' => $startup,
        'penilaian' => $penilaian,
        'kuisioner' => $kuisioner,
        'moora' => $hasil
    ]);
    exit;
}

$startup_id = isset($_GET['startup_id']) ? intval($_GET['startup_id']) : 0;

// Ambil data startup
$queryStartup = $conn->query("SELECT * FROM startup_profiles WHERE id = $startup_id");
$startup = $queryStartup->fetch_assoc();

if (!$startup) {
    $_SESSION['error'] = "Startup tidak ditemukan!";
    header("Location: ".($_SESSION['role'] == 'admin' ? 'penilaian.php' : 'dashboard.php'));
    exit;
}

// Cek akses - startup hanya bisa melihat profil sendiri
if ($_SESSION['role'] == 'startup') {
    $user_id = $_SESSION['user_id'];
    $queryCheck = $conn->query("SELECT id FROM startup_profiles WHERE id = $startup_id AND user_id = $user_id");
    if ($queryCheck->num_rows == 0) {
        header("Location: unauthorized.php");
        exit;
    }
}

// Ambil data penilaian untuk startup ini
$queryPenilaian = $conn->query("
    SELECT p.*, k.nama_kriteria, k.bobot, k.sifat 
    FROM penilaian p
    JOIN kriteria k ON p.kriteria_id = k.id
    WHERE p.startup_id = $startup_id
");
$penilaians = $queryPenilaian->fetch_all(MYSQLI_ASSOC);

// Ambil data kuisioner untuk startup ini
$queryKuisioner = $conn->query("SELECT * FROM kuisioner_startup WHERE startup_id = $startup_id");
$kuisioner = $queryKuisioner->fetch_assoc();

// Ambil hasil MOORA terakhir untuk startup ini
$queryHasil = $conn->query("
    SELECT hm.*, sp.nama_startup 
    FROM hasil_moora hm
    JOIN startup_profiles sp ON hm.startup_id = sp.id
    WHERE hm.startup_id = $startup_id
    ORDER BY hm.tanggal_proses DESC
    LIMIT 1
");
$hasilMOORA = $queryHasil->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Penilaian Startup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Hasil Penilaian Startup: <?= htmlspecialchars($startup['nama_startup']) ?></h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <h4>Profil Startup</h4>
            </div>
            <div class="card-body">
                <p><strong>Nama Startup:</strong> <?= htmlspecialchars($startup['nama_startup']) ?></p>
                <p><strong>Bidang Usaha:</strong> <?= htmlspecialchars($startup['bidang_usaha']) ?></p>
                <p><strong>Tahun Berdiri:</strong> <?= htmlspecialchars($startup['tahun_berdiri']) ?></p>
                <p><strong>Lokasi:</strong> <?= htmlspecialchars($startup['lokasi']) ?></p>
                <p><strong>Deskripsi:</strong> <?= htmlspecialchars($startup['deskripsi']) ?></p>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h4>Hasil Kuisioner</h4>
            </div>
            <div class="card-body">
                <?php if ($kuisioner): ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>Inovasi Produk</th>
                            <td><?= $kuisioner['inovasi_produk'] ?>/10</td>
                        </tr>
                        <tr>
                            <th>Potensi Pasar</th>
                            <td><?= $kuisioner['potensi_pasar'] ?>/10</td>
                        </tr>
                        <tr>
                            <th>Sustainability</th>
                            <td><?= $kuisioner['sustainability'] ?>/10</td>
                        </tr>
                        <tr>
                            <th>Reputasi Tim</th>
                            <td><?= $kuisioner['reputasi_tim'] ?>/10</td>
                        </tr>
                        <tr>
                            <th>Tingkat Risiko</th>
                            <td><?= ucfirst($kuisioner['tingkat_risiko']) ?></td>
                        </tr>
                        <tr>
                            <th>Valuasi Perusahaan</th>
                            <td><?= number_format($kuisioner['valuasi_perusahaan'], 2) ?> juta</td>
                        </tr>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning">Startup belum mengisi kuisioner.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h4>Penilaian Berdasarkan Kriteria</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Nilai</th>
                            <th>Bobot</th>
                            <th>Sifat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penilaians as $penilaian): ?>
                            <tr>
                                <td><?= htmlspecialchars($penilaian['nama_kriteria']) ?></td>
                                <td><?= $penilaian['nilai'] ?></td>
                                <td><?= $penilaian['bobot'] ?>%</td>
                                <td><?= ucfirst($penilaian['sifat']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($hasilMOORA): ?>
        <div class="card">
            <div class="card-header">
                <h4>Hasil MOORA</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Nilai Akhir</th>
                        <td><?= number_format($hasilMOORA['nilai_akhir'], 4) ?></td>
                    </tr>
                    <tr>
                        <th>Ranking</th>
                        <td><?= $hasilMOORA['ranking'] ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Proses</th>
                        <td><?= $hasilMOORA['tanggal_proses'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="penilaian.php" class="btn btn-primary">Kembali ke Form Penilaian</a>
                <a href="hasil_moora.php" class="btn btn-success">Lihat Semua Hasil MOORA</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>