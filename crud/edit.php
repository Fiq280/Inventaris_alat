<?php
include "config.php";

$id = (int) $_GET['id'];

if (isset($_POST['update'])) {
    $nama   = $_POST['nama_alat'];
    $tahun  = $_POST['tahun'];
    $merek  = $_POST['merek'];
    $lokasi = $_POST['lokasi'];

    $stmt = mysqli_prepare($koneksi,
        "UPDATE alat SET nama_alat=?, tahun=?, merek=?, lokasi=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sissi", $nama, $tahun, $merek, $lokasi, $id);
    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM alat WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    die("Data tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Alat - Spark Admin</title>
  <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-light p-4">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6">
        <div class="card border-light shadow-sm p-4">
          <h4 class="card-title fw-bold mb-4">Edit Alat #<?= $id ?></h4>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label-custom">Nama Alat</label>
              <input type="text" name="nama_alat" value="<?= htmlspecialchars($data['nama_alat']) ?>" class="form-control-custom" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label-custom">Tahun</label>
              <input type="number" name="tahun" value="<?= $data['tahun'] ?>" class="form-control-custom" required>
            </div>
            <div class="mb-3">
              <label class="form-label-custom">Merek</label>
              <input type="text" name="merek" value="<?= htmlspecialchars($data['merek']) ?>" class="form-control-custom" required>
            </div>
            <div class="mb-4">
              <label class="form-label-custom">Lokasi</label>
              <input type="text" name="lokasi" value="<?= htmlspecialchars($data['lokasi']) ?>" maxlength="10" class="form-control-custom" required>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" name="update" class="btn-custom btn-custom-primary">Update</button>
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
