<?php
session_start();
include '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = (int)$_POST['id'];

  // Cek apakah user dengan ID ini ada
  $check = $conn->query("SELECT * FROM users WHERE id = $id LIMIT 1");
  if ($check->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Pengguna tidak ditemukan']);
    exit;
  }

  // Hapus user
  $delete = $conn->query("DELETE FROM users WHERE id = $id");

  if ($delete) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus']);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
