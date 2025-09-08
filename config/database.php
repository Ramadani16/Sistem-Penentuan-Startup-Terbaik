<?php
// Konfigurasi database
$host = 'localhost';        // Nama host database (biasanya localhost)
$dbname = 'invest';  // Ganti dengan nama database kamu
$user = 'root';             // Username database
$pass = '';                 // Password database (kosong jika default XAMPP)

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}
?>
