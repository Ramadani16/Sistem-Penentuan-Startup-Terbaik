<?php
// Path tujuan login
$loginPath = 'auth/login.php';

// Cek apakah file login.php ada
if (file_exists($loginPath)) {
    // Redirect ke halaman login dengan status 302 (Found / Temporary Redirect)
    header('Location: ' . $loginPath, true, 302);
    exit;
} else {
    // Jika file tidak ditemukan, tampilkan pesan error
    http_response_code(404);
    echo 'Halaman login tidak ditemukan.';
    exit;
}
