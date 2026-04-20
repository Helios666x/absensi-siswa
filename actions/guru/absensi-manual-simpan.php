<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jadwal_id = (int)$_POST['manualJadwalId'];
    $siswa_id = (int)$_POST['manualSiswaId'];
    $status = $mysqli->real_escape_string($_POST['manualStatus']);
    $keterangan = $mysqli->real_escape_string($_POST['manualKet']);

    $waktu_input = date('Y-m-d H:i:s');

    $query = "INSERT INTO absensi (tipe_user, user_id, jadwal_id, jenis_absensi, status, keterangan, waktu_input, metode) 
              VALUES ('siswa', $siswa_id, $jadwal_id, 'kelas', '$status', '$keterangan', '$waktu_input', 'manual')";
              
    if ($mysqli->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $mysqli->error]);
    }
}