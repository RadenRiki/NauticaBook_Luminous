<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');

// Debug logging
error_log("Received POST data: " . file_get_contents('php://input'));
error_log("Session data: " . print_r($_SESSION, true));

// Cek autentikasi pengguna
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Mendecode data JSON yang diterima
    $data = json_decode(file_get_contents('php://input'), true);
    $data['user_id'] = $_SESSION['user_id'];

    // Debug log untuk melihat struktur data yang diterima
    error_log("Decoded data structure: " . print_r($data, true));

    // Generate barcode unik
    $barcode = 'TC' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));

    // Menyiapkan pernyataan SQL dengan tambahan kolom barcode
    $sql = "INSERT INTO cargo (
        user_id, asal, tujuan, jenis, berat_kg, tanggal,
        nama_pengirim, alamat_pengirim, kota_pengirim, kodepos_pengirim, 
        telepon_pengirim, nama_penerima, alamat_penerima, kota_penerima, 
        kodepos_penerima, telepon_penerima, catatan, status, barcode
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Konversi tanggal ke format yang benar
    $formattedDate = date('Y-m-d', strtotime($data['tanggal']));
    $status = 'aktif';

    // Menyesuaikan tipe parameter untuk bind_param
    // "i" untuk integer, "s" untuk string, "d" untuk double (berat_kg)
    // Total 19 parameter sesuai dengan kolom dalam INSERT
    $stmt->bind_param(
        "isssdssssssssssssss",
        $data['user_id'],
        $data['asal'],
        $data['tujuan'],
        $data['jenis'],
        $data['berat_kg'],
        $formattedDate,
        $data['pengirim']['nama'],
        $data['pengirim']['alamat'],
        $data['pengirim']['kota'],
        $data['pengirim']['kodePos'],
        $data['pengirim']['telepon'],
        $data['penerima']['nama'],
        $data['penerima']['alamat'],
        $data['penerima']['kota'],
        $data['penerima']['kodePos'],
        $data['penerima']['telepon'],
        $data['catatan'],
        $status,
        $barcode
    );

    // Eksekusi pernyataan dan cek keberhasilan
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'barcode' => $barcode]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    // Logging error dan mengembalikan response error
    error_log("Error in save_cargo.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Menutup statement dan koneksi jika sudah diatur
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>
