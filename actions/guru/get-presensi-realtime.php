<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (isset($_GET['jadwal_id'])) {
    $jadwal_id = (int)$_GET['jadwal_id'];

    // Ambil data absensi yang metodenya fingerprint ATAU manual untuk jadwal ini
    $query = "SELECT s.nis, s.nama, a.waktu_input AS waktu, a.metode 
              FROM absensi a 
              JOIN siswa s ON a.user_id = s.id 
              WHERE a.jadwal_id = $jadwal_id AND a.tipe_user = 'siswa'
              ORDER BY a.waktu_input DESC";
              
    $result = $mysqli->query($query);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode($data);
}