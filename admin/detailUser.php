<?php
session_start();
include '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

if (!isset($_GET['id'])) {
  echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
  exit;
}

$id = (int)$_GET['id'];
$query = $conn->query("SELECT * FROM users WHERE id = $id LIMIT 1");

if ($query->num_rows === 0) {
  echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
  exit;
}

$user = $query->fetch_assoc();

echo json_encode([
  'success' => true,
  'user' => [
    'id' => $user['id'],
    'nama_lengkap' => $user['nama_lengkap'],
    'email' => $user['email'],
    'username' => $user['username'],
    'role' => $user['role'],
    'status' => $user['status'],
    'created_at' => date('d M Y H:i', strtotime($user['created_at']))
  ]
]);
