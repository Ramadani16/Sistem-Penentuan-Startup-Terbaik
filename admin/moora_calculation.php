<?php
session_start();
require_once '../config/database.php';

// Ambil semua data kuisioner startup
$queryKuisioner = $conn->query("SELECT * FROM kuisioner_startup");
$dataKuisioner = [];
$startupIds = [];

while ($row = $queryKuisioner->fetch_assoc()) {
    $startupIds[] = $row['startup_id'];
    $dataKuisioner[$row['startup_id']] = $row;
}

// Ambil semua kriteria
$queryKriteria = $conn->query("SELECT * FROM kriteria");
$kriteria = [];

// Simpan berdasarkan kode untuk mapping
while ($k = $queryKriteria->fetch_assoc()) {
    $kriteria[] = $k;
}

// Manual mapping kolom kuisioner berdasarkan kode kriteria
$mappingKolom = [
    'C1' => 'inovasi_produk',
    'C2' => 'potensi_pasar',
    'C3' => 'sustainability',
    'C4' => 'reputasi_tim',
    'C5' => 'tingkat_risiko',
    'C6' => 'valuasi_perusahaan',
];

// Siapkan matriks penilaian
$matrix = [];

foreach ($dataKuisioner as $sid => $data) {
    foreach ($kriteria as $k) {
        $kode = $k['kode'];
        $kolom = $mappingKolom[$kode] ?? '';
        $nilai = 0;

        if (!isset($data[$kolom])) {
            $nilai = 0;
        } elseif ($kolom === 'tingkat_risiko') {
            // Konversi tingkat risiko ke angka (semakin tinggi nilainya, semakin rendah risikonya)
            $nilai = match($data[$kolom]) {
                'rendah' => 3,
                'sedang' => 2,
                'tinggi' => 1,
                default => 0,
            };
        } elseif ($kolom === 'valuasi_perusahaan') {
            $nilai = (float) $data[$kolom];
        } else {
            $nilai = (float) $data[$kolom];
        }

        $matrix[$sid][$k['id']] = $nilai;
    }
}

// Normalisasi matrix
$normal = [];
foreach ($kriteria as $k) {
    $idk = $k['id'];
    $totalKuadrat = 0;

    foreach ($startupIds as $sid) {
        $totalKuadrat += pow($matrix[$sid][$idk] ?? 0, 2);
    }

    $akar = sqrt($totalKuadrat) ?: 1;

    foreach ($startupIds as $sid) {
        $normal[$sid][$idk] = ($matrix[$sid][$idk] ?? 0) / $akar;
    }
}

// Hitung nilai MOORA
$hasilMoora = [];
foreach ($startupIds as $sid) {
    $benefit = 0;
    $cost = 0;

    foreach ($kriteria as $k) {
        $idk = $k['id'];
        $bobot = $k['bobot'] / 100;
        $nilai = $normal[$sid][$idk] ?? 0;

        if ($k['sifat'] === 'benefit') {
            $benefit += $nilai * $bobot;
        } else {
            $cost += $nilai * $bobot;
        }
    }

    $hasilMoora[$sid] = $benefit - $cost;
}

// Simpan ke tabel hasil_moora
$tanggal = date('Y-m-d');
$conn->query("DELETE FROM hasil_moora WHERE tanggal_proses = '$tanggal'");

arsort($hasilMoora); // urutkan dari nilai tertinggi
$rank = 1;

foreach ($hasilMoora as $sid => $nilai) {
    $stmt = $conn->prepare("INSERT INTO hasil_moora (startup_id, nilai_akhir, ranking, tanggal_proses) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idis", $sid, $nilai, $rank, $tanggal);
    $stmt->execute();
    $rank++;
}

$_SESSION['success'] = "Perhitungan MOORA berhasil dilakukan.";
header("Location: hasil_moora.php");
exit;
