<?php
require_once __DIR__ . '/app.php';

 $host = 'localhost';
 $user = 'root'; // Default XAMPP
 $pass = '';     // Default XAMPP kosong
 $db   = 'db_absensi';

 $mysqli = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}