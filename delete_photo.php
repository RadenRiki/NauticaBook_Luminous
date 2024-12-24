<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Koneksi database
$conn = new mysqli("localhost", "root", "root", "luminousdb");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    // Get current photo filename
    $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['profile_photo']) {
        $photoPath = 'uploads/profile_photos/' . $user['profile_photo'];
        
        // Delete file if exists
        if (file_exists($photoPath)) {
            if (!unlink($photoPath)) {
                throw new Exception('Failed to delete file');
            }
        }

        // Update database
        $stmt = $conn->prepare("UPDATE users SET profile_photo = NULL WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            unset($_SESSION['profile_photo']);
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Database update failed');
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No photo to delete']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>