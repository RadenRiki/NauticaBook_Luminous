<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    error_log('Received data: ' . print_r($data, true));

    $conn = new mysqli("localhost", "root", "root", "luminousdb");
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $barcode = 'TC' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));

    $sql = "INSERT INTO cargo (
        user_id,
        pelabuhanAsal,
        pelabuhanTujuan,
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
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $status = 'aktif';

    $stmt->bind_param(
        "isssssdssssssssssssss",  // 21 karakter (i,s,s,s,s,s,d,s,s,s,s,s,s,s,s,s,s,s,s,s,s)
        $data['user_id'],         // i
        $data['pelabuhanAsal'],   // s
        $data['pelabuhanTujuan'], // s 
        $data['asal'],           // s
        $data['tujuan'],         // s
        $data['jenis'],          // s
        $data['berat_kg'],       // d
        $data['tanggal'],        // s
        $data['nama_pengirim'],  // s
        $data['alamat_pengirim'],// s
        $data['kota_pengirim'],  // s
        $data['kodepos_pengirim'],// s
        $data['telepon_pengirim'],// s
        $data['nama_penerima'],   // s
        $data['alamat_penerima'], // s
        $data['kota_penerima'],   // s
        $data['kodepos_penerima'],// s
        $data['telepon_penerima'],// s
        $data['catatan'],         // s
        $status,                  // s
        $barcode                  // s
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'barcode' => $barcode
    ]);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code($e->getMessage() === 'Unauthorized' ? 401 : 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>