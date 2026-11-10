<?php
include "auth_check.php";
$judul = "Tambah Alat";

$error = [];
$nama = $merek = $lokasi = "";
$tahun = "";

if (isset($_POST['simpan'])) {
    if (!csrf_valid($_POST['csrf'] ?? '')) {
        die("Token tidak valid. Muat ulang halaman.");
    }

    $nama   = trim($_POST['nama_alat'] ?? '');
    $tahun  = (int) ($_POST['tahun'] ?? 0);
    $merek  = trim($_POST['merek'] ?? '');
    $lokasi = $_POST['lokasi'] ?? '';

    if ($nama === '' || mb_strlen($nama) > 100) {
        $error[] = "Nama alat wajib diisi (maksimal 100 karakter).";
    }
    if ($tahun < 1990 || $tahun > (int) date('Y') + 1) {
        $error[] = "Tahun tidak valid.";
    }
    if ($merek === '' || mb_strlen($merek) > 60) {
        $error[] = "Merek wajib diisi (maksimal 60 karakter).";
    }
    if (!in_array($lokasi, LOKASI_ALAT, true)) {
        $error[] = "Pilih lokasi yang tersedia.";
    }

    if (!$error) {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO alat (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "siss", $nama, $tahun, $merek, $lokasi);
        mysqli_stmt_execute($stmt);

        catat_log($koneksi, $_SESSION['user_id'], $_SESSION['username'],
                  $_SESSION['nama_lengkap'], mb_substr("Tambah Data: " . $nama, 0, 100));

        $_SESSION['pesan'] = "Alat berhasil ditambahkan.";
        header("Location: index.php");
        exit;
    }
}

include "header.php";
?>

<h3 class="mb-3">Tambah Alat</h3>

<?php foreach ($error as $e): ?>
  <div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="card" style="max-width:520px">
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

      <div class="mb-3">
        <label class="form-label">Nama Alat</label>
        <input type="text" name="nama_alat" class="form-control"
               value="<?= htmlspecialchars($nama) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Tahun Pengadaan</label>
        <input type="number" name="tahun" class="form-control" min="1990"
               value="<?= $tahun ?: '' ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Merek / Pabrikan</label>
        <input type="text" name="merek" class="form-control"
               value="<?= htmlspecialchars($merek) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Lokasi / Ruangan</label>
        <select name="lokasi" class="form-select" required>
          <option value="">-- Pilih --</option>
          <?php foreach (LOKASI_ALAT as $l): ?>
            <option value="<?= $l ?>" <?= $lokasi === $l ? 'selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<?php include "footer.php"; ?>