<?php
include "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Inventory Alat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #eaf4fb; }
    .kartu-login { max-width: 400px; margin: 10vh auto; border: 0; border-radius: 16px; }
    .btn-sky { background: #4aa3df; color: #fff; }
    .btn-sky:hover { background: #3a8fc9; color: #fff; }
  </style>
</head>
<body>
  <div class="card kartu-login shadow">
    <div class="card-body p-4">
      <h4 class="text-center mb-1">Inventory Alat Medis</h4>
      <p class="text-center text-muted mb-4">Silakan login</p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-sky w-100">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>