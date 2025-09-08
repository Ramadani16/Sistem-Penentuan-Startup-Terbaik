<?php
session_start();
include '../config/database.php';

// Cek session admin
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

// Validasi input
if (!isset($_POST['user_id']) || !isset($_POST['status'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

$userId = (int)$_POST['user_id'];
$status = in_array($_POST['status'], ['verified', 'pending']) ? $_POST['status'] : 'pending';

// Update status di database
$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $userId);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>