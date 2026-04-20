<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') redirect(BASE_URL . 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nama = $mysqli->real_escape_string(trim($_POST['nama_device']));
    $lokasi = $mysqli->real_escape_string(trim($_POST['lokasi']));

    if (empty($id)) {
        // Generate API Key acak yang ampan (panjang 40 karakter)
        $api_key = bin2hex(random_bytes(20)); 
        
        $query = "INSERT INTO device (nama_device, lokasi, api_key) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sss", $nama, $lokasi, $api_key);
        
        if ($stmt->execute()) {
            set_flashdata('success', 'Device berhasil ditambahkan.');
            set_flashdata('info_akun', $api_key); // Kirim key agar ditampilkan 1x
        } else {
            set_flashdata('error', 'Gagal menambahkan device.');
        }
    } else {
        $query = "UPDATE device SET nama_device = ?, lokasi = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssi", $nama, $lokasi, $id);
        
        if ($stmt->execute()) {
            set_flashdata('success', 'Data device berhasil diperbarui.');
        } else {
            set_flashdata('error', 'Gagal memperbarui device.');
        }
    }
    redirect(BASE_URL . 'admin/master-perangkat.php');
} else {
    redirect(BASE_URL . 'admin/master-perangkat.php');
}