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

// Warna badge sesuai jenis aktivitas
function warna_aktivitas($a) {
    if (strpos($a, 'Gagal') !== false || strpos($a, 'Hapus') === 0) return 'danger';
    if (strpos($a, 'Login') === 0)  return 'success';
    if (strpos($a, 'Logout') === 0) return 'secondary';
    if (strpos($a, 'Tambah') === 0) return 'primary';
    if (strpos($a, 'Ubah') === 0)   return 'warning';
    return 'light';
}

include "header.php";
?>

<h3 class="mb-3">Log Akses Sistem</h3>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-6">
    <input type="text" name="cari" class="form-control"
           placeholder="Cari username, nama, aktivitas, atau IP..."
           value="<?= htmlspecialchars($cari) ?>">
  </div>
  <div class="col-md-6">
    <button type="submit" class="btn btn-secondary">Cari</button>
    <a href="log_akses.php" class="btn btn-outline-secondary">Reset</a>
  </div>
</form>

<p class="text-muted">Total <?= $total ?> catatan</p>

<div class="table-responsive">
  <table class="table table-bordered table-hover bg-white align-middle">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Username</th>
        <th>Nama Lengkap</th>
        <th>Aktivitas</th>
        <th>IP</th>
        <th>Perangkat / Browser</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total === 0): ?>
        <tr><td colspan="6" class="text-center text-muted">Belum ada catatan.</td></tr>
      <?php endif; ?>

      <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
      <tr>
        <td class="text-nowrap"><?= htmlspecialchars($row['waktu']) ?></td>
        <td><?= htmlspecialchars($row['username'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
        <td>
          <span class="badge text-bg-<?= warna_aktivitas($row['aktivitas']) ?>">
            <?= htmlspecialchars($row['aktivitas']) ?>
          </span>
        </td>
        <td><?= htmlspecialchars($row['ip_address']) ?></td>
        <td class="small text-muted" title="<?= htmlspecialchars($row['user_agent']) ?>">
          <?= htmlspecialchars(mb_substr($row['user_agent'], 0, 60)) ?>...
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php if ($total_hal > 1): ?>
<nav>
  <ul class="pagination">
    <?php for ($i = max(1, $halaman - 2); $i <= min($total_hal, $halaman + 2); $i++): ?>
      <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
        <a class="page-link" href="?<?= http_build_query(['cari' => $cari, 'hal' => $i]) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

<?php include "footer.php"; ?>