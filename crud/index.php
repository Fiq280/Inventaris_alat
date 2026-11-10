<?php
include "config.php";
$hasil = mysqli_query($koneksi, "SELECT * FROM alat ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Daftar Alat</title>
</head>
<body>
  <h2>Daftar Alat Medis</h2>
  <a href="add.php">+ Tambah Alat</a>
  <br><br>

  <table border="1" cellpadding="8" cellspacing="0">
    <tr>
      <th>No</th>
      <th>Nama Alat</th>
      <th>Tahun</th>
      <th>Merek</th>
      <th>Lokasi</th>
      <th>Aksi</th>
    </tr>

    <?php $no = 1; while ($row = mysqli_fetch_assoc($hasil)) { ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($row['nama_alat']) ?></td>
      <td><?= $row['tahun'] ?></td>
      <td><?= htmlspecialchars($row['merek']) ?></td>
      <td><?= htmlspecialchars($row['lokasi']) ?></td>
      <td>
        <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Yakin hapus alat ini?')">Hapus</a>
      </td>
    </tr>
    <?php } ?>
  </table>
</body>
</html>