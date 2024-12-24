<?php
session_start();
header('Content-Type: application/json');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['valid' => false, 'message' => 'User not logged in.']);
    exit;
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "luminousdb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    echo json_encode(['valid' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Terima data JSON
$data = json_decode(file_get_contents('php://input'), true);
$referralCode = isset($data['referral']) ? trim($data['referral']) : '';

// Validasi input
if (empty($referralCode)) {
    echo json_encode(['valid' => false, 'message' => 'Referral code is required.']);
    exit;
}

// Mendapatkan ID pengguna yang sedang login
$userId = $_SESSION['user_id'];

// Periksa apakah pengguna sudah pernah menggunakan referral
$stmt = $conn->prepare("SELECT has_referral FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($hasReferral);
$stmt->fetch();
$stmt->close();

if ($hasReferral) {
    echo json_encode(['valid' => false, 'message' => 'Anda sudah menggunakan kode referral.']);
    $conn->close();
    exit;
}

// Validasi kode referral (pastikan kode tidak digunakan oleh pengguna yang sama)
$stmt = $conn->prepare("SELECT id FROM users WHERE referral = ?");
$stmt->bind_param("s", $referralCode);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah kode referral valid dan bukan milik pengguna sendiri
if ($result->num_rows > 0) {
    $referralUser = $result->fetch_assoc();
    if ($referralUser['id'] == $userId) {
        echo json_encode(['valid' => false, 'message' => 'Anda tidak dapat menggunakan kode referral Anda sendiri.']);
    } else {
        // Update status referral pengguna
        $updateStmt = $conn->prepare("UPDATE users SET has_referral = TRUE WHERE id = ?");
        $updateStmt->bind_param("i", $userId);
        if ($updateStmt->execute()) {
            echo json_encode(['valid' => true, 'message' => 'Referral code applied successfully.']);
        } else {
            echo json_encode(['valid' => false, 'message' => 'Failed to update referral status.']);
        }
        $updateStmt->close();
    }
} else {
    echo json_encode(['valid' => false, 'message' => 'Invalid referral code.']);
}

$conn->close();
?>
