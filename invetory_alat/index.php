<?php
include "auth_check.php";
$judul = "Dashboard";

$cari   = trim($_GET['cari'] ?? '');
$lokasi = trim($_GET['lokasi'] ?? '');

// Susun query sesuai pencarian dan filter yang diisi
$sql = "SELECT * FROM alat WHERE 1=1";
$tipe = "";
$param = [];

if ($cari !== '') {
    $sql .= " AND (nama_alat LIKE ? OR merek LIKE ?)";
    $like = "%" . $cari . "%";
    $tipe .= "ss";
    $param[] = $like;
    $param[] = $like;
}
if ($lokasi !== '') {
    $sql .= " AND lokasi = ?";
    $tipe .= "s";
    $param[] = $lokasi;
}
$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if ($param) {
    mysqli_stmt_bind_param($stmt, $tipe, ...$param);
}
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$total = mysqli_num_rows($hasil);

include "header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Daftar Alat Medis</h3>
  <a href="add.php" class="btn btn-primary">+ Tambah Alat</a>
</div>

<?php if (!empty($_SESSION['pesan'])): ?>
  <div class="alert alert-success py-2"><?= htmlspecialchars($_SESSION['pesan']) ?></div>
  <?php unset($_SESSION['pesan']); ?>
<?php endif; ?>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-5">
    <input type="text" name="cari" class="form-control"
           placeholder="Cari nama alat atau merek..." value="<?= htmlspecialchars($cari) ?>">
  </div>
  <div class="col-md-3">
    <select name="lokasi" class="form-select">
      <option value="">Semua ruangan</option>
      <?php foreach (LOKASI_ALAT as $l): ?>
        <option value="<?= $l ?>" <?= $lokasi === $l ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4">
    <button type="submit" class="btn btn-secondary">Cari</button>
    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
  </div>
</form>

<p class="text-muted">Menampilkan <?= $total ?> alat</p>

<div class="table-responsive">
  <table class="table table-bordered table-hover bg-white align-middle">
    <thead class="table-light">
      <tr>
        <th>Kode</th>
        <th>Nama Alat</th>
        <th>Tahun</th>
        <th>Merek</th>
        <th>Lokasi</th>
        <th style="width:160px">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total === 0): ?>
        <tr><td colspan="6" class="text-center text-muted">Data tidak ditemukan.</td></tr>
      <?php endif; ?>

      <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
      <tr>
        <td>#ALT-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
        <td><?= htmlspecialchars($row['nama_alat']) ?></td>
        <td><span class="badge text-bg-info"><?= $row['tahun'] ?></span></td>
        <td><?= htmlspecialchars($row['merek']) ?></td>
        <td><span class="badge text-bg-primary"><?= htmlspecialchars($row['lokasi']) ?></span></td>
        <td>
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <form action="delete.php" method="POST" class="d-inline"
                onsubmit="return confirm('Yakin hapus alat ini?')">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include "footer.php"; ?>