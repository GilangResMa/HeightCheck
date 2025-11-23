<?php include 'config/db.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tabel Hasil</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <img src="assets/logo.svg" alt="Logo" style="position: fixed; top: 30px; left: 40px; width: 70px; z-index: 9999; cursor: pointer;">

  <div class="container" style="max-width: 900px;">
    <h1>Tabel Hasil</h1>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Umur</th>
                    <th>Jenis Kelamin</th>
                    <th>Tinggi(cm)</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query JOIN untuk mengambil data dari kedua tabel
                $query_sql = "SELECT 
                                u.nama, 
                                u.umur, 
                                u.jenis_kelamin, 
                                d.tinggi, 
                                d.tanggal_input 
                              FROM 
                                data_ukur d
                              JOIN 
                                users u ON d.id = u.id 
                              ORDER BY 
                                d.id_data DESC";
                                
                $query = mysqli_query($conn, $query_sql);
                $no = 1;
                while($row = mysqli_fetch_array($query)){
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= $row['umur']; ?></td>
                    <td><?= $row['jenis_kelamin']; ?></td>
                    <td><?= $row['tinggi']; ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'])); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br><br>
    <a href="index.php" class="btn btn-black">Kembali</a>
  </div>

</body>
</html>