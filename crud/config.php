<?php
$koneksi = mysqli_connect("localhost", "root", "", "simrs");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}