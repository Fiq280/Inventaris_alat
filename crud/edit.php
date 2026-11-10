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
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Alat</title>
</head>
<body>
  <h2>Edit Alat</h2>
  <form method="POST">
    Nama Alat: <br>
    <input type="text" name="nama_alat" value="<?= htmlspecialchars($data['nama_alat']) ?>" required><br><br>

    Tahun: <br>
    <input type="number" name="tahun" value="<?= $data['tahun'] ?>" required><br><br>

    Merek: <br>
    <input type="text" name="merek" value="<?= htmlspecialchars($data['merek']) ?>" required><br><br>

    Lokasi: <br>
    <input type="text" name="lokasi" value="<?= htmlspecialchars($data['lokasi']) ?>" maxlength="10" required><br><br>

    <button type="submit" name="update">Update</button>
    <a href="index.php">Batal</a>
  </form>
</body>
</html>