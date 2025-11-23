<?php 
include 'config/db.php'; 

// Ambil data untuk grafik
$labels = [];
$data_tinggi = [];

// Ambil data dari tabel data_ukur, urutkan berdasarkan tanggal
$query = mysqli_query($conn, "SELECT * FROM data_ukur ORDER BY tanggal_input ASC LIMIT 20"); 

while($row = mysqli_fetch_array($query)){
    // Gunakan tanggal sebagai label
    $labels[] = date('d/m', strtotime($row['tanggal_input'])); 
    $data_tinggi[] = $row['tinggi'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Statistik Tinggi Badan</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <img src="assets/logo.svg" alt="Logo" style="position: fixed; top: 30px; left: 40px; width: 70px; z-index: 9999; cursor: pointer;">

  <div class="container">
    <h1>Statistik Perkembangan Tinggi Badan</h1>
    
    <canvas id="myChart" width="400" height="200"></canvas>

    <br><br>
    <a href="index.php" class="btn btn-black">Kembali</a>
  </div>

  <script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Tinggi Badan (cm)',
                data: <?php echo json_encode($data_tinggi); ?>,
                borderColor: 'black',
                borderWidth: 2,
                tension: 0.1,
                fill: false,
                pointBackgroundColor: 'black'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
  </script>

</body>
</html>