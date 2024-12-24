<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Koneksi database langsung di sini
$conn = new mysqli("localhost", "root", "root", "luminousdb");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

header('Content-Type: application/json'); // Set header ke JSON

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if (!isset($_FILES['photo'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$uploadDir = 'uploads/profile_photos/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$file = $_FILES['photo'];
$fileName = time() . '_' . basename($file['name']);
$targetFile = $uploadDir . $fileName;

// Check file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG & GIF allowed']);
    exit;
}

// Check file size (5MB max)
if ($file['size'] > 5000000) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max 5MB']);
    exit;
}

try {
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Delete old photo if exists
        $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user['profile_photo']) {
            $oldFile = $uploadDir . $user['profile_photo'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Update database
        $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $_SESSION['profile_photo'] = $fileName;
            echo json_encode(['success' => true, 'filename' => $fileName]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>