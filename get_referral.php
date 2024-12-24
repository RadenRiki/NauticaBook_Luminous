<?php
session_start();
header('Content-Type: application/json');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['hasReferral' => false]);
    exit;
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "luminousdb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    echo json_encode(['hasReferral' => false]);
    exit;
}

// Mendapatkan status referral pengguna
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT has_referral FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($hasReferral);
$stmt->fetch();
$stmt->close();
$conn->close();

// Mengembalikan status referral
echo json_encode(['hasReferral' => $hasReferral ? true : false]);
?>
