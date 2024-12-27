<?php
session_start();
require_once 'koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized');
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_admin':
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        
        $query = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: admin.php?tab=admin_management&msg=admin_added');
        } else {
            header('Location: admin.php?tab=admin_management&error=add_failed');
        }
        break;
        
    case 'edit_admin':
        $admin_id = (int)$_POST['admin_id'];
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        
        if ($password) {
            $query = "UPDATE admin SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $password, $admin_id);
        } else {
            $query = "UPDATE admin SET username = ?, email = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $admin_id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: admin.php?tab=admin_management&msg=admin_updated');
        } else {
            header('Location: admin.php?tab=admin_management&error=update_failed');
        }
        break;
        
    case 'delete_admin':
        $admin_id = (int)$_POST['admin_id'];
        
        // Tidak boleh menghapus diri sendiri
        if ($admin_id == $_SESSION['admin_id']) {
            header('Location: admin.php?tab=admin_management&error=cant_delete_self');
            exit;
        }
        
        $query = "DELETE FROM admin WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $admin_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: admin.php?tab=admin_management&msg=admin_deleted');
        } else {
            header('Location: admin.php?tab=admin_management&error=delete_failed');
        }
        break;
        
    default:
        header('Location: admin.php');
        break;
}
?>