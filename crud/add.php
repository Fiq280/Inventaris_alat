<?php
include "config.php";

if (isset($_POST['simpan'])) {
    $nama   = $_POST['nama_alat'];
    $tahun  = $_POST['tahun'];
    $merek  = $_POST['merek'];
    $lokasi = $_POST['lokasi'];

    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO alat (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "siss", $nama, $tahun, $merek, $lokasi);
    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Alat - Spark Admin</title>
  <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-light p-4">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6">
        <div class="card border-light shadow-sm p-4">
          <h4 class="card-title fw-bold mb-4">Tambah Alat</h4>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label-custom">Nama Alat</label>
              <input type="text" name="nama_alat" class="form-control-custom" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label-custom">Tahun</label>
              <input type="number" name="tahun" min="1990" max="2100" class="form-control-custom" required>
            </div>
            <div class="mb-3">
              <label class="form-label-custom">Merek</label>
              <input type="text" name="merek" class="form-control-custom" required>
            </div>
            <div class="mb-4">
              <label class="form-label-custom">Lokasi</label>
              <input type="text" name="lokasi" maxlength="10" class="form-control-custom" required>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" name="simpan" class="btn-custom btn-custom-primary">Simpan</button>
              <a href="index.php" class="btn-custom btn-custom-outline-secondary text-decoration-none">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
