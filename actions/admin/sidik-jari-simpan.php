<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') redirect(BASE_URL . 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipe_user = $mysqli->real_escape_string($_POST['tipe_user']);
    $user_id = (int)$_POST['user_id'];
    $fingerprint_id = (int)$_POST['fingerprint_id'];

    // Validasi range ID Sensor
    if ($fingerprint_id < 0 || $fingerprint_id > 162) {
        set_flashdata('error', 'ID Sensor harus berada di antara 0 hingga 162.');
        redirect(BASE_URL . 'admin/master-sidik-jari.php');
    }

    // Cek apakah ID Sensor sudah dipakai orang lain
    $cekSlot = $mysqli->query("SELECT id FROM sidik_jari WHERE fingerprint_id = $fingerprint_id AND status = 'active'");
    if ($cekSlot->num_rows > 0) {
        set_flashdata('error', "Slot ID $fingerprint_id sudah digunakan oleh orang lain!");
        redirect(BASE_URL . 'admin/master-sidik-jari.php');
    }

    // Cek apakah user ini sudah punya sidik jari
    $cekUser = $mysqli->query("SELECT id FROM sidik_jari WHERE tipe_user = '$tipe_user' AND user_id = $user_id AND status = 'active'");
    if ($cekUser->num_rows > 0) {
        set_flashdata('error', 'User ini sudah terdaftar memiliki sidik jari!');
        redirect(BASE_URL . 'admin/master-sidik-jari.php');
    }

    // Simpan
    $query = "INSERT INTO sidik_jari (tipe_user, user_id, fingerprint_id) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sii", $tipe_user, $user_id, $fingerprint_id);

    if ($stmt->execute()) {
        set_flashdata('success', "Sidik jari berhasil didaftarkan di Slot ID $fingerprint_id.");
    } else {
        set_flashdata('error', 'Gagal mendaftarkan sidik jari.');
    }
    redirect(BASE_URL . 'admin/master-sidik-jari.php');
} else {
    redirect(BASE_URL . 'admin/master-sidik-jari.php');
}