<?php
include "config.php";

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($koneksi, "DELETE FROM alat WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: index.php");
exit;