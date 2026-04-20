<?php
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (isset($_GET['tipe'])) {
    $tipe = $mysqli->real_escape_string($_GET['tipe']);
    $kelas = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;

    $data = [];
    
    if ($tipe == 'guru') {
        $q = $mysqli->query("SELECT id, nip AS nomor_induk, nama FROM guru WHERE status_aktif = 1 ORDER BY nama");
    } else {
        $filterKelas = $kelas > 0 ? " AND id_kelas = $kelas" : "";
        $q = $mysqli->query("SELECT id, nis AS nomor_induk, nama FROM siswa WHERE status_aktif = 1 $filterKelas ORDER BY nama");
    }

    while ($row = $q->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode($data);
}