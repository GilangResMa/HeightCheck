<?php
// Gunakan require_once agar tidak crash jika dipanggil berkali-kali
require_once 'db.php'; 

// Cek apakah file ini dipanggil secara internal oleh data_processing.php?
if (isset($internal_data)) {
    // Jika YA, pakai data dari internal
    $input = $internal_data;
    // Matikan header JSON agar tidak bentrok dengan output data_processing
} else {
    // Jika TIDAK (dipanggil langsung oleh ESP/Postman), baca input mentah
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
}

if (!$input) {
    // Jika dipanggil internal, kita return saja array errornya
    if (isset($internal_data)) {
        $status_simpan = ['status' => 'error', 'message' => 'Data kosong'];
        return; 
    }
    echo json_encode(['status' => 'error', 'message' => 'Data kosong']);
    exit;
}

// Ambil Data
$nama           = $input['nama'] ?? 'User IoT';
$umur           = $input['umur'] ?? 0;
$jenis_kelamin  = $input['jenis_kelamin'] ?? '-';
$tinggi         = $input['tinggi'] ?? 0; // Ini sudah tinggi hasil olahan

// --- PROSES SIMPAN KE DB ---
$response_db = [];

// 1. Simpan User (gunakan prepared statement untuk keamanan)
$stmt_user = $conn->prepare("INSERT INTO users (nama, umur, jenis_kelamin) VALUES (?, ?, ?)");
$stmt_user->bind_param("sis", $nama, $umur, $jenis_kelamin);

if ($stmt_user->execute()) {
    $id_terbaru = $conn->insert_id;

    // 2. Simpan Data Ukur
    $stmt_ukur = $conn->prepare("INSERT INTO data_ukur (tinggi, id, tanggal_input) VALUES (?, ?, NOW())");
    $stmt_ukur->bind_param("di", $tinggi, $id_terbaru);
    
    if ($stmt_ukur->execute()) {
        $response_db = ['status' => 'success', 'message' => 'Data berhasil disimpan ke DB', 'user_id' => $id_terbaru];
    } else {
        $response_db = ['status' => 'error', 'message' => 'Gagal DB Ukur: ' . $conn->error];
    }
    $stmt_ukur->close();
} else {
    $response_db = ['status' => 'error', 'message' => 'Gagal DB User: ' . $conn->error];
}
$stmt_user->close();

// OUTPUT
// Jika dipanggil internal, simpan hasil ke variabel global agar bisa dibaca data_processing
if (isset($internal_data)) {
    $status_simpan = $response_db;
} else {
    echo json_encode($response_db);
}
?>