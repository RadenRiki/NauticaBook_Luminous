<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Debug logging
error_log("Received POST data: " . file_get_contents('php://input'));

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Connect to database
$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Parse incoming data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception("Invalid JSON data received");
    }

    // Generate unique barcode
    $barcode = 'TC' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));

    // Prepare SQL statement
    $sql = "INSERT INTO cargo (
        user_id,
        asal,
        tujuan,
        jenis,
        berat_kg,
        tanggal,
        nama_pengirim,
        alamat_pengirim,
        kota_pengirim,
        kodepos_pengirim,
        telepon_pengirim,
        nama_penerima,
        alamat_penerima,
        kota_penerima,
        kodepos_penerima,
        telepon_penerima,
        catatan,
        status,
        barcode
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Get user_id from session
    $user_id = $_SESSION['user_id'];

    // Default status
    $status = 'aktif';

    // Debug log
    error_log("Binding parameters for user_id: $user_id");
    error_log("Data to be inserted: " . print_r($data, true));

    $stmt->bind_param(
        "isssdssssssssssssss",
        $user_id,
        $data['asal'],
        $data['tujuan'],
        $data['jenis'],
        $data['berat_kg'],
        $data['tanggal'],
        $data['nama_pengirim'],
        $data['alamat_pengirim'],
        $data['kota_pengirim'],
        $data['kodepos_pengirim'],
        $data['telepon_pengirim'],
        $data['nama_penerima'],
        $data['alamat_penerima'],
        $data['kota_penerima'],
        $data['kodepos_penerima'],
        $data['telepon_penerima'],
        $data['catatan'],
        $status,
        $barcode
    );

    // Execute and check result
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'barcode' => $barcode
    ]);

} catch (Exception $e) {
    error_log("Error in save_cargo.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>