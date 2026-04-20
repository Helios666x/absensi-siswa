<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') redirect(BASE_URL . 'index.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Alih-alih menghapus, kita update statusnya menjadi 'inactive' 
    // agar histori log absensi tidak rusak/terhapus
    $update = $mysqli->query("UPDATE sidik_jari SET status = 'inactive' WHERE id = $id");
    
    if ($update) {
        set_flashdata('success', 'Pemetaan sidik jari berhasil dihapus (dinonaktifkan).');
    } else {
        set_flashdata('error', 'Gagal menghapus sidik jari.');
    }
}
redirect(BASE_URL . 'admin/master-sidik-jari.php');