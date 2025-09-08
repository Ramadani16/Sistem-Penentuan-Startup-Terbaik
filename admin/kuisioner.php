<?php
include '../config/database.php';

// Build the base query with proper joins
$sql = "
SELECT
    k.*,
    s.nama_startup,
    s.bidang_usaha,
    s.tahun_berdiri,
    u.nama_lengkap as pemilik_startup
FROM kuisioner_startup k
JOIN startup_profiles s ON k.startup_id = s.id
JOIN users u ON s.user_id = u.id
";

$where = [];
$params = [];
$types = ''; // Inisialisasi string tipe untuk bind_param

// Perubahan: Mengganti filter dengan fitur pencarian
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $search_term = '%' . $_GET['search_query'] . '%'; // Tambahkan wildcard untuk LIKE
    $where[] = "(s.nama_startup LIKE ? OR s.bidang_usaha LIKE ? OR u.nama_lengkap LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss"; // Tiga string parameter untuk tiga kondisi LIKE
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY k.tanggal_pengisian DESC";

try {
    $stmt = $conn->prepare($sql);
    if (!empty($params)) { // Perbaikan: Cek apakah ada parameter sebelum binding
        // Bind parameter secara dinamis
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kuisioner Startup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Light Gray Background */
            color: #343a40; /* Dark Gray Text */
        }
        .container-fluid { /* Menggunakan container-fluid untuk lebar penuh */
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); /* Soft Shadow */
        }
        .card-header {
            background-color: #343a40; /* Dark Gray Header */
            color: #ffffff; /* White Text */
            border-bottom: none;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.25rem;
        }
        h4 {
            font-weight: 700;
            color: #212529; /* Darker title */
            margin-bottom: 20px;
        }
        .bg-light {
            background-color: #e9ecef !important; /* Lighter gray for filter/search area */
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
        }
        .btn-primary {
            background-color: #007bff; /* Bootstrap primary blue */
            border-color: #007bff;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-secondary {
            background-color: #6c757d; /* Bootstrap secondary gray */
            border-color: #6c757d;
            font-weight: 600;
            border-radius: 8px;
        }
        .table {
            border-radius: 8px;
            overflow: hidden; /* Memastikan sudut bulat diterapkan pada konten tabel */
            border: 1px solid #dee2e6; /* Border ringan untuk tabel */
            width: 100%; /* Memastikan tabel mengambil lebar penuh kontainernya */
            table-layout: fixed; /* Menggunakan fixed layout untuk kontrol lebar kolom yang lebih baik */
        }
        .table th, .table td {
            padding: 8px 6px; /* Padding dikurangi lebih lanjut untuk kekompakan */
            vertical-align: middle;
            border-color: #e9ecef; /* Border lebih terang untuk sel */
            font-size: 0.8rem; /* Ukuran font lebih kecil untuk konten tabel */
            word-wrap: break-word; /* Memastikan teks panjang pecah baris */
        }
        .table thead th {
            background-color: #e9ecef; /* Header tabel abu-abu terang */
            color: #495057; /* Teks lebih gelap untuk header */
            font-weight: 600;
            text-align: center;
        }
        .table tbody tr:hover {
            background-color: #f1f3f5; /* Efek hover halus */
        }
        .text-center {
            text-align: center;
        }
        .badge {
            font-weight: 600;
            padding: 0.3em 0.6em; /* Padding lebih kecil untuk badge */
            border-radius: 0.375rem;
        }
        /* Penyesuaian lebar kolom spesifik */
        .table th:nth-child(1), .table td:nth-child(1) { /* No */
            width: 4%;
        }
        .table th:nth-child(2), .table td:nth-child(2) { /* Startup */
            width: 15%;
        }
        .table th:nth-child(3), .table td:nth-child(3) { /* Bidang */
            width: 12%;
        }
        .table th:nth-child(4), .table td:nth-child(4) { /* Tahun */
            width: 7%;
        }
        .table th:nth-child(5), .table td:nth-child(5) { /* Pemilik */
            width: 12%;
        }
        .table th:nth-child(6), .table td:nth-child(6), /* Inovasi */
        .table th:nth-child(7), .table td:nth-child(7), /* Pasar */
        .table th:nth-child(8), .table td:nth-child(8), /* Sustain. */
        .table th:nth-child(9), .table td:nth-child(9) { /* Tim */
            width: 7%; /* Lebih kecil */
        }
        .table th:nth-child(10), .table td:nth-child(10) { /* Risiko */
            width: 7%;
        }
        .table th:nth-child(11), .table td:nth-child(11) { /* Valuasi */
            width: 10%;
        }
        .table th:nth-child(12), .table td:nth-child(12) { /* Tanggal */
            width: 10%;
        }
        .table th:nth-child(13), .table td:nth-child(13) { /* Aksi */
            width: 6%;
        }

        /* Penyesuaian responsif untuk tabel */
        @media (max-width: 992px) { /* Untuk layar desktop kecil/tablet besar */
            .table th, .table td {
                font-size: 0.75rem;
                padding: 6px 5px;
            }
            /* Sembunyikan kolom "Tahun" dan "Tim" */
            .table th:nth-child(4), .table td:nth-child(4), /* Tahun */
            .table th:nth-child(9), .table td:nth-child(9) { /* Tim */
                display: none;
            }
            /* Sesuaikan lebar kolom yang tersisa */
            .table th:nth-child(1), .table td:nth-child(1) { width: 5%; } /* No */
            .table th:nth-child(2), .table td:nth-child(2) { width: 18%; } /* Startup */
            .table th:nth-child(3), .table td:nth-child(3) { width: 15%; } /* Bidang */
            .table th:nth-child(5), .table td:nth-child(5) { width: 18%; } /* Pemilik */
            .table th:nth-child(6), .table td:nth-child(6) { width: 8%; } /* Inovasi */
            .table th:nth-child(7), .table td:nth-child(7) { width: 8%; } /* Pasar */
            .table th:nth-child(8), .table td:nth-child(8) { width: 8%; } /* Sustain. */
            .table th:nth-child(10), .table td:nth-child(10) { width: 8%; } /* Risiko */
            .table th:nth-child(11), .table td:nth-child(11) { width: 12%; } /* Valuasi */
            .table th:nth-child(12), .table td:nth-child(12) { width: 12%; } /* Tanggal */
            .table th:nth-child(13), .table td:nth-child(13) { width: 8%; } /* Aksi */
        }

        @media (max-width: 768px) { /* Untuk layar tablet kecil/ponsel besar */
            .table th, .table td {
                font-size: 0.7rem;
                padding: 4px 5px;
            }
            /* Sembunyikan lebih banyak kolom */
            .table th:nth-child(3), .table td:nth-child(3), /* Bidang */
            .table th:nth-child(5), .table td:nth-child(5), /* Pemilik */
            .table th:nth-child(6), .table td:nth-child(6), /* Inovasi */
            .table th:nth-child(7), .table td:nth-child(7) { /* Pasar */
                display: none;
            }
            /* Sesuaikan lebar kolom yang tersisa */
            .table th:nth-child(1), .table td:nth-child(1) { width: 7%; } /* No */
            .table th:nth-child(2), .table td:nth-child(2) { width: 25%; } /* Startup */
            .table th:nth-child(8), .table td:nth-child(8) { width: 15%; } /* Sustain. */
            .table th:nth-child(10), .table td:nth-child(10) { width: 15%; } /* Risiko */
            .table th:nth-child(11), .table td:nth-child(11) { width: 18%; } /* Valuasi */
            .table th:nth-child(12), .table td:nth-child(12) { width: 18%; } /* Tanggal */
            .table th:nth-child(13), .table td:nth-child(13) { width: 10%; } /* Aksi */
        }

        @media (max-width: 576px) { /* Untuk layar ponsel */
            .table th, .table td {
                font-size: 0.65rem;
                padding: 3px 4px;
            }
            /* Sembunyikan lebih banyak kolom lagi */
            .table th:nth-child(8), .table td:nth-child(8), /* Sustain. */
            .table th:nth-child(10), .table td:nth-child(10), /* Risiko */
            .table th:nth-child(11), .table td:nth-child(11) { /* Valuasi */
                display: none;
            }
            /* Sesuaikan lebar kolom yang tersisa */
            .table th:nth-child(1), .table td:nth-child(1) { width: 10%; } /* No */
            .table th:nth-child(2), .table td:nth-child(2) { width: 40%; } /* Startup */
            .table th:nth-child(12), .table td:nth-child(12) { width: 30%; } /* Tanggal */
            .table th:nth-child(13), .table td:nth-child(13) { width: 20%; } /* Aksi */
        }
    </style>
