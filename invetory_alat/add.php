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

<!-- START: Page Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Tambah Alat Medis</h1>
    <p class="page-subtitle text-muted">Daftarkan instrumen atau peralatan medis baru ke inventaris.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Tambah Alat</li>
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
      <h5 class="card-title fw-bold mb-4">
        <i class="bi bi-file-earmark-plus me-2 text-success"></i>Formulir Data Alat
      </h5>

      <form method="POST">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <div class="mb-3">
          <label class="form-label-custom">Nama Alat Medis <span class="text-danger">*</span></label>
          <input type="text" name="nama_alat" class="form-control-custom"
                 placeholder="Contoh: USG Mindray DC-30, Defibrillator..."
                 value="<?= htmlspecialchars($nama) ?>" required autofocus>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label-custom">Tahun Pengadaan <span class="text-danger">*</span></label>
            <input type="number" name="tahun" class="form-control-custom" min="1990" max="<?= (int)date('Y') + 1 ?>"
                   placeholder="Tahun (min 1990)"
                   value="<?= $tahun ?: '' ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-custom">Merek / Pabrikan <span class="text-danger">*</span></label>
            <input type="text" name="merek" class="form-control-custom"
                   placeholder="Contoh: Philips, GE, Mindray..."
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
          <button type="submit" name="simpan" class="btn-custom btn-custom-primary">
            <i class="bi bi-check-lg me-1"></i> Simpan Data
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
