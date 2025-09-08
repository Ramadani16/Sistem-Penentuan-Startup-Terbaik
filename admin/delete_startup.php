<?php
session_start();
include '../config/database.php';

// Header JSON hanya untuk request AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!isset($_SESSION['user_id'])) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    } else {
        header("Location: ../login.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        $stmt = $conn->prepare("DELETE FROM startup_profiles WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Data startup berhasil dihapus!'
                ]);
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Data startup berhasil dihapus!'
                ];
                header("Location: dashboard.php");
            }
        } else {
            throw new Exception("Gagal menghapus data: " . $stmt->error);
        }
    } catch (Exception $e) {
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => $e->getMessage()
            ];
            header("Location: dashboard.php");
        }
    } finally {
        $stmt->close();
        $conn->close();
    }
} else {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Permintaan tidak valid!'
        ]);
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'warning',
            'message' => 'Permintaan tidak valid!'
        ];
        header("Location: dashboard.php");
    }
}
exit;
?>

