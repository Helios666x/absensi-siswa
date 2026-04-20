<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    redirect(BASE_URL . 'index.php');
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah guru ini adalah wali kelas
    $cekWaliKelas = $mysqli->query("SELECT id FROM kelas WHERE guru_id = $id");
    
    if ($cekWaliKelas->num_rows > 0) {
        set_flashdata('error', 'Gagal menghapus! Guru ini masih menjabat sebagai Wali Kelas. Silakan lepaskan status Wali Kelas terlebih dahulu.');
    } else {
        // 1. Hapus akun login terkait di tabel users
        $hapusUser = $mysqli->query("DELETE FROM users WHERE related_id = $id AND role = 'guru'");
        
        // 2. Hapus data guru
        $hapusGuru = $mysqli->query("DELETE FROM guru WHERE id = $id");
        
        if ($hapusGuru) {
            set_flashdata('success', 'Data guru dan akun login-nya berhasil dihapus.');
        } else {
            set_flashdata('error', 'Gagal menghapus data guru.');
        }
    }
}

redirect(BASE_URL . 'admin/master-guru.php');