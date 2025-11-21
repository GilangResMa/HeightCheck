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

// 1. Simpan User
$sql_user = "INSERT INTO users (nama, umur, jenis_kelamin) VALUES ('$nama', '$umur', '$jenis_kelamin')";

if (mysqli_query($conn, $sql_user)) {
    $id_terbaru = mysqli_insert_id($conn);

    // 2. Simpan Data Ukur
    $sql_ukur = "INSERT INTO data_ukur (tinggi, id, tanggal_input) VALUES ('$tinggi', '$id_terbaru', NOW())";
    
    if (mysqli_query($conn, $sql_ukur)) {
        $response_db = ['status' => 'success', 'message' => 'Data berhasil disimpan ke DB'];
    } else {
        $response_db = ['status' => 'error', 'message' => 'Gagal DB Ukur: ' . mysqli_error($conn)];
    }
} else {
    $response_db = ['status' => 'error', 'message' => 'Gagal DB User: ' . mysqli_error($conn)];
}

// OUTPUT
// Jika dipanggil internal, simpan hasil ke variabel global agar bisa dibaca data_processing
if (isset($internal_data)) {
    $status_simpan = $response_db;
} else {
    echo json_encode($response_db);
}
?>