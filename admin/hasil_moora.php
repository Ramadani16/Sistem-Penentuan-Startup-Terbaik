<?php
session_start();
require_once '../config/database.php';

// Ambil tanggal proses terakhir
$queryTanggal = $conn->query("SELECT MAX(tanggal_proses) AS terakhir FROM hasil_moora");
$tanggalTerakhir = $queryTanggal->fetch_assoc()['terakhir'];

// Ambil hasil MOORA terakhir
$queryHasil = $conn->query("
    SELECT hm.*, sp.nama_startup
    FROM hasil_moora hm
    JOIN startup_profiles sp ON hm.startup_id = sp.id
    WHERE hm.tanggal_proses = '$tanggalTerakhir'
    ORDER BY hm.ranking ASC
");
$hasilMOORA = $queryHasil->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil MOORA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; }
        .container { max-width: 900px; margin-top: 50px; }
        .card { border-radius: 10px; box-shadow: 0 0 12px rgba(0,0,0,0.05); }
        .card-header { background-color: #4A6572; color: white; font-size: 1.2rem; font-weight: 600; }
        .badge-success { background-color: #2ecc71; }
        .badge-danger { background-color: #e74c3c; }
        .badge-secondary { background-color: #95a5a6; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">Hasil Perhitungan MOORA</div>
        <div class="card-body">
            <p class="text-muted">Tanggal Proses: <?= $tanggalTerakhir ?? 'Belum ada proses' ?></p>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($hasilMOORA)): ?>
                <div class="alert alert-warning text-center">Belum ada hasil perhitungan.</div>
            <?php else: ?>
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ranking</th>
                            <th>Nama Startup</th>
                            <th>Nilai Akhir</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hasilMOORA as $hasil): 
                            $warna = $hasil['nilai_akhir'] > 0 ? 'success' : ($hasil['nilai_akhir'] < 0 ? 'danger' : 'secondary');
                        ?>
                        <tr>
                            <td><?= $hasil['ranking'] ?></td>
                            <td><?= htmlspecialchars($hasil['nama_startup']) ?></td>
                            <td><span class="badge bg-<?= $warna ?>"><?= number_format($hasil['nilai_akhir'], 4) ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-info btn-detail" data-id="<?= $hasil['startup_id'] ?>">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-detail').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');

        fetch(`hasil_penilaian.php?startup_id=${id}&ajax=1`)
            .then(res => res.ok ? res.json() : res.text().then(t => { throw new Error(t) }))
            .then(data => {
                if (data.status === 'ok') {
                    const q = data.kuisioner;
                    const m = data.moora;

                    let html = `<h5><strong>${data.startup.nama_startup}</strong></h5>`;
                    html += '<ul class="list-group list-group-flush">';
                    html += `<li class="list-group-item"><strong>Inovasi Produk:</strong> ${q.inovasi_produk}/10</li>`;
                    html += `<li class="list-group-item"><strong>Potensi Pasar:</strong> ${q.potensi_pasar}/10</li>`;
                    html += `<li class="list-group-item"><strong>Sustainability:</strong> ${q.sustainability}/10</li>`;
                    html += `<li class="list-group-item"><strong>Reputasi Tim:</strong> ${q.reputasi_tim}/10</li>`;
                    html += `<li class="list-group-item"><strong>Tingkat Risiko:</strong> ${q.tingkat_risiko}</li>`;
                    html += `<li class="list-group-item"><strong>Valuasi:</strong> Rp ${parseFloat(q.valuasi_perusahaan).toLocaleString('id-ID')}</li>`;
                    html += '</ul><hr>';
                    html += `<p><strong>Nilai Akhir:</strong> <span class="badge bg-primary">${parseFloat(m.nilai_akhir).toFixed(4)}</span></p>`;
                    html += `<p><strong>Ranking:</strong> <span class="badge bg-success">${m.ranking}</span></p>`;
                    html += `<p><strong>Tanggal Proses:</strong> ${m.tanggal_proses}</p>`;

                    Swal.fire({
                        title: 'Detail Penilaian Startup',
                        html: html,
                        width: 700,
                        showCloseButton: true,
                        confirmButtonText: 'Tutup'
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Data tidak ditemukan.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', err.message, 'error');
            });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
