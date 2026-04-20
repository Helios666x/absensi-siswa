<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    redirect(BASE_URL . 'index.php');
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah kelas ini masih memiliki siswa
    $cekSiswa = $mysqli->query("SELECT id FROM siswa WHERE id_kelas = $id");
    
    if ($cekSiswa->num_rows > 0) {
        set_flashdata('error', 'Gagal menghapus! Kelas ini masih memiliki data siswa.');
    } else {
        // Jika aman, hapus kelas
        $hapus = $mysqli->query("DELETE FROM kelas WHERE id = $id");
        if ($hapus) {
            set_flashdata('success', 'Data kelas berhasil dihapus.');
        } else {
            set_flashdata('error', 'Gagal menghapus data kelas.');
        }
    }
}

redirect(BASE_URL . 'admin/master-kelas.php');