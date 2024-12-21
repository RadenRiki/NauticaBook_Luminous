<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Terima data dari POST request
$data = json_decode(file_get_contents('php://input'), true);

// Query untuk insert data
$sql = "INSERT INTO passengers (user_id, asal, tujuan, layanan, tipe, jumlah_penumpang, tanggal, jam) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssis", 
    $data['user_id'],
    $data['asal'],
    $data['tujuan'],
    $data['layanan'],
    $data['tipe'],
    $data['jumlah_penumpang'],
    $data['tanggal'],
    $data['jam']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>