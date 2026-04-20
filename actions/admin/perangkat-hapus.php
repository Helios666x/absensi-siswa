<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') redirect(BASE_URL . 'index.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $hapus = $mysqli->query("DELETE FROM device WHERE id = $id");
    
    if ($hapus) {
        set_flashdata('success', 'Device berhasil dihapus.');
    } else {
        set_flashdata('error', 'Gagal menghapus device.');
    }
}
redirect(BASE_URL . 'admin/master-perangkat.php');