</head>
<body>
<div class="container-fluid"> <!-- Diubah menjadi container-fluid untuk lebar yang lebih luas -->
    <div class="card mb-4 shadow-sm border-0 rounded-4">
        <?php if (isset($_GET['hapus']) && $_GET['hapus'] == 'berhasil'): ?>
            <div class="alert alert-success text-center">Data berhasil dihapus.</div>
        <?php endif; ?>

        <h4 class="fw-bold mb-3"><i class="bi bi-clipboard-data me-2"></i>Data Kuisioner Startup</h4>

        <div class="bg-light p-3 rounded mb-3">
            <form method="get" class="row g-2">
                <div class="col-md-8">
                    <label for="search_query" class="form-label">Cari:</label>
                    <input type="text" name="search_query" id="search_query" class="form-control"
                           placeholder="Cari nama startup, bidang, atau pemilik..."
                           value="<?= htmlspecialchars($_GET['search_query'] ?? '') ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search me-1"></i>Cari</button>
                    <a href="?" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive"> <!-- Tetap pertahankan table-responsive untuk mencegah overflow di layar sangat kecil -->
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Startup</th>
                        <th>Bidang</th>
                        <th class="d-none d-lg-table-cell">Tahun</th> <!-- Disembunyikan di layar lg dan di bawahnya -->
                        <th>Pemilik</th>
                        <th class="d-none d-md-table-cell">Inovasi</th> <!-- Disembunyikan di layar md dan di bawahnya -->
                        <th class="d-none d-md-table-cell">Pasar</th> <!-- Disembunyikan di layar md dan di bawahnya -->
                        <th>Sustain.</th> <!-- Disikat untuk menghemat ruang -->
                        <th class="d-none d-lg-table-cell">Tim</th> <!-- Disembunyikan di layar lg dan di bawahnya -->
                        <th>Risiko</th>
                        <th>Valuasi</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): $no = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_startup']) ?></td>
                                <td><?= htmlspecialchars($row['bidang_usaha']) ?></td>
                                <td class="text-center d-none d-lg-table-cell"><?= $row['tahun_berdiri'] ?></td>
                                <td><?= htmlspecialchars($row['pemilik_startup']) ?></td>
                                <td class="text-center d-none d-md-table-cell"><?= $row['inovasi_produk'] ?></td>
                                <td class="text-center d-none d-md-table-cell"><?= $row['potensi_pasar'] ?></td>
                                <td class="text-center"><?= $row['sustainability'] ?></td>
                                <td class="text-center d-none d-lg-table-cell"><?= $row['reputasi_tim'] ?></td>
                                <td class="text-center">
                                    <?php
                                    $risk = strtolower($row['tingkat_risiko']);
                                    $color = ['rendah' => 'success', 'sedang' => 'warning', 'tinggi' => 'danger'][$risk] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>"><?= ucfirst($risk) ?></span>
                                </td>
                                <td>Rp <?= number_format($row['valuasi_perusahaan'], 2, ',', '.') ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['tanggal_pengisian'])) ?></td>
                                <td class="text-center">
                                    <form method="POST" action="hapus_kuisioner.php" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-hapus" data-id="<?= $row['id'] ?>" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="13" class="text-center text-muted">Tidak ada data ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CSS & Icons -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
<script>
document.querySelectorAll('.btn-hapus').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data ini tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('hapus_kuisioner.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire('Berhasil!', 'Data berhasil dihapus.', 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat menghapus.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Gagal', 'Tidak dapat menghubungi server.', 'error');
                });
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
