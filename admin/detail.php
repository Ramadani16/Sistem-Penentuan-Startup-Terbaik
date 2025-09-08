<?php
session_start();

header('Content-Type: application/json');
include '../config/database.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM startup_profiles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>