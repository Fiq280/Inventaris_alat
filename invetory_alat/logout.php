<?php
include "config.php";

if (isset($_SESSION['user_id'])) {
    catat_log($koneksi, $_SESSION['user_id'], $_SESSION['username'], $_SESSION['nama_lengkap'], "Logout");
}

session_unset();
session_destroy();

header("Location: login.php");
exit;