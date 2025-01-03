<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_GET['asal'])) {
    echo json_encode(['error' => 'Asal not provided']);
    exit;
}

$asal = mysqli_real_escape_string($conn, $_GET['asal']);

// Cari rute yang valid berdasarkan asal
$query = "SELECT DISTINCT rute FROM tarif_layanan WHERE rute LIKE ?";
$stmt = mysqli_prepare($conn, $query);
$pattern = $asal . "-%";
mysqli_stmt_bind_param($stmt, "s", $pattern);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$validRoutes = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Ambil tujuan dari rute
    list($_, $tujuan) = explode('-', $row['rute']);
    
    if (!in_array($tujuan, $validRoutes)) {
        $validRoutes[] = array(
            'value' => $tujuan,
            'label' => ucfirst($tujuan)
        );
    }
}

echo json_encode($validRoutes);
mysqli_close($conn);
?>