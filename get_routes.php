<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

// Ambil rute unik dari tarif_layanan
$query = "SELECT DISTINCT rute FROM tarif_layanan";
$result = mysqli_query($conn, $query);

$routes = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Split rute menjadi asal dan tujuan
    list($asal, $tujuan) = explode('-', $row['rute']);
    
    // Tambahkan ke array jika belum ada
    if (!in_array($asal, $routes)) {
        $routes[] = $asal;
    }
    if (!in_array($tujuan, $routes)) {
        $routes[] = $tujuan;
    }
}

// Format untuk response
$response = array();
foreach ($routes as $route) {
    // Format nama pelabuhan (capitalize first letter)
    $formatted = ucfirst($route);
    $response[] = array(
        'value' => $route,
        'label' => $formatted
    );
}

echo json_encode($response);
mysqli_close($conn);
?>