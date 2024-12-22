<?php
session_start();
header('Content-Type: application/json');

// Cek login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Terima data dari POST request
    $data = json_decode(file_get_contents('php://input'), true);

    // Pastikan user_id dari session digunakan
    $data['user_id'] = $_SESSION['user_id'];

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
        detail_penumpang
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    $formattedDate = date('Y-m-d', strtotime($data['tanggal']));
    $detailPenumpangJson = isset($data['detail_penumpang'])
        ? json_encode($data['detail_penumpang'])
        : null;
    
    $stmt->bind_param(
        "issssissssss",
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
        $detailPenumpangJson
    );
    

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
