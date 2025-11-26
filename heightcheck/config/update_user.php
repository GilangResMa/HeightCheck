<?php
// Simpan di: config/update_user.php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $umur = $_POST['umur'];
    $jk   = $_POST['jenis_kelamin'];

    // Kita update user dengan ID TERAKHIR (ID terbesar)
    // Asumsinya adalah data yang baru saja masuk dari ESP32 memiliki ID paling besar
    
    // 1. Cari ID user terakhir
    $sql_get_id = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql_get_id);
    
    if($row = mysqli_fetch_assoc($result)) {
        $last_id = $row['id'];

        // 2. Update data user tersebut
        $sql_update = "UPDATE users SET nama='$nama', umur='$umur', jenis_kelamin='$jk' WHERE id='$last_id'";
        
        if (mysqli_query($conn, $sql_update)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data user']);
    }
}
?>