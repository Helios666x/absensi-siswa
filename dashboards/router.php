<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';

// Kalau role tidak ada, hentikan paksa. Jangan berputar.
if (!isset($_SESSION['role'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header("Location: " . BASE_URL . "dashboards/admin.php");
    exit;
} else {
    // Sementara, role lain juga dilempar ke admin (karena cuma punya akun admin sekarang)
    header("Location: " . BASE_URL . "login.php");
    exit;
}