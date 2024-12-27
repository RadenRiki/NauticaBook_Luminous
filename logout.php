<?php
session_start();

// Cek apakah yang logout adalah admin atau user biasa
$is_admin = isset($_SESSION['admin_id']);

session_destroy();

if ($is_admin) {
    header("Location: admin_login.php");
} else {
    header("Location: login.html");
}
exit;
?>