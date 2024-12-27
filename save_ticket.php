<?php
session_start();
header('Content-Type: application/json');

// Tambahkan error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek login
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
    // Log raw input data
    error_log("Received raw data: " . file_get_contents('php://input'));

    // Terima data dari POST request
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Debug logs
    error_log("Received ticket data: " . print_r($data, true));

    // Validasi data yang diperlukan
    $requiredFields = ['asal', 'tujuan', 'layanan', 'tipe', 'jumlah_penumpang', 'tanggal', 'jam', 'nama_pemesan', 'email_pemesan', 'nomor_hp', 'detail_penumpang', 'total_harga'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validasi detail penumpang
    if (!isset($data['detail_penumpang'])) {
        throw new Exception('Missing passenger details');
    }

    // Pastikan user_id dari session digunakan
    $data['user_id'] = $_SESSION['user_id'];

    // Generate unique barcode
    $barcode = 'TF' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
    
    // Query untuk insert data
    $sql = "INSERT INTO passengers (
        user_id,
        asal,
        tujuan,
        layanan,
        tipe,
        jumlah_penumpang,
        tanggal,
        jam,
        nama_pemesan,
        email_pemesan,
        nomor_hp,
        detail_penumpang,
        barcode,
        total_harga
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    $formattedDate = date('Y-m-d', strtotime($data['tanggal']));
    if ($formattedDate === false) {
        throw new Exception('Invalid date format');
    }
    
    // Debug logs untuk detail penumpang
    error_log("Detail penumpang before encode: " . print_r($data['detail_penumpang'], true));
    
    // Encode detail penumpang dengan validasi
    $detailPenumpangJson = json_encode($data['detail_penumpang']);
    error_log("Detail penumpang after encode: " . $detailPenumpangJson);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Failed to encode passenger details: ' . json_last_error_msg());
    }
    
    $stmt->bind_param(
        "issssisssssssd",
        $data['user_id'],
        $data['asal'],
        $data['tujuan'],
        $data['layanan'],
        $data['tipe'],
        $data['jumlah_penumpang'],
        $formattedDate,
        $data['jam'],
        $data['nama_pemesan'],
        $data['email_pemesan'],
        $data['nomor_hp'],
        $detailPenumpangJson,
        $barcode,
        $data['total_harga']
    );

    if ($stmt->execute()) {
        error_log("Successfully inserted ticket data");
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    error_log("Error in save_ticket.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>