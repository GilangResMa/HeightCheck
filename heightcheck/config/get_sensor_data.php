<?php
require '../config/koneksi.php';

// Ambil 50 data terakhir (perlu diubah)
$sql = "SELECT tinggi, tanggal_input FROM data_ukur
        WHERE id = '$id'
        ORDER BY tanggal_input DESC
        LIMIT 50";
$result = mysqli_query($koneksi, $sql);

// Proses data ke dalam array assoc
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

// echo json_encode($data);
echo json_encode($data);
?>