<?php
require '../config/koneksi.php';

// Baca input JSON
$input = json_decode(file_get_contents('php://input'), true);

// Nangkap Data JSON (perlu diubah)
$nama       = $input['nama'] ?? null;
$umur      = $input['umur'] ?? null;
$jenis_kelamin = $input['jenis_kelamin'] ?? null;
$tinggi = $input['tinggi'] ?? null;
$tanggal_input = $input['tanggal_input'] ?? null;

$sql = "INSERT INTO users (nama, umur, jenis_kelamin) VALUES ('$nama', $umur, '$jenis_kelamin')";
mysqli_query($koneksi, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit;
}

$sql = "SELECT id_users FROM users ORDER BY id_users DESC LIMIT 10";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $id_terbaru = $row['id_users']; // Simpan ID ke variabel PHP
    echo "ID terbaru adalah: " . $id_terbaru;
} else {
    echo "Tidak ada data di tabel.";
}

$sql = "INSERT INTO data_ukur (tinggi, tanggal_input, id) VALUES ($tinggi, '$tanggal_input', $id_terbaru)";
mysqli_query($koneksi, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit;
}

echo json_encode(['status' => 'ok']);
?>