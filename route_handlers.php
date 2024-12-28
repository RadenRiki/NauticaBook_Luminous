<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized');
}

$action = $_POST['action'] ?? '';

// Debug log untuk melihat action dan data yang diterima
error_log("Received action: " . $action);
error_log("POST data: " . print_r($_POST, true));

switch ($action) {
    case 'add_route':
        $rute = mysqli_real_escape_string($conn, $_POST['rute']);
        $layanan = mysqli_real_escape_string($conn, $_POST['layanan']);
        $tipe_tiket = mysqli_real_escape_string($conn, $_POST['tipe_tiket']);
        $kategori = !empty($_POST['kategori']) ? mysqli_real_escape_string($conn, $_POST['kategori']) : null;
        $harga = (float)$_POST['harga'];
        
        error_log("Adding new route: Rute=$rute, Layanan=$layanan, Tipe=$tipe_tiket, Harga=$harga");
        
        $query = "INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            error_log("Failed to prepare insert statement: " . mysqli_error($conn));
            header('Location: admin.php?tab=routes&error=add_failed');
            exit;
        }
        
        mysqli_stmt_bind_param($stmt, "ssssd", $rute, $layanan, $tipe_tiket, $kategori, $harga);
        
        if (mysqli_stmt_execute($stmt)) {
            error_log("Successfully added new route");
            header('Location: admin.php?tab=routes&msg=route_added');
        } else {
            error_log("Failed to execute insert: " . mysqli_stmt_error($stmt));
            header('Location: admin.php?tab=routes&error=add_failed');
        }
        break;
        
    case 'edit_route':
        $tarif_id = (int)$_POST['tarif_id'];
        $rute = mysqli_real_escape_string($conn, $_POST['rute']);
        $layanan = mysqli_real_escape_string($conn, $_POST['layanan']);
        $tipe_tiket = mysqli_real_escape_string($conn, $_POST['tipe_tiket']);
        $kategori = !empty($_POST['kategori']) ? mysqli_real_escape_string($conn, $_POST['kategori']) : null;
        $harga = (float)$_POST['harga'];
        
        error_log("Updating route: ID=$tarif_id, Rute=$rute, Layanan=$layanan, Tipe=$tipe_tiket, Harga=$harga");
        
        // Query to check if record exists
        $check_query = "SELECT id FROM tarif_layanan WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $tarif_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) === 0) {
            error_log("Route ID $tarif_id not found");
            header('Location: admin.php?tab=routes&error=route_not_found');
            exit;
        }
        
        $query = "UPDATE tarif_layanan SET rute = ?, layanan = ?, tipe_tiket = ?, kategori = ?, harga = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            error_log("Failed to prepare update statement: " . mysqli_error($conn));
            header('Location: admin.php?tab=routes&error=update_failed');
            exit;
        }
        
        mysqli_stmt_bind_param($stmt, "ssssdi", $rute, $layanan, $tipe_tiket, $kategori, $harga, $tarif_id);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                error_log("Successfully updated route ID $tarif_id");
                header('Location: admin.php?tab=routes&msg=route_updated');
            } else {
                error_log("No changes made to route ID $tarif_id");
                header('Location: admin.php?tab=routes&msg=no_changes');
            }
        } else {
            error_log("Failed to execute update: " . mysqli_stmt_error($stmt));
            header('Location: admin.php?tab=routes&error=update_failed');
        }
        break;
        
    case 'delete_route':
        $tarif_id = (int)$_POST['tarif_id'];
        
        error_log("Deleting route ID: $tarif_id");
        
        $query = "DELETE FROM tarif_layanan WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            error_log("Failed to prepare delete statement: " . mysqli_error($conn));
            header('Location: admin.php?tab=routes&error=delete_failed');
            exit;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $tarif_id);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                error_log("Successfully deleted route ID $tarif_id");
                header('Location: admin.php?tab=routes&msg=route_deleted');
            } else {
                error_log("Route ID $tarif_id not found for deletion");
                header('Location: admin.php?tab=routes&error=route_not_found');
            }
        } else {
            error_log("Failed to execute delete: " . mysqli_stmt_error($stmt));
            header('Location: admin.php?tab=routes&error=delete_failed');
        }
        break;
        
    default:
        error_log("Invalid action received: $action");
        header('Location: admin.php');
        break;
}

// Close any remaining statements and connection
if (isset($stmt)) mysqli_stmt_close($stmt);
if (isset($check_stmt)) mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>