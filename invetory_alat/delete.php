<?php
include "auth_check.php";

// Hanya menerima permintaan dari tombol Hapus (POST), bukan dari mengetik alamat
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid($_POST['csrf'] ?? '')) {
    die("Permintaan tidak valid.");
}

$id = (int) ($_POST['id'] ?? 0);

// Ambil nama alat dulu supaya bisa dicatat di log
$stmt = mysqli_prepare($koneksi, "SELECT nama_alat FROM alat WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($data) {
    $del = mysqli_prepare($koneksi, "DELETE FROM alat WHERE id=?");
    mysqli_stmt_bind_param($del, "i", $id);
    mysqli_stmt_execute($del);

    catat_log($koneksi, $_SESSION['user_id'], $_SESSION['username'],
              $_SESSION['nama_lengkap'], mb_substr("Hapus Data: " . $data['nama_alat'], 0, 100));

    $_SESSION['pesan'] = "Alat berhasil dihapus.";
}

header("Location: index.php");
exit;