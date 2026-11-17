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

// Summary metrics (hanya membaca data tanpa mengubah apapun)
$total_alat_row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM alat"));
$total_semua_alat = $total_alat_row['jml'] ?? 0;

$total_log_row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM log_akses"));
$total_semua_log = $total_log_row['jml'] ?? 0;

include "header.php";
?>

<!-- START: Page Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Dashboard Inventaris</h1>
    <p class="page-subtitle text-muted">Kelola data peralatan medis rumah sakit dengan mudah, aman, dan presisi.</p>
  </div>
  <div>
    <a href="add.php" class="btn-custom btn-custom-primary text-decoration-none">
      <i class="bi bi-plus-lg me-1"></i> Tambah Alat
    </a>
  </div>
</div>
<!-- END: Page Header Banner -->

<!-- Notifikasi Sukses -->
<?php if (!empty($_SESSION['pesan'])): ?>
  <div class="alert-custom alert-custom-success mb-4">
    <i class="bi bi-check-circle-fill alert-custom-icon"></i>
    <div class="alert-custom-content">
      <strong>Sukses!</strong> <?= htmlspecialchars($_SESSION['pesan']) ?>
    </div>
    <button class="alert-custom-close" type="button" aria-label="Tutup" onclick="this.parentElement.remove();">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
  <?php unset($_SESSION['pesan']); ?>
<?php endif; ?>

<!-- START: Quick Info Stat Cards Row -->
<div class="row g-4 mb-4">
  <!-- Stat 1: Total Alat -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-light shadow-sm p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-muted fw-semibold small text-uppercase">Total Alat Medis</span>
        <div class="bg-success-subtle text-success p-2 rounded-circle">
          <i class="bi bi-tools fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1"><?= $total_semua_alat ?></h3>
      <span class="text-muted small">Peralatan terdaftar</span>
    </div>
  </div>

  <!-- Stat 2: Hasil Filter -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-light shadow-sm p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-muted fw-semibold small text-uppercase">Tampil di Tabel</span>
        <div class="bg-primary-subtle text-primary p-2 rounded-circle">
          <i class="bi bi-search fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1"><?= $total ?></h3>
      <span class="text-muted small">Hasil pencarian/filter</span>
    </div>
  </div>

  <!-- Stat 3: Lokasi Ruangan -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-light shadow-sm p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-muted fw-semibold small text-uppercase">Lokasi Ruangan</span>
        <div class="bg-info-subtle text-info p-2 rounded-circle">
          <i class="bi bi-hospital fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1"><?= count(LOKASI_ALAT) ?></h3>
      <span class="text-muted small">Ruangan tersedia</span>
    </div>
  </div>

  <!-- Stat 4: Total Log Akses -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-light shadow-sm p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-muted fw-semibold small text-uppercase">Log Akses</span>
        <div class="bg-warning-subtle text-warning p-2 rounded-circle">
          <i class="bi bi-activity fs-5"></i>
        </div>
      </div>
      <h3 class="fw-bold mb-1"><?= $total_semua_log ?></h3>
      <span class="text-muted small">Aktivitas tercatat</span>
    </div>
  </div>
</div>
<!-- END: Quick Info Stat Cards Row -->

<!-- START: Table Card Container -->
<div class="table-card-custom">
  <!-- Search & Filter Controls -->
  <form method="GET" class="table-header-control d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <div class="d-flex flex-wrap gap-2 flex-grow-1 align-items-center">
      <div class="table-search-box flex-grow-1" style="max-width: 380px;">
        <i class="bi bi-search table-search-icon"></i>
        <input type="text" name="cari" class="table-search-input"
               placeholder="Cari nama alat atau merek..." value="<?= htmlspecialchars($cari) ?>">
      </div>
      <div style="min-width: 180px;">
        <select name="lokasi" class="form-select-custom w-100">
          <option value="">Semua Ruangan</option>
          <?php foreach (LOKASI_ALAT as $l): ?>
            <option value="<?= $l ?>" <?= $lokasi === $l ? 'selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn-custom btn-custom-primary">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
        <?php if ($cari !== '' || $lokasi !== ''): ?>
          <a href="index.php" class="btn-custom btn-custom-outline-secondary text-decoration-none">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="text-muted small">
      Menampilkan <strong><?= $total ?></strong> alat
    </div>
  </form>

  <!-- Responsive Table Wrapper -->
  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama Alat</th>
          <th>Tahun</th>
          <th>Merek / Pabrikan</th>
          <th>Lokasi Ruangan</th>
          <th class="text-center" style="width: 120px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total === 0): ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
              Data alat tidak ditemukan.
            </td>
          </tr>
        <?php endif; ?>

        <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
        <tr>
          <td class="table-order-id">
            <span class="badge-table info">#ALT-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="p-2 rounded bg-light text-success me-2">
                <i class="bi bi-heart-pulse"></i>
              </div>
              <span class="table-product-name"><?= htmlspecialchars($row['nama_alat']) ?></span>
            </div>
          </td>
          <td>
            <span class="badge bg-light text-dark border"><?= $row['tahun'] ?></span>
          </td>
          <td><?= htmlspecialchars($row['merek']) ?></td>
          <td>
            <span class="badge-table success"><?= htmlspecialchars($row['lokasi']) ?></span>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <a href="edit.php?id=<?= $row['id'] ?>" class="table-btn-action" title="Edit Alat">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="delete.php" method="POST" class="d-inline m-0 p-0"
                    onsubmit="return confirm('Yakin ingin menghapus alat <?= htmlspecialchars(addslashes($row['nama_alat'])) ?>?')">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <button type="submit" class="table-btn-action delete border-0 bg-transparent p-0" title="Hapus Alat">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<!-- END: Table Card Container -->

<?php include "footer.php"; ?>
