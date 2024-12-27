<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM admin WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        // Update last login
        $update_query = "UPDATE admin SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $admin['id']);
        mysqli_stmt_execute($update_stmt);
        
        header('Location: admin.php');
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet">
    <title>Admin Login - NauticaBook</title>
</head>
<body>
    <div class="auth-page">
        <!-- Bagian kiri dengan gambar -->
        <div class="auth-left">
            <h1>NauticaBook Admin Panel</h1>
            <p>
                Selamat datang di panel admin NauticaBook. 
                Silakan masuk untuk mengelola sistem booking ferry dan cargo.
            </p>
        </div>
        
        <!-- Bagian kanan untuk login form -->
        <div class="auth-right">
            <form class="auth-form" method="POST">
                <h2>Admin Login</h2>
                <?php if (isset($error)): ?>
                    <div style="color: #dc2626; margin-bottom: 1rem; padding: 0.5rem; background-color: #fef2f2; border-radius: 0.375rem;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" placeholder="Enter admin email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn">Login as Admin</button>
            </form>
        </div>
    </div>
</body>
</html>