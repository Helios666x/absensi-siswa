<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php'; // <- TAMBAHKAN BARIS INI
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

if (!isset($_SESSION['role'])) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

switch ($_SESSION['role']) {
    case ROLE_ADMIN:
        redirect(BASE_URL . 'dashboards/admin.php');
        break;
    case ROLE_GURU:
        redirect(BASE_URL . 'dashboards/guru.php');
        break;
    case ROLE_SISWA:
        redirect(BASE_URL . 'dashboards/siswa.php');
        break;
    default:
        redirect(BASE_URL . 'index.php');
}