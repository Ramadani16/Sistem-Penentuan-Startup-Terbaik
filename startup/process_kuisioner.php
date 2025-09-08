<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("Sesi habis. Silakan login ulang.");
    }

    $startup_id = $_POST['startup_id'];
    $inovasi_produk = $_POST['inovasi_produk'];
    $potensi_pasar = $_POST['potensi_pasar'];
    $sustainability = $_POST['sustainability'];
    $reputasi_tim = $_POST['reputasi_tim'];
    $tingkat_risiko = $_POST['tingkat_risiko'];
    $valuasi_perusahaan = $_POST['valuasi_perusahaan'];
    $tanggal_pengisian = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO kuisioner_startup 
        (startup_id, inovasi_produk, potensi_pasar, sustainability, reputasi_tim, tingkat_risiko, valuasi_perusahaan, tanggal_pengisian)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("iiiissds", 
        $startup_id, 
        $inovasi_produk, 
        $potensi_pasar, 
        $sustainability, 
        $reputasi_tim, 
        $tingkat_risiko, 
        $valuasi_perusahaan, 
        $tanggal_pengisian
    );

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
} else {
    echo "Metode tidak diizinkan.";
}
