<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/helper.php';

// Kirim flash message DULU sebelum session dihancurkan
set_flashdata('success', 'Anda telah berhasil logout.');

session_unset();
session_destroy();

redirect(BASE_URL . 'login.php');