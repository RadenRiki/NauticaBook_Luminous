<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');

// Debug logging
error_log("Received POST data: " . file_get_contents('php://input'));
error_log("Session data: " . print_r($_SESSION, true));

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $data['user_id'] = $_SESSION['user_id'];

    // Debug log untuk melihat structure data yang diterima
    error_log("Decoded data structure: " . print_r($data, true));
    
    $sql = "INSERT INTO cargo (
        user_id, asal, tujuan, jenis, berat_kg, tanggal,
        nama_pengirim, alamat_pengirim, kota_pengirim, kodepos_pengirim, telepon_pengirim,
        nama_penerima, alamat_penerima, kota_penerima, kodepos_penerima, telepon_penerima,
        catatan, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Convert date to proper format
    $formattedDate = date('Y-m-d', strtotime($data['tanggal']));
    $status = 'aktif';

    // Bind parameters
    $stmt->bind_param(
        "isssdsssssssssssss",
        $data['user_id'],
        $data['asal'],
        $data['tujuan'],
        $data['jenis'],
        $data['berat_kg'],
        $formattedDate,
        $data['pengirim']['nama'],
        $data['pengirim']['alamat'],
        $data['pengirim']['kota'],
        $data['pengirim']['kodePos'],
        $data['pengirim']['telepon'],
        $data['penerima']['nama'],
        $data['penerima']['alamat'],
        $data['penerima']['kota'],
        $data['penerima']['kodePos'],
        $data['penerima']['telepon'],
        $data['catatan'],
        $status
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    error_log("Error in save_cargo.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>