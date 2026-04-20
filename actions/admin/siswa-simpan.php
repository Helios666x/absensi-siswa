<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    redirect(BASE_URL . 'index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nis = $mysqli->real_escape_string(trim($_POST['nis']));
    $nama = $mysqli->real_escape_string(trim($_POST['nama']));
    $id_kelas = (int)$_POST['id_kelas'];
    $jenis_kelamin = $mysqli->real_escape_string(trim($_POST['jenis_kelamin']));

    if (empty($id)) {
        // === MODE: TAMBAH BARU ===
        $querySiswa = "INSERT INTO siswa (nis, nama, id_kelas, jenis_kelamin) VALUES (?, ?, ?, ?)";
        $stmtSiswa = $mysqli->prepare($querySiswa);
        $stmtSiswa->bind_param("ssis", $nis, $nama, $id_kelas, $jenis_kelamin);
        
        if ($stmtSiswa->execute()) {
            $siswa_id = $mysqli->insert_id;
            
            // Buat Akun Login
            $username = "siswa_" . $nis;
            $password_plain = "siswa123";
            $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);
            
            $queryUser = "INSERT INTO users (username, password, role, related_id) VALUES (?, ?, 'siswa', ?)";
            $stmtUser = $mysqli->prepare($queryUser);
            $stmtUser->bind_param("ssi", $username, $password_hash, $siswa_id);
            
            if ($stmtUser->execute()) {
                set_flashdata('success', 'Data siswa berhasil disimpan.');
                $info = "<strong>Username:</strong> $username <br><strong>Password:</strong> $password_plain";
                set_flashdata('info_akun', $info);
            } else {
                $mysqli->query("DELETE FROM siswa WHERE id = $siswa_id");
                set_flashdata('error', 'Gagal membuat akun login: ' . $mysqli->error);
            }
        } else {
            set_flashdata('error', 'Gagal menyimpan data siswa. Kemungkinan NIS sudah terdaftar.');
        }

    } else {
        // === MODE: EDIT ===
        $querySiswa = "UPDATE siswa SET nis = ?, nama = ?, id_kelas = ?, jenis_kelamin = ? WHERE id = ?";
        $stmtSiswa = $mysqli->prepare($querySiswa);
        $stmtSiswa->bind_param("ssisi", $nis, $nama, $id_kelas, $jenis_kelamin, $id);
        
        if ($stmtSiswa->execute()) {
            $username_baru = "siswa_" . $nis;
            $mysqli->query("UPDATE users SET username = '$username_baru' WHERE related_id = $id AND role = 'siswa'");
            set_flashdata('success', 'Data siswa berhasil diperbarui.');
        } else {
            set_flashdata('error', 'Gagal memperbarui data siswa.');
        }
    }
    
    redirect(BASE_URL . 'admin/master-siswa.php');
} else {
    redirect(BASE_URL . 'admin/master-siswa.php');
}