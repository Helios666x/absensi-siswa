<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device_id = (int)$_POST['device_id'];
    
    // Set NULL untuk menghentikan sesi
    $mysqli->query("UPDATE device SET sesi_jadwal_id = NULL WHERE id = $device_id");
}