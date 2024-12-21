<?php
// Mulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "root", "luminousdb");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data pengguna berdasarkan session user_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email, referral FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Profil Pengguna</title>
</head>
<body>
    <!-- Konten Profil -->
    <h1>Profil Pengguna</h1>
    <div>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Referral Code:</strong> <?php echo htmlspecialchars($user['referral']); ?></p>
    </div>
    <button onclick="window.location.href='logout.php'">Logout</button>
</body>
</html>
