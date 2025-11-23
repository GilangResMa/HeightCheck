<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config/db.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HeightCheck Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <div class="container">
  <img src="assets/Logo-iot-2.svg" alt="HeightCheck Logo" class="logo">

    <h1>Selamat Datang di HeightCheck</h1>
    <p class="desc">Aplikasi sederhana untuk mengukur dan memantau tinggi badanmu.</p>

    <div class="menu">
      <a href="cek_tinggi.php" class="btn">Cek Tinggi Badan</a>
      <a href="tabel.php" class="btn">Lihat Data</a>
      <a href="statistik.php" class="btn">Lihat Statistik</a>
    </div>
  </div>

</body>
</html>
