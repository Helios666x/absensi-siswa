<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    redirect(BASE_URL . 'index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nip = $mysqli->real_escape_string(trim($_POST['nip']));
    $nama = $mysqli->real_escape_string(trim($_POST['nama']));
    $mapel = $mysqli->real_escape_string(trim($_POST['mapel']));
    $no_hp = $mysqli->real_escape_string(trim($_POST['no_hp']));

    if (empty($id)) {
        // === MODE: TAMBAH BARU ===
        
        // 1. Insert ke tabel guru
        $queryGuru = "INSERT INTO guru (nip, nama, mapel, no_hp) VALUES (?, ?, ?, ?)";
        $stmtGuru = $mysqli->prepare($queryGuru);
        $stmtGuru->bind_param("ssss", $nip, $nama, $mapel, $no_hp);
        
        if ($stmtGuru->execute()) {
            $guru_id = $mysqli->insert_id; // Ambil ID guru yang baru saja dibuat
            
            // 2. Buat Akun Login di tabel users
            $username = "guru_" . $nip;
            $password_plain = "guru123"; // Password default
            $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);
            
            $queryUser = "INSERT INTO users (username, password, role, related_id) VALUES (?, ?, 'guru', ?)";
            $stmtUser = $mysqli->prepare($queryUser);
            $stmtUser->bind_param("ssi", $username, $password_hash, $guru_id);
            
            if ($stmtUser->execute()) {
                set_flashdata('success', 'Data guru berhasil disimpan.');
                // Kirim info akun lewat flashdata agar admin bisa memberitahu guru
                $info = "<strong>Username:</strong> $username <br><strong>Password:</strong> $password_plain";
                set_flashdata('info_akun', $info);
            } else {
                // Jika akun gagal dibuat, hapus data guru yang baru saja diinsert
                $mysqli->query("DELETE FROM guru WHERE id = $guru_id");
                set_flashdata('error', 'Gagal membuat akun login: ' . $mysqli->error);
            }
        } else {
            set_flashdata('error', 'Gagal menyimpan data guru: ' . $mysqli->error);
        }

    } else {
        // === MODE: EDIT ===
        $queryGuru = "UPDATE guru SET nip = ?, nama = ?, mapel = ?, no_hp = ? WHERE id = ?";
        $stmtGuru = $mysqli->prepare($queryGuru);
        $stmtGuru->bind_param("ssssi", $nip, $nama, $mapel, $no_hp, $id);
        
        if ($stmtGuru->execute()) {
            // Update username di tabel users jika NIP berubah
            $username_baru = "guru_" . $nip;
            $mysqli->query("UPDATE users SET username = '$username_baru' WHERE related_id = $id AND role = 'guru'");
            
            set_flashdata('success', 'Data guru berhasil diperbarui.');
        } else {
            set_flashdata('error', 'Gagal memperbarui data guru.');
        }
    }
    
    redirect(BASE_URL . 'admin/master-guru.php');
} else {
    redirect(BASE_URL . 'admin/master-guru.php');
}