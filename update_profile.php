<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "luminousdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];

// Cek apakah email sudah digunakan user lain
$check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$check_email->bind_param("si", $email, $user_id);
$check_email->execute();
$result = $check_email->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email sudah digunakan.'); window.location.href='profile.php';</script>";
    exit;
}

// Update data profil
$sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $name, $email, $user_id);

if ($stmt->execute()) {
    // Update session data
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    
    // Update sessionStorage via JavaScript
    echo "<script>
        sessionStorage.setItem('user', JSON.stringify({
            id: " . $user_id . ",
            name: '" . $name . "',
            email: '" . $email . "'
        }));
        alert('Profil berhasil diperbarui!');
        window.location.href='profile.php';
    </script>";
} else {
    echo "<script>alert('Gagal memperbarui profil.'); window.location.href='profile.php';</script>";
}

$stmt->close();
$conn->close();
?>