<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (isset($_GET['jadwal_id'])) {
    $jadwal_id = (int)$_GET['jadwal_id'];

    // Ambil kelas_id dari jadwal
    $qJadwal = $mysqli->query("SELECT kelas_id FROM jadwal WHERE id = $jadwal_id");
    $jadwal = $qJadwal->fetch_assoc();
    $kelas_id = $jadwal['kelas_id'];

    // Ambil siswa yang ID-nya TIDAK ADA di tabel absensi untuk jadwal ini
    $query = "SELECT s.id, s.nis, s.nama 
              FROM siswa s 
              WHERE s.id_kelas = $kelas_id AND s.status_aktif = 1
              AND s.id NOT IN (
                  SELECT user_id FROM absensi WHERE jadwal_id = $jadwal_id AND tipe_user = 'siswa'
              )
              ORDER BY s.nama ASC";
              
    $result = $mysqli->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode($data);
}