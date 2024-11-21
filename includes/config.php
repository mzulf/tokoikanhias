<?php
$servername = "localhost";  // Alamat server database
$username = "root";         // Username database
$password = "";             // Password database
$dbname = "zlfg_store";  // Nama database

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
