<?php
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (kode PHP bagian atas tetap sama, tidak perlu diubah) ...
    $nama = $_POST['nama'];
    $umur = $_POST['umur'];
    $jk   = $_POST['jenis_kelamin'];
    $tinggi = $_POST['tinggi']; 

    $query_user = "INSERT INTO users (nama, umur, jenis_kelamin) VALUES ('$nama', '$umur', '$jk')";
    
    if (mysqli_query($conn, $query_user)) {
        $id_user_baru = mysqli_insert_id($conn);
        $query_ukur = "INSERT INTO data_ukur (tinggi, id, tanggal_input) VALUES ('$tinggi', '$id_user_baru', NOW())";
        
        if (mysqli_query($conn, $query_ukur)) {
            echo "<script>alert('Data berhasil disimpan!'); window.location.href='tabel.php';</script>";
        } else {
            echo "Error (data_ukur): " . mysqli_error($conn);
        }

    } else {
        echo "Error (users): " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cek Tinggi - HeightCheck</title>
  <link rel="stylesheet" href="css/style.css"> 
</head>
<body>

  <img src="assets/logo.svg" alt="Logo" style="position: fixed; top: 30px; left: 40px; width: 70px; z-index: 9999; cursor: pointer;">

  <div class="container"> 
  <form method="POST" action="">
      <div class="form-group">
        <h1>Height Check</h1><br>
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="number" name="umur" placeholder="Umur" required>
        <select name="jenis_kelamin" required>
            <option value="">Pilih Gender</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
      </div>

      <div style="margin: 50px 0;">
          <p>Masukkan Tinggi (Simulasi Sensor)</p>
          <input type="number" name="tinggi" step="0.1" placeholder="0" style="font-size: 3rem; text-align: center; border: none; background: transparent; width: 150px;" required>
          <span style="font-size: 1.5rem;">CM</span>
          <p>....................................</p>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
          <a href="index.php" class="btn btn-black">Kembali</a>
          <a href="simpan.php" class="btn btn-black">Simpan</a>
      </div>
    </form>
    </div>

</body>
</html>