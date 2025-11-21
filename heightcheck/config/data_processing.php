<?php
require_once 'db.php'; 
header('Content-Type: application/json');

// 1. TERIMA DATA MENTAH DARI ESP32
$input_esp = json_decode(file_get_contents('php://input'), true);

$jarak_sensor   = $input_esp['jarak_sensor'] ?? 0;
$nama           = $input_esp['nama'] ?? 'User IoT';

// 2. PROSES LOGIKA (Hitung Tinggi)
// Rumus: 200cm - Jarak Terbaca
$tinggi_bersih = 200 - $jarak_sensor;
if ($tinggi_bersih < 0) $tinggi_bersih = 0;

// 3. SIAPKAN DATA UNTUK DISIMPAN
// Kita masukkan ke variabel khusus bernama $internal_data
$internal_data = [
    'nama'          => $nama,
    'umur'          => $input_esp['umur'] ?? 20,
    'jenis_kelamin' => $input_esp['jenis_kelamin'] ?? 'Laki-laki',
    'tinggi'        => $tinggi_bersih // Hasil perhitungan dikirim ke save_data
];

// 4. PANGGIL SAVE_DATA.PHP SECARA INTERNAL
// Variabel $status_simpan akan terisi otomatis setelah file di-include
$status_simpan = []; 
include 'save_data.php'; 

// 5. KEMBALIKAN DATA KE ESP32 (JSON FINAL)
// Kita gabungkan hasil hitungan dengan status penyimpanan database
echo json_encode([
    'status_processing' => 'success',
    'jarak_sensor'      => $jarak_sensor,
    'tinggi_final'      => $tinggi_bersih, // Data ini yang diambil LCD ESP32
    'database_info'     => $status_simpan  // Info sukses/gagal dari save_data
]);
?>