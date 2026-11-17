<?php
include "auth_check.php";
$judul = "Edit Alat";

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM alat WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    die("Data alat tidak ditemukan.");
}

$error  = [];
$nama   = $data['nama_alat'];
$tahun  = $data['tahun'];
$merek  = $data['merek'];
$lokasi = $data['lokasi'];

if (isset($_POST['update'])) {
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
        $up = mysqli_prepare($koneksi,
            "UPDATE alat SET nama_alat=?, tahun=?, merek=?, lokasi=? WHERE id=?");
        mysqli_stmt_bind_param($up, "sissi", $nama, $tahun, $merek, $lokasi, $id);
        mysqli_stmt_execute($up);

        catat_log($koneksi, $_SESSION['user_id'], $_SESSION['username'],
                  $_SESSION['nama_lengkap'], mb_substr("Ubah Data: " . $nama, 0, 100));

        $_SESSION['pesan'] = "Data alat berhasil diperbarui.";
        header("Location: index.php");
        exit;
    }
}

include "header.php";
?>

<!-- START: Page Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Edit Alat Medis</h1>
    <p class="page-subtitle text-muted">Perbarui informasi spesifikasi atau lokasi alat #ALT-<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?>.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Edit Alat</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<?php if (!empty($error)): ?>
  <div class="alert-custom alert-custom-danger mb-4">
    <i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
    <div class="alert-custom-content">
      <strong>Terjadi Kesalahan:</strong>
      <ul class="mb-0 mt-1 ps-3">
        <?php foreach ($error as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <button class="alert-custom-close" type="button" aria-label="Tutup" onclick="this.parentElement.remove();">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-12 col-lg-8 col-xl-6">
    <div class="card border-light shadow-sm p-4">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="card-title fw-bold mb-0">
          <i class="bi bi-pencil-square me-2 text-primary"></i>Ubah Data Alat
        </h5>
        <span class="badge-table info">#ALT-<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></span>
      </div>

      <form method="POST">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <div class="mb-3">
          <label class="form-label-custom">Nama Alat Medis <span class="text-danger">*</span></label>
          <input type="text" name="nama_alat" class="form-control-custom"
                 value="<?= htmlspecialchars($nama) ?>" required autofocus>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label-custom">Tahun Pengadaan <span class="text-danger">*</span></label>
            <input type="number" name="tahun" class="form-control-custom" min="1990" max="<?= (int)date('Y') + 1 ?>"
                   value="<?= $tahun ?: '' ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-custom">Merek / Pabrikan <span class="text-danger">*</span></label>
            <input type="text" name="merek" class="form-control-custom"
                   value="<?= htmlspecialchars($merek) ?>" required>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label-custom">Lokasi / Ruangan Penempatan <span class="text-danger">*</span></label>
          <select name="lokasi" class="form-select-custom" required>
            <option value="">-- Pilih Lokasi Ruangan --</option>
            <?php foreach (LOKASI_ALAT as $l): ?>
              <option value="<?= $l ?>" <?= $lokasi === $l ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="d-flex align-items-center gap-2 pt-2">
          <button type="submit" name="update" class="btn-custom btn-custom-primary">
            <i class="bi bi-check-lg me-1"></i> Perbarui Data
          </button>
          <a href="index.php" class="btn-custom btn-custom-outline-secondary text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Batal
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
