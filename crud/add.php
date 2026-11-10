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
<html>
<head>
  <meta charset="UTF-8">
  <title>Tambah Alat</title>
</head>
<body>
  <h2>Tambah Alat</h2>
  <form method="POST">
    Nama Alat: <br>
    <input type="text" name="nama_alat" required><br><br>

    Tahun: <br>
    <input type="number" name="tahun" min="1990" max="2100" required><br><br>

    Merek: <br>
    <input type="text" name="merek" required><br><br>

    Lokasi: <br>
    <input type="text" name="lokasi" maxlength="10" required><br><br>

    <button type="submit" name="simpan">Simpan</button>
    <a href="index.php">Batal</a>
  </form>
</body>
</html>