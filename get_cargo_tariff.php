<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID not provided']);
    exit;
}

$tariff_id = (int)$_GET['id'];

$query = "SELECT * FROM tarif_cargo WHERE id = ? AND aktif = 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $tariff_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($tariff = mysqli_fetch_assoc($result)) {
    echo json_encode($tariff);
} else {
    echo json_encode(['error' => 'Tariff not found']);
}
?>