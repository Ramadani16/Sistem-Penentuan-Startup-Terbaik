<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("<div class='alert alert-danger'>Sesi habis. Silakan login ulang.</div>");
}

$user_id = $_SESSION['user_id'];

// Ambil data dari form
$nama_startup = $_POST['nama_startup'];
$deskripsi = $_POST['deskripsi'];
$bidang_usaha = $_POST['bidang_usaha'];
$tahun_berdiri = $_POST['tahun_berdiri'];
$lokasi = $_POST['lokasi'];

// Cek apakah profil sudah ada
$stmt = $conn->prepare("SELECT user_id FROM startup_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update profil yang sudah ada
    $stmt = $conn->prepare("UPDATE startup_profiles SET 
                          nama_startup = ?, 
                          deskripsi = ?, 
                          bidang_usaha = ?, 
                          tahun_berdiri = ?, 
                          lokasi = ? 
                          WHERE user_id = ?");
    $stmt->bind_param("sssssi", $nama_startup, $deskripsi, $bidang_usaha, $tahun_berdiri, $lokasi, $user_id);
} else {
    // Buat profil baru
    $stmt = $conn->prepare("INSERT INTO startup_profiles 
                          (user_id, nama_startup, deskripsi, bidang_usaha, tahun_berdiri, lokasi) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $nama_startup, $deskripsi, $bidang_usaha, $tahun_berdiri, $lokasi);
}

// Eksekusi query
if ($stmt->execute()) {
    $_SESSION['success_message'] = "Profil start-up berhasil disimpan!";
} else {
    $_SESSION['error_message'] = "Gagal menyimpan profil: " . $conn->error;
}

$stmt->close();
$conn->close();

// Redirect kembali ke form
header("Location: dashboard.php?page=form_profil");
exit();
?>