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

  <script>
    function triggerSensor() {
      const espIp = "http://10.14.115.128/ukur"; // Ganti dengan IP ESP A
      const statusText = document.getElementById("status-sensor");

      statusText.innerText = "Mengirim perintah ke sensor...";

      // Menggunakan fetch untuk menembak URL ESP A
      fetch(espIp)
        .then(response => {
          if (response.ok) {
            return response.text();
          } else {
            throw new Error('Gagal menghubungi sensor');
          }
        })
        .then(data => {
          statusText.innerText = "Berhasil: " + data;
          statusText.style.color = "green";
          // Opsional: Reload halaman setelah beberapa detik untuk melihat data baru
          // setTimeout(() => location.reload(), 3000);
        })
        .catch(error => {
          console.error('Error:', error);
          statusText.innerText = "Gagal konek ke ESP A (Pastikan satu jaringan WiFi)";
          statusText.style.color = "red";
        });
    }
  </script>
</body>

</html>