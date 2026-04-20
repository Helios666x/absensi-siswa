<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') redirect(BASE_URL . 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $hari = $mysqli->real_escape_string($_POST['hari']);
    $kelas_id = (int)$_POST['kelas_id'];
    $guru_id = (int)$_POST['guru_id'];
    $jam_mulai = $mysqli->real_escape_string($_POST['jam_mulai']);
    $jam_selesai = $mysqli->real_escape_string($_POST['jam_selesai']);
    $nama_kegiatan = $mysqli->real_escape_string($_POST['nama_kegiatan']);

    if (empty($id)) {
        $query = "INSERT INTO jadwal (kelas_id, guru_id, hari, jam_mulai, jam_selesai, nama_kegiatan) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("iissss", $kelas_id, $guru_id, $hari, $jam_mulai, $jam_selesai, $nama_kegiatan);
    } else {
        $query = "UPDATE jadwal SET kelas_id = ?, guru_id = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, nama_kegiatan = ? 
                  WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("iissssi", $kelas_id, $guru_id, $hari, $jam_mulai, $jam_selesai, $nama_kegiatan, $id);
    }

    if ($stmt->execute()) {
        set_flashdata('success', 'Jadwal berhasil disimpan.');
    } else {
        set_flashdata('error', 'Gagal menyimpan jadwal.');
    }
    redirect(BASE_URL . 'admin/master-jadwal.php');
} else {
    redirect(BASE_URL . 'admin/master-jadwal.php');
}