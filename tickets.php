<?php
session_start();
header('Content-Type: application/json');

// Tambahkan error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    
    // Debug log
    error_log("Fetching tickets for user_id: " . $user_id);
    
    $sql = "SELECT * FROM passengers WHERE user_id = ? ORDER BY tanggal DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Tambahkan log untuk hasil query
    error_log("Query result: " . print_r($result, true));

    $tickets = [];
    while($row = $result->fetch_assoc()) {
        // Debug untuk tiap row
        error_log("Processing row ID: " . $row['id']);
        error_log("Raw detail_penumpang: " . $row['detail_penumpang']);
        
        // Debug log untuk setiap row
        error_log("Raw ticket data: " . print_r($row, true));
        
        // Validasi data sebelum pemrosesan
        if (!isset($row['id']) || !isset($row['user_id'])) {
            error_log("Invalid row data - missing required fields");
            continue; // Skip row yang tidak valid
        }
        
        // Pastikan detail_penumpang di-decode jika ada
        if (isset($row['detail_penumpang']) && !empty($row['detail_penumpang'])) {
            // Coba decode JSON
            $decodedDetails = json_decode($row['detail_penumpang'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $row['detail_penumpang'] = $decodedDetails;
                error_log("Successfully decoded detail_penumpang for ID " . $row['id']);
            } else {
                error_log("JSON decode error for ticket ID " . $row['id'] . ": " . json_last_error_msg());
                // Jika gagal decode, set sebagai array kosong
                $row['detail_penumpang'] = [];
            }
        } else {
            $row['detail_penumpang'] = [];
        }
        
        // Format tanggal dengan validasi
        if (isset($row['tanggal']) && !empty($row['tanggal'])) {
            $timestamp = strtotime($row['tanggal']);
            if ($timestamp === false) {
                error_log("Invalid date format for ticket ID " . $row['id']);
                $row['tanggal'] = null;
            } else {
                $row['tanggal'] = date('Y-m-d', $timestamp);
            }
        } else {
            $row['tanggal'] = null;
        }
        
        $tickets[] = $row;
    }

    // Debug log final output
    error_log("Final tickets data: " . print_r($tickets, true));
    
    if (empty($tickets)) {
        error_log("No tickets found for user_id: " . $user_id);
    }
    
    echo json_encode($tickets);

} catch (Exception $e) {
    error_log("Error in tickets.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>