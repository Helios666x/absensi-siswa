<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') redirect(BASE_URL . 'index.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Jika jadwal ini sedang di-use oleh device (sesi aktif), batalkan sesi device tersebut
    $mysqli->query("UPDATE device SET sesi_jadwal_id = NULL WHERE sesi_jadwal_id = $id");
    
    $hapus = $mysqli->query("DELETE FROM jadwal WHERE id = $id");
    
    if ($hapus) {
        set_flashdata('success', 'Jadwal berhasil dihapus.');
    } else {
        set_flashdata('error', 'Gagal menghapus jadwal.');
    }
}
redirect(BASE_URL . 'admin/master-jadwal.php');