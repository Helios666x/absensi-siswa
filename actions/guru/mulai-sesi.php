<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

// Pastikan guru yang login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jadwal_id = (int)$_POST['jadwal_id'];
    $device_id = (int)$_POST['device_id'];
    $guru_id = $_SESSION['related_id'];

    // Validasi: Cek apakah jadwal ini benar milik guru yang login
    $cekJadwal = $mysqli->query("SELECT id FROM jadwal WHERE id = $jadwal_id AND guru_id = $guru_id");
    if ($cekJadwal->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Jadwal bukan milik Anda']);
        exit;
    }

    // Validasi: Cek apakah device sudah dipakai sesi lain
    $cekDevice = $mysqli->query("SELECT sesi_jadwal_id FROM device WHERE id = $device_id");
    $dev = $cekDevice->fetch_assoc();
    if ($dev['sesi_jadwal_id'] != NULL) {
        echo json_encode(['success' => false, 'message' => 'Device ini sedang dipakai sesi lain!']);
        exit;
    }

    // Update device, set sesi aktif
    $update = $mysqli->query("UPDATE device SET sesi_jadwal_id = $jadwal_id WHERE id = $device_id");
    
    if ($update) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update database']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}