<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized');
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_tariff':
        $rute = mysqli_real_escape_string($conn, $_POST['rute']);
        $jenis_barang = mysqli_real_escape_string($conn, $_POST['jenis_barang']);
        $harga_per_kg = (float)$_POST['harga_per_kg'];
        
        // Cek apakah kombinasi rute dan jenis_barang sudah ada
        $check_query = "SELECT id FROM tarif_cargo WHERE rute = ? AND jenis_barang = ? AND aktif = 1";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ss", $rute, $jenis_barang);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            header('Location: admin.php?tab=cargo-tariffs&error=tariff_exists');
            exit;
        }
        
        $query = "INSERT INTO tarif_cargo (rute, jenis_barang, harga_per_kg) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssd", $rute, $jenis_barang, $harga_per_kg);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: admin.php?tab=cargo-tariffs&msg=tariff_added');
        } else {
            header('Location: admin.php?tab=cargo-tariffs&error=add_failed');
        }
        break;
        
    case 'edit_tariff':
        $tariff_id = (int)$_POST['tariff_id'];
        $rute = mysqli_real_escape_string($conn, $_POST['rute']);
        $jenis_barang = mysqli_real_escape_string($conn, $_POST['jenis_barang']);
        $harga_per_kg = (float)$_POST['harga_per_kg'];
        
        // Cek apakah tarif masih aktif
        $check_query = "SELECT id FROM tarif_cargo WHERE id = ? AND aktif = 1";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $tariff_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) === 0) {
            header('Location: admin.php?tab=cargo-tariffs&error=tariff_not_found');
            exit;
        }
        
        $query = "UPDATE tarif_cargo SET rute = ?, jenis_barang = ?, harga_per_kg = ? WHERE id = ? AND aktif = 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssdi", $rute, $jenis_barang, $harga_per_kg, $tariff_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: admin.php?tab=cargo-tariffs&msg=tariff_updated');
        } else {
            header('Location: admin.php?tab=cargo-tariffs&error=update_failed');
        }
        break;
        
    case 'delete_tariff':
        $tariff_id = (int)$_POST['tariff_id'];
        
        // Soft delete dengan mengubah status aktif
        $query = "UPDATE tarif_cargo SET aktif = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $tariff_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: admin.php?tab=cargo-tariffs&msg=tariff_deleted');
        } else {
            header('Location: admin.php?tab=cargo-tariffs&error=delete_failed');
        }
        break;
        
    default:
        header('Location: admin.php');
        break;
}

if (isset($stmt)) mysqli_stmt_close($stmt);
if (isset($check_stmt)) mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>