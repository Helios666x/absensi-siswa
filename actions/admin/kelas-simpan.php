<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    redirect(BASE_URL . 'index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nama_kelas = $mysqli->real_escape_string(trim($_POST['nama_kelas']));
    $tingkat = $mysqli->real_escape_string(trim($_POST['tingkat']));
    $kategori = $mysqli->real_escape_string(trim($_POST['kategori']));
    $guru_id = !empty($_POST['guru_id']) ? (int)$_POST['guru_id'] : null;

    if (empty($id)) {
        // MODE: TAMBAH BARU
        $query = "INSERT INTO kelas (nama_kelas, tingkat, kategori, guru_id) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssi", $nama_kelas, $tingkat, $kategori, $guru_id);
    } else {
        // MODE: EDIT
        $query = "UPDATE kelas SET nama_kelas = ?, tingkat = ?, kategori = ?, guru_id = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssii", $nama_kelas, $tingkat, $kategori, $guru_id, $id);
    }

    if ($stmt->execute()) {
        set_flashdata('success', 'Data kelas berhasil disimpan.');
    } else {
        set_flashdata('error', 'Gagal menyimpan data kelas: ' . $mysqli->error);
    }
    
    redirect(BASE_URL . 'admin/master-kelas.php');
} else {
    redirect(BASE_URL . 'admin/master-kelas.php');
}