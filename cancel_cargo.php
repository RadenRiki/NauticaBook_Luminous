<?php
session_start();
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

if (!isset($_POST['cargo_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Cargo ID not provided'
    ]);
    exit;
}

$cargo_id = (int)$_POST['cargo_id'];

// Mulai transaction
mysqli_begin_transaction($conn);

try {
    // Hapus cargo
    $query = "DELETE FROM cargo WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $cargo_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to delete cargo");
    }

    // Commit transaction
    mysqli_commit($conn);

    echo json_encode([
        'success' => true,
        'message' => 'Cargo shipment cancelled successfully'
    ]);

} catch (Exception $e) {
    // Rollback jika terjadi error
    mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>