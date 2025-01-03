<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

try {
    $rute = $_GET['rute'] ?? '';
    $layanan = $_GET['layanan'] ?? '';

    if (empty($rute) || empty($layanan)) {
        throw new Exception('Parameter rute dan layanan harus diisi');
    }

    // Prepare statement untuk mencegah SQL injection
    $sql = "SELECT tipe_tiket, kategori, harga FROM tarif_layanan WHERE rute = ? AND layanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $rute, $layanan);
    $stmt->execute();
    $result = $stmt->get_result();

    $tarif = [];
    while ($row = $result->fetch_assoc()) {
        // Konversi tipe_tiket sesuai format frontend
        $tipeTicket = $row['tipe_tiket'];
        switch($tipeTicket) {
            case 'Motor Besar':
                $tipeTicket = 'Motor Besar';
                break;
            case 'Motor Kecil':
                $tipeTicket = 'Motor Kecil';
                break;
            case 'Pejalan Kaki':
                $tipeTicket = 'Pejalan Kaki';
                break;
            case 'Sepeda':
                $tipeTicket = 'Sepeda';
                break;
            case 'Mobil':
                $tipeTicket = 'Mobil';
                break;
        }

        if ($row['kategori']) {
            if (!isset($tarif[$tipeTicket])) {
                $tarif[$tipeTicket] = [];
            }
            $tarif[$tipeTicket][strtolower($row['kategori'])] = (float)$row['harga'];
        } else {
            $tarif[$tipeTicket] = (float)$row['harga'];
        }
    }

    echo json_encode($tarif);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>