<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $judul ?? "Inventory Alat" ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand navbar-dark navbar-sky mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Inventory Alat</a>
    <div class="navbar-nav me-auto">
      <a class="nav-link" href="index.php">Dashboard</a>
      <a class="nav-link" href="log_akses.php">Log Akses</a>
    </div>
    <span class="navbar-text me-3"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></span>
    <a class="btn btn-light btn-sm" href="logout.php">Logout</a>
  </div>
</nav>
<div class="container">