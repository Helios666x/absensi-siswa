<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    redirect(BASE_URL . 'index.php');
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah siswa ini sudah terdaftar sidik jarinya di sensor
    $cekSidikJari = $mysqli->query("SELECT id FROM sidik_jari WHERE user_id = $id AND tipe_user = 'siswa'");
    
    if ($cekSidikJari->num_rows > 0) {
        set_flashdata('error', 'Gagal menghapus! Siswa ini masih memiliki data sidik jari terdaftar di sensor. Harap hapus sidik jarinya terlebih dahulu.');
    } else {
        // 1. Hapus akun login
        $mysqli->query("DELETE FROM users WHERE related_id = $id AND role = 'siswa'");
        
        // 2. Hapus data siswa
        $hapusSiswa = $mysqli->query("DELETE FROM siswa WHERE id = $id");
        
        if ($hapusSiswa) {
            set_flashdata('success', 'Data siswa dan akun login-nya berhasil dihapus.');
        } else {
            set_flashdata('error', 'Gagal menghapus data siswa.');
        }
    }
}

redirect(BASE_URL . 'admin/master-siswa.php');