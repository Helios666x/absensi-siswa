<?php
// Mulai session
session_start();

// Setting zona waktu
date_default_timezone_set('Asia/Jakarta');

// Base URL (Sesuaikan dengan nama folder kamu)
// Jika pakai folder "absensi-siswa" di localhost:
define('BASE_URL', 'http://localhost/absensi-siswa/');

// Path folder
define('BASE_PATH', __DIR__ . '/..');