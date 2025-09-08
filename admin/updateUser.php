<?php
session_start();
include '../config/database.php'; // atau sesuaikan path database

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id'];
  $nama = $conn->real_escape_string(trim($_POST['nama_lengkap']));
  $email = $conn->real_escape_string(trim($_POST['email']));
  $role = $_POST['role'];
  $status = $_POST['status'];

  // Validasi dasar
  if (!$id || !$nama || !$email) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
  }

  // Validasi nilai role dan status
  if (!in_array($role, ['admin', 'startup']) || !in_array($status, ['verified', 'pending'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
  }

  // Proses update
  $update = $conn->query("UPDATE users SET 
    nama_lengkap = '$nama',
    email = '$email',
    role = '$role',
    status = '$status'
    WHERE id = $id
  ");

  if ($update) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Gagal update']);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
