<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';

function check_login() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        set_flashdata('error', 'Anda harus login terlebih dahulu.');
        redirect(BASE_URL . 'index.php');
    }
}

function check_role($allowed_roles) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        set_flashdata('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        redirect(BASE_URL . 'dashboards/router.php');
    }
}