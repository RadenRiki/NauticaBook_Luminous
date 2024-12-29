<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID not provided']);
    exit;
}

$cargo_id = (int)$_GET['id'];

$query = "SELECT c.*, u.name as customer_name,
          (SELECT harga_per_kg * c.berat_kg FROM tarif_cargo tc 
           WHERE tc.rute = CONCAT(LOWER(c.asal), '-', LOWER(c.tujuan)) 
           AND tc.jenis_barang = c.jenis LIMIT 1) as total_harga
          FROM cargo c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $cargo_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($cargo = mysqli_fetch_assoc($result)) {
    // Format data
    $data = [
        'customer' => $cargo['customer_name'],
        'route' => $cargo['asal'] . ' - ' . $cargo['tujuan'],
        'type' => $cargo['jenis'],
        'weight' => $cargo['berat_kg'] . ' kg',
        'date' => date('d M Y', strtotime($cargo['tanggal'])),
        'status' => $cargo['status'],
        'barcode' => $cargo['barcode'],
        'total_harga' => number_format($cargo['total_harga'], 0, ',', '.'),
        'pengirim' => [
            'nama' => $cargo['nama_pengirim'],
            'alamat' => $cargo['alamat_pengirim'],
            'kota' => $cargo['kota_pengirim'],
            'kodepos' => $cargo['kodepos_pengirim'],
            'telepon' => $cargo['telepon_pengirim']
        ],
        'penerima' => [
            'nama' => $cargo['nama_penerima'],
            'alamat' => $cargo['alamat_penerima'],
            'kota' => $cargo['kota_penerima'],
            'kodepos' => $cargo['kodepos_penerima'],
            'telepon' => $cargo['telepon_penerima']
        ],
        'catatan' => $cargo['catatan']
    ];

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Cargo data not found'
    ]);
}

mysqli_close($conn);
?>