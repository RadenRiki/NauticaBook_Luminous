<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID not provided']);
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
    // Format data before sending
    $booking['total_harga'] = number_format($booking['total_harga'], 2, '.', '');
    $booking['tanggal'] = date('Y-m-d', strtotime($booking['tanggal']));
    $booking['jam'] = date('H:i', strtotime($booking['jam']));
    
    // Decode JSON string if it exists
    if (isset($booking['detail_penumpang'])) {
        $booking['detail_penumpang'] = json_decode($booking['detail_penumpang'], true);
    }
    
    echo json_encode($booking);
} else {
    echo json_encode(['error' => 'Booking not found']);
}

mysqli_close($conn);
?>