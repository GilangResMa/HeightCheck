<?php 
include 'config/db.php'; 

// Variabel untuk menampung data grafik
$labels = [];
$data_rata = [];

// --- QUERY SQL (Dimodifikasi) ---
// Kita gunakan GROUP BY untuk mengelompokkan data berdasarkan tanggal
// Fungsi AVG() digunakan untuk menghitung rata-rata tinggi pada tanggal tersebut
$sql = "SELECT 
            DATE(tanggal_input) as tanggal, 
            AVG(tinggi) as rata_tinggi 
        FROM data_ukur 
        GROUP BY DATE(tanggal_input) 
        ORDER BY tanggal ASC 
        LIMIT 7"; // Menampilkan 7 hari terakhir agar grafik tidak terlalu padat

$query = mysqli_query($conn, $sql);

while($row = mysqli_fetch_array($query)){
    // Format tanggal menjadi 'dd/mm' (contoh: 21/11)
    $labels[] = date('d/m', strtotime($row['tanggal']));
    
    // Bulatkan rata-rata menjadi 1 angka di belakang koma (contoh: 165.5)
    $data_rata[] = round($row['rata_tinggi'], 1);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Statistik Tinggi Badan</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
      /* Sedikit styling tambahan khusus halaman ini */
      .chart-container {
          position: relative; 
          height: 300px; 
          width: 100%;
          margin-top: 20px;
      }
  </style>
</head>
<body>

  <img src="assets/logo.svg" alt="Logo" style="position: fixed; top: 30px; left: 40px; width: 70px; z-index: 9999; cursor: pointer;" onclick="location.href='index.php'">

  <div class="container" style="max-width: 600px;"> <h1>Statistik Rata-rata Harian</h1>
    <p class="desc">Grafik perkembangan rata-rata tinggi badan per hari.</p>
    
    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>

    <br><br>
    <a href="index.php" class="btn btn-black">Kembali ke Menu</a>
  </div>

  <script>
    const ctx = document.getElementById('myChart').getContext('2d');
    
    const myChart = new Chart(ctx, {
        type: 'bar', // TIPE GRAFIK: Batang
        data: {
            labels: <?php echo json_encode($labels); ?>, // Label Tanggal
            datasets: [{
                label: 'Rata-rata Tinggi (cm)',
                data: <?php echo json_encode($data_rata); ?>, // Data Rata-rata
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)', // Warna Biru Transparan
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(54, 162, 235, 0.6)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)', // Warna Garis Tepi Biru Solid
                    'rgba(54, 162, 235, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1,
                borderRadius: 5, // Membuat sudut batang sedikit melengkung
                barPercentage: 0.6 // Mengatur lebar batang
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false, // Supaya grafik lebih fokus pada perbedaan tinggi
                    title: {
                        display: true,
                        text: 'Tinggi (cm)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rata-rata: ' + context.parsed.y + ' cm';
                        }
                    }
                }
            }
        }
    });
  </script>

</body>
</html>