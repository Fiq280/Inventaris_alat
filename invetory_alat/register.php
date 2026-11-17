<?php
include "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = [];
$nama_lengkap = "";
$username = "";
$role = "Petugas";

if (isset($_POST['register'])) {
    $nama_lengkap        = trim($_POST['nama_lengkap'] ?? '');
    $username            = trim($_POST['username'] ?? '');
    $role                = trim($_POST['role'] ?? 'Petugas');
    $password            = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Validasi Nama Lengkap
    if ($nama_lengkap === '' || mb_strlen($nama_lengkap) > 100) {
        $error[] = "Nama lengkap wajib diisi (maksimal 100 karakter).";
    }

    // Validasi Username
    if ($username === '' || mb_strlen($username) < 3 || mb_strlen($username) > 50) {
        $error[] = "Username minimal 3 karakter dan maksimal 50 karakter.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error[] = "Username hanya boleh memuat huruf, angka, dan underscore (_).";
    } else {
        // Cek keunikan username
        $cek = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        if (mysqli_stmt_num_rows($cek) > 0) {
            $error[] = "Username '{$username}' sudah digunakan. Silakan pilih username lain.";
        }
        mysqli_stmt_close($cek);
    }

    // Validasi Role
    $daftar_role = ['Petugas', 'Teknisi Medis', 'Staf Administrasi', 'Admin Ruangan'];
    if (!in_array($role, $daftar_role, true)) {
        $role = 'Petugas';
    }

    // Validasi Password
    if (strlen($password) < 6) {
        $error[] = "Password minimal 6 karakter.";
    }
    if ($password !== $konfirmasi_password) {
        $error[] = "Konfirmasi password tidak cocok.";
    }

    // Simpan ke database jika valid
    if (empty($error)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO users (username, password, nama_lengkap, role, terakhir_login) VALUES (?, ?, ?, ?, NULL)");
        mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hash, $nama_lengkap, $role);

        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($koneksi);
            catat_log($koneksi, $new_id, $username, $nama_lengkap, "Registrasi Akun Baru ({$role})");

            $_SESSION['pesan_sukses'] = "Pendaftaran berhasil! Akun Anda telah siap, silakan Sign In.";
            header("Location: login.php");
            exit;
        } else {
            $error[] = "Gagal mendaftarkan akun. Silakan coba beberapa saat lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - SIMRS Inventaris Alat</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/images/favicon.ico">

  <!-- Local Third-Party Libraries (100% Offline Compatible) -->
  <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/libs/bootstrap-icons/bootstrap-icons.css">

  <!-- Main Design System & Custom Stylesheet -->
  <link rel="stylesheet" href="assets/css/main.css">
  <style>
    .login-card {
      max-width: 480px;
    }
  </style>
</head>
<body>

  <!-- ==========================================
       START: Authentication Container & Register Card
       ========================================== -->
  <div class="login-wrapper">
    <!-- Glowing background shapes for modern visual appearance -->
    <div class="login-bg-shape login-bg-shape-1"></div>
    <div class="login-bg-shape login-bg-shape-2"></div>

    <!-- Main centered register card -->
    <div class="login-card">

      <!-- Brand Identity -->
      <a href="login.php" class="login-brand text-decoration-none">
        <i class="bi bi-heart-pulse-fill"></i>
        <span>Inventaris RS</span>
      </a>

      <p class="login-subtitle">Pendaftaran akun baru inventaris alat medis</p>

      <?php if (!empty($error)): ?>
        <div class="alert-custom alert-custom-danger mb-4">
          <i class="bi bi-exclamation-circle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            <strong>Pendaftaran gagal:</strong>
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

      <!-- Register Form -->
      <form method="POST" id="registerForm" class="needs-validation">

        <!-- Nama Lengkap -->
        <div class="login-form-group">
          <label for="nama_lengkap" class="login-form-label">Nama Lengkap</label>
          <div class="login-input-group">
            <i class="bi bi-person-vcard input-icon"></i>
            <input type="text" id="nama_lengkap" name="nama_lengkap" class="login-input"
                   placeholder="Contoh: dr. Budi Santoso / Siti Rahma"
                   value="<?= htmlspecialchars($nama_lengkap) ?>" required autofocus>
          </div>
        </div>

        <!-- Username -->
        <div class="login-form-group">
          <label for="username" class="login-form-label">Username</label>
          <div class="login-input-group">
            <i class="bi bi-person input-icon"></i>
            <input type="text" id="username" name="username" class="login-input"
                   placeholder="Huruf, angka, atau underscore"
                   value="<?= htmlspecialchars($username) ?>" required>
          </div>
        </div>

        <!-- Role / Bagian -->
        <div class="login-form-group">
          <label for="role" class="login-form-label">Role / Bagian Penugasan</label>
          <div class="login-input-group">
            <i class="bi bi-shield-check input-icon"></i>
            <select id="role" name="role" class="login-input" style="padding-left: 2.75rem;">
              <option value="Petugas" <?= $role === 'Petugas' ? 'selected' : '' ?>>Petugas Inventaris</option>
              <option value="Teknisi Medis" <?= $role === 'Teknisi Medis' ? 'selected' : '' ?>>Teknisi Medis</option>
              <option value="Staf Administrasi" <?= $role === 'Staf Administrasi' ? 'selected' : '' ?>>Staf Administrasi</option>
              <option value="Admin Ruangan" <?= $role === 'Admin Ruangan' ? 'selected' : '' ?>>Admin Ruangan</option>
            </select>
          </div>
        </div>

        <!-- Password -->
        <div class="login-form-group">
          <label for="password" class="login-form-label">Password</label>
          <div class="login-input-group">
            <i class="bi bi-shield-lock input-icon"></i>
            <input type="password" id="password" name="password" class="login-input"
                   placeholder="Minimal 6 karakter" required>
            <button type="button" class="password-toggle-btn" id="toggle-password" aria-label="Tampilkan password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="login-form-group">
          <label for="konfirmasi_password" class="login-form-label">Konfirmasi Password</label>
          <div class="login-input-group">
            <i class="bi bi-shield-check input-icon"></i>
            <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="login-input"
                   placeholder="Ulangi password di atas" required>
            <button type="button" class="password-toggle-btn" id="toggle-password-confirm" aria-label="Tampilkan password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" name="register" class="btn-login mt-2" id="btn-submit">
          <span>Daftar Akun (Register)</span>
          <i class="bi bi-arrow-right"></i>
        </button>

      </form>

      <!-- Navigasi ke Sign In -->
      <div class="mt-4 pt-3 border-top text-center">
        <p class="text-muted small mb-0">
          Sudah memiliki akun? <a href="login.php" class="text-success fw-bold text-decoration-none">Masuk di sini (Sign In)</a>
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

  <!-- Script Toggle Password -->
  <script>
    function setupToggle(btnId, inputId) {
      const btn = document.getElementById(btnId);
      const input = document.getElementById(inputId);
      if (btn && input) {
        btn.addEventListener('click', function () {
          const isPassword = input.getAttribute('type') === 'password';
          input.setAttribute('type', isPassword ? 'text' : 'password');
          const icon = btn.querySelector('i');
          if (icon) {
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
          }
        });
      }
    }
    setupToggle('toggle-password', 'password');
    setupToggle('toggle-password-confirm', 'konfirmasi_password');
  </script>
</body>
</html>
