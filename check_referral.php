<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "root", "luminousdb");

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Terima data JSON
$data = json_decode(file_get_contents('php://input'), true);
$referralCode = $data['referral'];

// Query untuk cek referral code
$stmt = $conn->prepare("SELECT id FROM users WHERE referral = ?");
$stmt->bind_param("s", $referralCode);
$stmt->execute();
$result = $stmt->get_result();

// Return response
echo json_encode([
    'valid' => $result->num_rows > 0
]);

$conn->close();
?>