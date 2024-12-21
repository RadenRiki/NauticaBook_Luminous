<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    echo "<script>alert('Password baru tidak cocok.'); window.location.href='profile.php';</script>";
    exit;
}

// Cek password lama
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verifikasi password lama 
if ($current_password !== $user['password']) {
    echo "<script>alert('Password lama salah.'); window.location.href='profile.php';</script>";
    exit;
}

// Update password baru
$sql = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_password, $user_id);

if ($stmt->execute()) {
    echo "<script>alert('Password berhasil diubah!'); window.location.href='profile.php';</script>";
} else {
    echo "<script>alert('Gagal mengubah password.'); window.location.href='profile.php';</script>";
}

$stmt->close();
$conn->close();
?>