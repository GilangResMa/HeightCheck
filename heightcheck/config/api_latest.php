<?php
// File: config/api_latest.php
require_once 'db.php';
header('Content-Type: application/json');

// Ambil 1 data pengukuran paling baru (berdasarkan ID terbesar)
$sql = "SELECT tinggi, tanggal_input FROM data_ukur ORDER BY id_data DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'status' => 'success',
        'tinggi' => $row['tinggi'],
        'waktu'  => $row['tanggal_input']
    ]);
} else {
    echo json_encode(['status' => 'empty']);
}
?>