<?php
include "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $valid = false;
    if ($user) {
        if (substr($user['password'], 0, 4) === '$2y$') {
            // password sudah BCRYPT
            $valid = password_verify($password, $user['password']);
        } else {
            // password masih MD5, cocokkan lalu upgrade otomatis ke BCRYPT
            $valid = hash_equals($user['password'], md5($password));
            if ($valid) {
                $hashBaru = password_hash($password, PASSWORD_DEFAULT);
                $up = mysqli_prepare($koneksi, "UPDATE users SET password=? WHERE id=?");
                mysqli_stmt_bind_param($up, "si", $hashBaru, $user['id']);
                mysqli_stmt_execute($up);
            }
        }
    }

    if ($valid) {
        session_regenerate_id(true);
        $_SESSION['user_id']      = $user['id'];
        $_SESSION['username']     = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role']         = $user['role'];

        $up = mysqli_prepare($koneksi, "UPDATE users SET terakhir_login=NOW() WHERE id=?");
        mysqli_stmt_bind_param($up, "i", $user['id']);
        mysqli_stmt_execute($up);

        catat_log($koneksi, $user['id'], $user['username'], $user['nama_lengkap'], "Login");
        header("Location: index.php");
        exit;
    } else {
        $uid  = $user ? $user['id'] : null;
        $nama = $user ? $user['nama_lengkap'] : null;
        catat_log($koneksi, $uid, $username, $nama, "Percobaan Login Gagal");
        $error = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - SIMRS Inventaris Alat</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/images/favicon.ico">

  <!-- Local Third-Party Libraries (100% Offline Compatible) -->
  <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/libs/bootstrap-icons/bootstrap-icons.css">

  <!-- Main Design System & Custom Stylesheet -->
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

  <!-- ==========================================
       START: Authentication Container & Login Card
       ========================================== -->
  <div class="login-wrapper">
    <!-- Glowing background shapes for modern visual appearance -->
    <div class="login-bg-shape login-bg-shape-1"></div>
    <div class="login-bg-shape login-bg-shape-2"></div>

    <!-- Main centered login card -->
    <div class="login-card">

      <!-- Brand Identity -->
      <a href="login.php" class="login-brand text-decoration-none">
        <i class="bi bi-heart-pulse-fill"></i>
        <span>Inventaris RS</span>
      </a>

      <p class="login-subtitle">Masuk ke sistem manajemen inventaris alat medis</p>

      <!-- Pesan Sukses (e.g. Setelah Registrasi) -->
      <?php if (!empty($_SESSION['pesan_sukses'])): ?>
        <div class="alert-custom alert-custom-success mb-4">
          <i class="bi bi-check-circle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            <?= htmlspecialchars($_SESSION['pesan_sukses']) ?>
          </div>
          <button class="alert-custom-close" type="button" aria-label="Tutup" onclick="this.parentElement.remove();">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <?php unset($_SESSION['pesan_sukses']); ?>
      <?php endif; ?>

      <!-- Pesan Error -->
      <?php if (!empty($error)): ?>
        <div class="alert-custom alert-custom-danger mb-4">
          <i class="bi bi-exclamation-circle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            <?= htmlspecialchars($error) ?>
          </div>
          <button class="alert-custom-close" type="button" aria-label="Tutup" onclick="this.parentElement.remove();">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form method="POST" id="loginForm" class="needs-validation">

        <!-- Username Input Group -->
        <div class="login-form-group">
          <label for="username" class="login-form-label">Username</label>
          <div class="login-input-group">
            <i class="bi bi-person input-icon"></i>
            <input type="text" id="username" name="username" class="login-input"
                   placeholder="Masukkan username" required autofocus
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          </div>
        </div>

        <!-- Password Input Group -->
        <div class="login-form-group">
          <label for="password" class="login-form-label">Password</label>
          <div class="login-input-group">
            <i class="bi bi-shield-lock input-icon"></i>
            <input type="password" id="password" name="password" class="login-input login-input-password"
                   placeholder="••••••••" required>
            <button type="button" class="password-toggle-btn" id="toggle-password" aria-label="Tampilkan password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" name="login" class="btn-login" id="btn-submit">
          <span>Masuk (Sign In)</span>
          <i class="bi bi-arrow-right"></i>
        </button>

      </form>

      <!-- Navigasi Register -->
      <div class="mt-4 pt-3 border-top text-center">
        <p class="text-muted small mb-0">
          Belum memiliki akun? <a href="register.php" class="text-success fw-bold text-decoration-none">Daftar Akun Baru (Register)</a>
        </p>
      </div>

      <div class="mt-3 text-center">
        <span class="text-muted small">&copy; <?= date('Y') ?> SIMRS Inventaris Alat &bull; Spark Admin Template</span>
      </div>

    </div>
  </div>
  <!-- END: Authentication Container -->

  <!-- Local Bootstrap bundle -->
  <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Custom Authentication interactions script (Password toggle) -->
  <script src="assets/js/auth.js"></script>
</body>
</html>
