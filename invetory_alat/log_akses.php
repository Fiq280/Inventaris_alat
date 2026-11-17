<?php
include "auth_check.php";
$judul = "Log Akses";

$cari        = trim($_GET['cari'] ?? '');
$per_halaman = 20;
$halaman     = max(1, (int) ($_GET['hal'] ?? 1));

// Syarat pencarian (opsional)
$where = "";
$tipe  = "";
$param = [];
if ($cari !== '') {
    $where = " WHERE username LIKE ? OR nama_lengkap LIKE ? OR aktivitas LIKE ? OR ip_address LIKE ?";
    $like  = "%" . $cari . "%";
    $tipe  = "ssss";
    $param = [$like, $like, $like, $like];
}

// Hitung total baris untuk pembagian halaman
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS n FROM log_akses" . $where);
if ($param) {
    mysqli_stmt_bind_param($stmt, $tipe, ...$param);
}
mysqli_stmt_execute($stmt);
$total      = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['n'];
$total_hal  = max(1, (int) ceil($total / $per_halaman));
$halaman    = min($halaman, $total_hal);
$offset     = ($halaman - 1) * $per_halaman;

// Ambil data untuk halaman yang sedang dibuka
$sql  = "SELECT * FROM log_akses" . $where . " ORDER BY waktu DESC, id DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($koneksi, $sql);
$param2 = array_merge($param, [$per_halaman, $offset]);
mysqli_stmt_bind_param($stmt, $tipe . "ii", ...$param2);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

// Warna badge Spark Admin sesuai jenis aktivitas
function badge_aktivitas($a) {
    if (strpos($a, 'Gagal') !== false || strpos($a, 'Hapus') === 0) return 'failed';
    if (strpos($a, 'Login') === 0)  return 'success';
    if (strpos($a, 'Logout') === 0) return 'info';
    if (strpos($a, 'Tambah') === 0) return 'primary';
    if (strpos($a, 'Ubah') === 0)   return 'pending';
    return 'info';
}

include "header.php";
?>

<!-- START: Page Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Log Akses & Aktivitas</h1>
    <p class="page-subtitle text-muted">Audit jejak keamanan, autentikasi, serta riwayat perubahan data inventaris.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Log Akses</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<!-- START: Table Card Container -->
<div class="table-card-custom">
  <!-- Search Controls -->
  <form method="GET" class="table-header-control d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <div class="d-flex flex-wrap gap-2 flex-grow-1 align-items-center">
      <div class="table-search-box flex-grow-1" style="max-width: 460px;">
        <i class="bi bi-search table-search-icon"></i>
        <input type="text" name="cari" class="table-search-input"
               placeholder="Cari username, nama, aktivitas, atau IP..."
               value="<?= htmlspecialchars($cari) ?>">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn-custom btn-custom-primary">
          <i class="bi bi-search me-1"></i> Cari Log
        </button>
        <?php if ($cari !== ''): ?>
          <a href="log_akses.php" class="btn-custom btn-custom-outline-secondary text-decoration-none">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="text-muted small">
      Total <strong><?= $total ?></strong> aktivitas tercatat
    </div>
  </form>

  <!-- Responsive Table Wrapper -->
  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th style="min-width: 160px;">Waktu Kejadian</th>
          <th>Username</th>
          <th>Nama Lengkap</th>
          <th>Aktivitas</th>
          <th>Alamat IP</th>
          <th>Perangkat / Browser</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total === 0): ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
              Belum ada riwayat aktivitas yang tercatat.
            </td>
          </tr>
        <?php endif; ?>

        <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
        <tr>
          <td>
            <div class="text-nowrap small text-muted">
              <i class="bi bi-clock me-1 text-primary"></i>
              <?= htmlspecialchars($row['waktu']) ?>
            </div>
          </td>
          <td>
            <strong><?= htmlspecialchars($row['username'] ?? '-') ?></strong>
          </td>
          <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
          <td>
            <span class="badge-table <?= badge_aktivitas($row['aktivitas']) ?>">
              <?= htmlspecialchars($row['aktivitas']) ?>
            </span>
          </td>
          <td>
            <code class="text-dark bg-light px-2 py-1 rounded small"><?= htmlspecialchars($row['ip_address']) ?></code>
          </td>
          <td>
            <span class="small text-muted" title="<?= htmlspecialchars($row['user_agent']) ?>">
              <?= htmlspecialchars(mb_substr($row['user_agent'], 0, 50)) ?>...
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Table Pagination Footer -->
  <?php if ($total_hal > 1): ?>
  <div class="table-footer-control d-flex flex-wrap justify-content-between align-items-center p-3 border-top">
    <div class="text-muted small mb-2 mb-md-0">
      Menampilkan halaman <strong><?= $halaman ?></strong> dari <strong><?= $total_hal ?></strong>
    </div>
    <nav aria-label="Navigasi Halaman">
      <ul class="pagination mb-0">
        <?php if ($halaman > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?<?= http_build_query(['cari' => $cari, 'hal' => $halaman - 1]) ?>" aria-label="Sebelumnya">
              &laquo;
            </a>
          </li>
        <?php endif; ?>

        <?php for ($i = max(1, $halaman - 2); $i <= min($total_hal, $halaman + 2); $i++): ?>
          <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(['cari' => $cari, 'hal' => $i]) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($halaman < $total_hal): ?>
          <li class="page-item">
            <a class="page-link" href="?<?= http_build_query(['cari' => $cari, 'hal' => $halaman + 1]) ?>" aria-label="Berikutnya">
              &raquo;
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
  <?php endif; ?>
</div>
<!-- END: Table Card Container -->

<?php include "footer.php"; ?>
