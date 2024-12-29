<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

$rute = $_GET['rute'] ?? '';
$jenis = $_GET['jenis'] ?? '';

if (!$rute || !$jenis) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Format rute sesuai dengan format di database
$rute = strtolower($rute);

$query = "SELECT harga_per_kg FROM tarif_cargo 
          WHERE rute = ? AND jenis_barang = ? AND aktif = true";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $rute, $jenis);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(['success' => true, 'harga_per_kg' => $row['harga_per_kg']]);
} else {
    // Jika tidak ditemukan, gunakan tarif default
    echo json_encode(['success' => true, 'harga_per_kg' => 15000]);
}
?>