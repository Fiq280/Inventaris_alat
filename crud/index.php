<?php
include "config.php";
$hasil = mysqli_query($koneksi, "SELECT * FROM alat ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Alat - Spark Admin</title>
  <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-light p-4">
  <div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="page-title">Daftar Alat Medis (Simple CRUD)</h1>
        <p class="page-subtitle text-muted">Halaman modul CRUD dasar.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="../invetory_alat/index.php" class="btn-custom btn-custom-outline-secondary text-decoration-none">
          <i class="bi bi-grid-fill me-1"></i> Dashboard Utama
        </a>
        <a href="add.php" class="btn-custom btn-custom-primary text-decoration-none">
          <i class="bi bi-plus-lg me-1"></i> Tambah Alat
        </a>
      </div>
    </div>

    <div class="table-card-custom">
      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th style="width: 60px;">No</th>
              <th>Nama Alat</th>
              <th>Tahun</th>
              <th>Merek</th>
              <th>Lokasi</th>
              <th class="text-center" style="width: 120px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($hasil)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td class="table-product-name"><?= htmlspecialchars($row['nama_alat']) ?></td>
              <td><span class="badge bg-light text-dark border"><?= $row['tahun'] ?></span></td>
              <td><?= htmlspecialchars($row['merek']) ?></td>
              <td><span class="badge-table success"><?= htmlspecialchars($row['lokasi']) ?></span></td>
              <td>
                <div class="d-flex justify-content-center gap-1">
                  <a href="edit.php?id=<?= $row['id'] ?>" class="table-btn-action" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="delete.php?id=<?= $row['id'] ?>" class="table-btn-action delete"
                     onclick="return confirm('Yakin hapus alat ini?')" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
