<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($judul ?? "Inventaris Alat") ?> - SIMRS</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/images/favicon.ico">

  <!-- Local Third-Party Libraries (Spark Admin) -->
  <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/libs/bootstrap-icons/bootstrap-icons.css">

  <!-- Main Design System & Custom Stylesheet -->
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

  <!-- ==========================================
       START: Sidebar Component
       ========================================== -->
  <div class="sidebar-wrapper" id="sidebar">
    <!-- Brand Logo / Identity -->
    <a href="index.php" class="sidebar-brand">
      <i class="bi bi-heart-pulse-fill"></i>
      <span>Inventaris RS</span>
    </a>

    <!-- Navigation Menu -->
    <div class="flex-grow-1 overflow-y-auto">
      <!-- Group: Menu Utama -->
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Menu Utama</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="index.php" class="sidebar-menu-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>" id="menu-dashboard" title="Dashboard">
              <i class="bi bi-grid-fill"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="add.php" class="sidebar-menu-link <?= (basename($_SERVER['PHP_SELF']) == 'add.php') ? 'active' : '' ?>" id="menu-add" title="Tambah Alat">
              <i class="bi bi-plus-circle-fill"></i>
              <span>Tambah Alat</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="log_akses.php" class="sidebar-menu-link <?= (basename($_SERVER['PHP_SELF']) == 'log_akses.php') ? 'active' : '' ?>" id="menu-logs" title="Log Akses">
              <i class="bi bi-clock-history"></i>
              <span>Log Akses</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Group: Autentikasi -->
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Autentikasi & Akun</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="login.php" class="sidebar-menu-link <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>" id="menu-signin" title="Sign In">
              <i class="bi bi-box-arrow-in-right"></i>
              <span>Sign In</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="register.php" class="sidebar-menu-link <?= (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : '' ?>" id="menu-register" title="Register">
              <i class="bi bi-person-plus-fill"></i>
              <span>Register</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="logout.php" class="sidebar-menu-link text-danger" id="menu-logout" title="Log Out"
               onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
              <i class="bi bi-box-arrow-right text-danger"></i>
              <span>Log Out</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Sidebar Profile Card (Dynamic Footer) -->
    <div class="sidebar-profile">
      <img src="assets/images/avatar.png" alt="Petugas" class="sidebar-profile-img">
      <div class="sidebar-profile-info">
        <div class="sidebar-profile-name"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Petugas RS') ?></div>
        <div class="sidebar-profile-email"><?= htmlspecialchars($_SESSION['role'] ?? $_SESSION['username'] ?? 'Admin') ?></div>
      </div>
      <a href="logout.php" class="text-danger ms-auto p-1" title="Log Out"
         onclick="return confirm('Apakah Anda yakin ingin log out?')">
        <i class="bi bi-power fs-5"></i>
      </a>
    </div>
  </div>
  <!-- END: Sidebar Component -->

  <!-- ==========================================
       START: Main Content Area
       ========================================== -->
  <div class="main-wrapper">

    <!-- START: Top Navbar Component -->
    <header class="navbar-custom">
      <div class="navbar-left">
        <!-- Desktop sidebar toggle -->
        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
          id="desktop-sidebar-toggle" aria-label="Minimize Sidebar">
          <i class="bi bi-chevron-bar-left"></i>
        </button>
        <!-- Mobile sidebar toggle -->
        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Toggle Navigation">
          <i class="bi bi-list"></i>
        </button>

        <!-- Quick Actions Button -->
        <a href="add.php" class="btn-quick-action d-none d-sm-inline-flex text-decoration-none">
          <i class="bi bi-plus-lg"></i>
          <span>Tambah Alat</span>
        </a>
      </div>

      <!-- Right actions -->
      <div class="navbar-actions">
        <!-- Fullscreen Toggle -->
        <button class="navbar-action-btn me-1" aria-label="Toggle Fullscreen" id="btn-fullscreen">
          <i class="bi bi-arrows-fullscreen"></i>
        </button>

        <!-- Profile Dropdown -->
        <div class="dropdown ms-2">
          <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false" id="profile-dropdown">
            <img src="assets/images/avatar.png" alt="Profile" class="navbar-profile-img">
            <span class="navbar-profile-name d-none d-md-inline"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Petugas RS') ?></span>
            <i class="bi bi-chevron-down navbar-profile-caret"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="profile-dropdown">
            <li class="dropdown-header">Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>!</li>
            <li><span class="dropdown-item-text text-muted small"><i class="bi bi-person-badge me-2"></i><?= htmlspecialchars($_SESSION['role'] ?? 'Petugas') ?></span></li>
            <li><a class="dropdown-item" href="log_akses.php"><i class="bi bi-clock-history me-2"></i>Riwayat Log</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin log out?')"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
          </ul>
        </div>

        <!-- Tombol Log Out Utama di Navbar -->
        <a href="logout.php" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 ms-2 px-3 fw-medium"
           onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" title="Log Out">
          <i class="bi bi-box-arrow-right"></i>
          <span class="d-none d-sm-inline">Log Out</span>
        </a>
      </div>
    </header>
    <!-- END: Top Navbar Component -->

    <!-- Page Content Container -->
    <div class="p-4 flex-grow-1">
