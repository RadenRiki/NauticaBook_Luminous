<?php
session_start();
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Tarif ID is required']);
    exit;
}

$tarif_id = (int)$_GET['id'];

$query = "SELECT * FROM tarif_layanan WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $tarif_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($tarif = mysqli_fetch_assoc($result)) {
    echo json_encode($tarif);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Tarif not found']);
}
?>