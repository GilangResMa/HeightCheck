<?php 
include 'config/db.php'; 

// Pagination setup
$limit = 10; // Batasi 10 data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Minimal halaman 1
$offset = ($page - 1) * $limit;

// Hitung total data
$count_query = "SELECT COUNT(*) as total FROM data_ukur d JOIN users u ON d.id = u.id";
$count_result = mysqli_query($conn, $count_query);
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tabel Hasil</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      padding: 10px;
      height: 100vh;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      max-width: 1200px;
      width: 100%;
      height: 95vh;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }
    h1 {
      font-size: 1.5rem;
      margin-bottom: 10px;
      margin-top: 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      border-radius: 8px;
      overflow: hidden;
      font-size: 0.85rem;
      flex-shrink: 0;
    }
    th, td {
      padding: 8px 10px;
      text-align: left;
      border-bottom: 1px solid #f0f0f0;
      white-space: nowrap;
    }
    th {
      background: #4a90e2;
      color: white;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.3px;
      padding: 10px;
    }
    tr:hover {
      background: #f8f9fa;
    }
    tr:last-child td {
      border-bottom: none;
    }
    td {
      color: #333;
      font-size: 0.85rem;
    }
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 6px;
      margin: 15px 0 10px;
      flex-wrap: wrap;
      flex-shrink: 0;
    }
    .pagination a, .pagination span {
      padding: 6px 10px;
      border-radius: 6px;
      text-decoration: none;
      color: #4a90e2;
      background: white;
      border: 1px solid #e0e0e0;
      transition: all 0.3s;
      font-weight: 500;
      min-width: 35px;
      text-align: center;
      font-size: 0.85rem;
    }
    .pagination a:hover {
      background: #4a90e2;
      color: white;
      border-color: #4a90e2;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(74, 144, 226, 0.3);
    }
    .pagination .active {
      background: #4a90e2;
      color: white;
      border-color: #4a90e2;
      font-weight: 600;
    }
    .pagination .disabled {
      opacity: 0.5;
      pointer-events: none;
    }
    .info-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      padding: 8px 12px;
      background: #f8f9fa;
      border-radius: 8px;
      flex-wrap: wrap;
      gap: 10px;
      flex-shrink: 0;
    }
    .info-text {
      color: #666;
      font-size: 0.8rem;
    }
    .btn-container {
      margin-top: 10px;
      flex-shrink: 0;
    }
    .btn {
      padding: 8px 20px;
      font-size: 0.9rem;
    }
    .no-data {
      text-align: center;
      padding: 30px 20px;
      color: #999;
      font-size: 0.95rem;
    }
    @media (max-width: 768px) {
      body { padding: 5px; }
      .container { padding: 10px; height: 98vh; }
      h1 { font-size: 1.2rem; margin-bottom: 8px; }
      th, td { padding: 6px; font-size: 0.75rem; }
      .info-text { font-size: 0.7rem; }
      .pagination a, .pagination span { padding: 5px 8px; font-size: 0.75rem; min-width: 30px; }
    }
  </style>
</head>
<body>

  <img src="assets/logo.svg" alt="Logo" style="position: fixed; top: 15px; left: 15px; width: 50px; z-index: 9999; cursor: pointer;" onclick="location.href='index.php'">

  <div class="container">
    <h1>📊 Tabel Hasil Pengukuran</h1>
    
    <?php if ($total_data > 0): ?>
    <div class="info-bar">
      <span class="info-text">
        📝 Menampilkan <strong><?= min($offset + 1, $total_data) ?> - <?= min($offset + $limit, $total_data) ?></strong> dari <strong><?= $total_data ?></strong> data
      </span>
      <span class="info-text">Halaman <strong><?= $page ?></strong> dari <strong><?= $total_pages ?></strong></span>
    </div>
    <?php endif; ?>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Umur</th>
                    <th>Jenis Kelamin</th>
                    <th>Tinggi (cm)</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query JOIN dengan LIMIT untuk pagination
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
                                d.id_data DESC
                              LIMIT $limit OFFSET $offset";
                                
                $query = mysqli_query($conn, $query_sql);
                
                // Cek jika query gagal
                if (!$query) {
                    echo "<tr><td colspan='6' class='no-data'>❌ Error: " . mysqli_error($conn) . "</td></tr>";
                } else {
                    // Cek jika ada data
                    if (mysqli_num_rows($query) > 0) {
                        $no = $offset + 1; // Nomor urut berdasarkan halaman
                        while($row = mysqli_fetch_array($query)){
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= $row['umur']; ?> tahun</td>
                    <td><?= $row['jenis_kelamin']; ?></td>
                    <td><strong><?= $row['tinggi']; ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'])); ?></td>
                </tr>
                <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='no-data'>📭 Belum ada data pengukuran</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <!-- Tombol Previous -->
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">← Previous</a>
      <?php else: ?>
        <span class="disabled">← Previous</span>
      <?php endif; ?>
      
      <!-- Nomor halaman -->
      <?php
      // Tampilkan max 5 nomor halaman
      $start_page = max(1, $page - 2);
      $end_page = min($total_pages, $page + 2);
      
      if ($start_page > 1) {
        echo '<a href="?page=1">1</a>';
        if ($start_page > 2) echo '<span>...</span>';
      }
      
      for ($i = $start_page; $i <= $end_page; $i++):
      ?>
        <?php if ($i == $page): ?>
          <span class="active"><?= $i ?></span>
        <?php else: ?>
          <a href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
      <?php 
      endfor;
      
      if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) echo '<span>...</span>';
        echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
      }
      ?>
      
      <!-- Tombol Next -->
      <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next →</a>
      <?php else: ?>
        <span class="disabled">Next →</span>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="btn-container">
      <a href="index.php" class="btn">⬅ Kembali</a>
    </div>
  </div>

</body>
</html>