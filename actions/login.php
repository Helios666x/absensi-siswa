<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Cari user di database
    $query = "SELECT * FROM users WHERE username = ? AND status_aktif = 1";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['related_id'] = $user['related_id'];

            set_flashdata('success', 'Login berhasil! Selamat datang.');
            redirect(BASE_URL . 'dashboards/router.php');
        } else {
            set_flashdata('error', 'Password yang Anda masukkan salah.');
        }
    } else {
        set_flashdata('error', 'Username tidak ditemukan atau akun dinonaktifkan.');
    }
    redirect(BASE_URL . 'index.php');
} else {
    redirect(BASE_URL . 'index.php');
}