<?php
session_start();
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['booking_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

$booking_id = (int)$_POST['booking_id'];

// Hapus booking
$query = "DELETE FROM passengers WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $booking_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
}
?>