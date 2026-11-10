<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$koneksi = mysqli_connect("localhost", "root", "", "simrs");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($koneksi, "utf8mb4");

// Mencatat siapa melakukan apa ke tabel log_akses
function catat_log($koneksi, $user_id, $username, $nama, $aktivitas) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu)
         VALUES (?, ?, ?, ?, ?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, "isssss", $user_id, $username, $nama, $aktivitas, $ip, $ua);
    mysqli_stmt_execute($stmt);
}
// Daftar ruangan yang boleh dipilih
define('LOKASI_ALAT', ['ICU', 'UGD', 'OK', 'POLI', 'LAB', 'RAWAT']);

// Token pengaman supaya tombol hapus/simpan tidak bisa dipicu dari situs lain
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_valid($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}