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
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM passengers WHERE user_id = ? ORDER BY tanggal DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $tickets = [];
    while($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }

    echo json_encode($tickets);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->bind_param("i", $user_id);
    if (isset($conn)) $conn->close();
}
?>