<?php
// Simpan di folder utama: hasil.php
include 'config/db.php';

// Ambil 1 data gabungan (users + data_ukur) yang paling baru
$query = "SELECT u.nama, u.umur, u.jenis_kelamin, d.tinggi, d.tanggal_input 
          FROM data_ukur d 
          JOIN users u ON d.id = u.id 
          ORDER BY d.id_data DESC 
          LIMIT 1";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Belum ada data pengukuran.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Pengukuran</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
      .result-box {
          background: #f8f9fa;
          border-radius: 10px;
          padding: 20px;
          margin: 20px 0;
          text-align: left;
      }
      .result-item {
          display: flex;
          justify-content: space-between;
          border-bottom: 1px solid #eee;
          padding: 10px 0;
      }
      .result-item span:first-child { color: #888; }
      .result-item span:last-child { font-weight: 600; color: #333; }
      .big-number {
          font-size: 3rem;
          color: #4a90e2;
          font-weight: bold;
          text-align: center;
          margin: 10px 0;
      }
  </style>
</head>
<body>

  <div class="container">
    <img src="assets/Logo-iot-2.svg" alt="Logo" class="logo">
    
    <h1>Hasil Pengukuran</h1>
    <p class="desc">Pengukuran berhasil dilakukan!</p>

    <div class="result-box">
        <div class="result-item">
            <span>Nama</span>
            <span><?= htmlspecialchars($data['nama']); ?></span>
        </div>
        <div class="result-item">
            <span>Umur</span>
            <span><?= htmlspecialchars($data['umur']); ?> Tahun</span>
        </div>
        <div class="result-item">
            <span>Jenis Kelamin</span>
            <span><?= htmlspecialchars($data['jenis_kelamin']); ?></span>
        </div>
        <div class="result-item">
            <span>Waktu Ukur</span>
            <span><?= date('d/m/Y H:i', strtotime($data['tanggal_input'])); ?></span>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <p>Tinggi Badan</p>
            <div class="big-number"><?= $data['tinggi']; ?> <span style="font-size: 1rem;">cm</span></div>
        </div>
    </div>

    <div class="menu">
      <a href="tabel.php" class="btn" style="background-color: #4a90e2;">Lihat Semua Data</a>
      <a href="index.php" class="btn btn-black">Kembali ke Menu Utama</a>
    </div>
  </div>

</body>
</html>