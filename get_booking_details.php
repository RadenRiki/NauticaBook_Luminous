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
    echo json_encode(['error' => 'Booking ID is required']);
    exit;
}

$booking_id = (int)$_GET['id'];

$query = "SELECT p.*, u.name as customer_name 
          FROM passengers p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($booking = mysqli_fetch_assoc($result)) {
    echo json_encode($booking);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Booking not found']);
}
?>